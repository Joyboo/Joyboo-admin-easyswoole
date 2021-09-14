<?php


namespace EasySwoole\HttpAnnotation;


use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di as IOC;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\GlobalParam\Hook;
use EasySwoole\HttpAnnotation\Annotation\MethodAnnotation;
use EasySwoole\HttpAnnotation\Annotation\Parser;
use EasySwoole\HttpAnnotation\Annotation\ParserInterface;
use EasySwoole\HttpAnnotation\Annotation\PropertyAnnotation;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation\ActionTimeout;
use EasySwoole\HttpAnnotation\Exception\Annotation\MethodNotAllow;
use EasySwoole\HttpAnnotation\Exception\Annotation\ParamValidateError;
use EasySwoole\HttpAnnotation\Exception\Exception;
use EasySwoole\Session\Context;
use EasySwoole\Validate\Validate;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class AnnotationController extends Controller
{
    private $classAnnotation;

    public function __construct(?ParserInterface $parser = null)
    {
        parent::__construct();
        if($parser == null){
            $parser = new Parser();
        }
        $this->classAnnotation = $parser->parseObject(new \ReflectionClass(static::class));
    }

    protected function __exec()
    {
        /*
           执行成员属性解析
        */
        foreach ($this->classAnnotation->getProperty() as $name => $propertyAnnotation){
            /** @var $propertyAnnotation PropertyAnnotation */
            if($propertyAnnotation->getContextTag()){
                $contextKey = $propertyAnnotation->getContextTag()->key;
                $this->{$name} = ContextManager::getInstance()->get($contextKey);
            }else if($propertyAnnotation->getDiTag()){
                $key = $propertyAnnotation->getDiTag()->key;
                $this->{$name} = IOC::getInstance()->get($key);
            }else if($propertyAnnotation->getInjectTag()){
                $injectTag = $propertyAnnotation->getInjectTag();
                $this->{$name} = new $injectTag->className(...$injectTag->args);
            }
        }
        //执行
        $actionName = $this->getActionName();

        $forwardPath = null;
        try{
            //执行一次onRequest
            $this->__handleMethodAnnotation('onRequest');
            $ret = call_user_func([$this,'onRequest'],$actionName);
            if ($ret !== false) {
                $actionArgs = $this->__handleMethodAnnotation($actionName);
                $allowMethodReflections = $this->getAllowMethodReflections();
                if(isset($allowMethodReflections[$actionName])){
                    /** @var \ReflectionMethod $ref */
                    $ref = $this->getAllowMethodReflections()[$actionName];
                    $runArg = [];
                    foreach ($ref->getParameters() as $parameter){
                        $name = $parameter->getName();
                        if(isset($actionArgs[$name])){
                            $runArg[] = $actionArgs[$name];
                        }else{
                            $runArg[] = $this->request()->getRequestParam($name);
                        }
                    }
                    /** @var MethodAnnotation $methodAnnotation */
                    $methodAnnotation = $this->classAnnotation->getMethod($actionName);
                    if($methodAnnotation->getCircuitBreakerTag()){
                        $timeout = $methodAnnotation->getCircuitBreakerTag()->timeout;
                        $failAction = $methodAnnotation->getCircuitBreakerTag()->failAction;
                        $channel = new Channel(1);
                        Coroutine::create(function ()use($channel,$actionName,$runArg){
                            /*
                             * 因为协程内的异常需要被外层捕获
                             */
                            try{
                                $ret = $this->$actionName(...array_values($runArg));
                            }catch (\Throwable $exception){
                                $ret = $exception;
                            }
                            $channel->push($ret);
                        });
                        $ret = $channel->pop($timeout);
                        if($ret instanceof \Throwable){
                            throw $ret;
                        }
                        if($ret === false){
                            if($failAction){
                                $forwardPath = $this->$failAction();
                            }else{
                                throw new ActionTimeout("action:{$actionName} timeout");
                            }
                        }else{
                            $forwardPath = $ret;
                        }
                    }else{
                        $forwardPath = $this->$actionName(...array_values($runArg));
                    }
                }else{
                    $forwardPath = $this->actionNotFound($actionName);
                }
            }
        }catch (\Throwable $throwable){
            //若没有重构onException，直接抛出给上层
            $this->onException($throwable);
        } finally {
            try {
                $this->afterAction($actionName);
            } catch (\Throwable $throwable) {
                $this->onException($throwable);
            } finally {
                try {
                    $this->gc();
                } catch (\Throwable $throwable) {
                    $this->onException($throwable);
                }
            }
        }
        return $forwardPath;
    }

    protected function __handleMethodAnnotation(?string $methodName):array
    {
        $methodAnnotation = $this->classAnnotation->getMethod($methodName);
        if($methodAnnotation instanceof MethodAnnotation){
            //判断请求方法
            if($methodAnnotation->getMethodTag()){
                if(!in_array($this->request()->getMethod(),$methodAnnotation->getMethodTag()->allow)){
                    throw new MethodNotAllow("request method {$this->request()->getMethod()} is not allow for action {$methodName} in class ".(static::class) );
                }
            }
            //判断InjectParamsContext
            $injectKey = null;
            $filterNull = false;
            $filterEmpty = false;
            $onlyParamTag = true;
            $methodParams = [];
            if($methodAnnotation->getInjectParamsContextTag()){
                $injectKey = $methodAnnotation->getInjectParamsContextTag()->key;
                $filterNull = $methodAnnotation->getInjectParamsContextTag()->filterNull;
                $filterEmpty = $methodAnnotation->getInjectParamsContextTag()->filterEmpty;
                $onlyParamTag = $methodAnnotation->getInjectParamsContextTag()->onlyParamTag;
            }

            //处理需要校验的参数
            $allParamsData = [];
            $validate = new Validate();
            $validateParams = [];
            //先找全局的权限定义
            /** @var ApiGroupAuth $param */
            foreach ($this->classAnnotation->getGroupAuthTag() as $param){
                $validateParams[$param->name] = $param;
            }

            /** @var Param $param */
            foreach ($this->classAnnotation->getParamTag() as $param){
                $validateParams[$param->name] = $param;
            }
            //找出方法的apiAuth标签
            /** @var ApiAuth $param */
            foreach ($methodAnnotation->getApiAuth() as $param){
                $validateParams[$param->name] = $param;
            }
            //找出方法的param标签
            /** @var Param $param */
            foreach ($methodAnnotation->getParamTag() as $param){
                $validateParams[$param->name] = $param;
                $methodParams[$param->name] = true;
            }
            //进行校验
            $requestJson = null;
            /** @var Param $param */
            foreach ($validateParams as $param)
            {
                if ($param->deprecated === true) {
                    continue;
                }

                if($param instanceof ApiGroupAuth){
                    if(in_array($methodName,$param->ignoreAction)){
                        continue;
                    }
                }
                $paramName = $param->name;
                if(!empty($param->from)){
                    $value = null;
                    /*
                     * 按照允许的列表顺序进行取值
                     */
                    foreach ($param->from as $from){
                        switch ($from){
                            case "POST":{
                                $value = $this->request()->getParsedBody($paramName);
                                break;
                            }
                            case "GET":{
                                $value = $this->request()->getQueryParam($paramName);
                                break;
                            }
                            case "COOKIE":{
                                $value = $this->request()->getCookieParams($paramName);
                                break;
                            }
                            case 'HEADER':{
                                $value = $this->request()->getHeader($paramName);
                                if(!empty($value)){
                                    $value = $value[0];
                                }else{
                                    $value = null;
                                }
                                break;
                            }
                            case 'FILE':{
                                $value = $this->request()->getUploadedFile($paramName);
                                break;
                            }
                            case 'DI':{
                                $value = IOC::getInstance()->get($paramName);
                                break;
                            }
                            case 'CONTEXT':{
                                $value = ContextManager::getInstance()->get($paramName);
                                break;
                            }
                            case 'RAW':{
                                $value = $this->request()->getBody()->__toString();
                                break;
                            }
                            case 'JSON':{
                                if($requestJson === null){
                                    $requestJson = json_decode($this->request()->getBody()->__toString(),true);
                                    if(!is_array($requestJson)){
                                        $requestJson = [];
                                    }
                                }
                                if(isset($requestJson[$paramName])){
                                    $value = $requestJson[$paramName];
                                }
                                break;
                            }
                            case 'SESSION':{
                                $context = ContextManager::getInstance()->get(Hook::SESSION_CONTEXT);
                                if($context instanceof Context){
                                    $value = $context->get($paramName);
                                }
                                break;
                            }
                            case 'ROUTER_PARAMS':{
                                $context = ContextManager::getInstance()->get(AbstractRouter::PARSE_PARAMS_CONTEXT_KEY);
                                if(isset($context[$paramName])){
                                    $value = $context[$paramName];
                                }
                                break;
                            }
                        }
                        if($value !== null){
                            break;
                        }
                    }
                }else{
                    $value = $this->request()->getRequestParam($paramName);
                }

                if($value === null){
                    $value = $param->defaultValue;
                }

                if($value !== null){
                    $value = $param->typeCast($value);
                }
                //如果参数不为null,执行预处理，并设置进去参数
                if(!empty($param->preHandler) && $value !== null){
                    if(is_callable($param->preHandler)){
                        $value = call_user_func($param->preHandler,$value);
                    }else{
                        throw new Exception("annotation param: {$paramName} preHandler is not callable");
                    }
                }
                $allParamsData[$paramName] = $value;
                if(!empty($param->validateRuleList)){
                    foreach ($param->validateRuleList as $rule => $none){
                        $validateArgs = $param->{$rule};
                        if(!is_array($validateArgs)){
                            $validateArgs = [$validateArgs];
                        }
                        $validate->addColumn($param->name,$param->alias)->{$rule}(...$validateArgs);
                    }
                }
            }
            //执行校验
            //合并参数
            $data = $allParamsData + $this->request()->getRequestParam();
            if(!$validate->validate($data)){
                $ex = new ParamValidateError("validate fail for column {$validate->getError()->getField()}");
                $ex->setValidate($validate);
                throw $ex;
            }
            //仅仅返回所指定的注解参数
            if($injectKey){
                foreach ($allParamsData as $key => $arg){
                    if($onlyParamTag && (!isset($methodParams[$key]))){
                        unset($allParamsData[$key]);
                        continue;
                    }
                    if($filterNull && $arg === null){
                        unset($allParamsData[$key]);
                        continue;
                    }
                    if($filterEmpty && empty($allParamsData[$key])){
                        unset($allParamsData[$key]);
                    }
                }
                ContextManager::getInstance()->set($injectKey,$allParamsData);
            }

            return $allParamsData;
        }
        return [];
    }
}
