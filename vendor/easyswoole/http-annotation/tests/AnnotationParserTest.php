<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\HttpAnnotation\Annotation\MethodAnnotation;
use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;
use EasySwoole\HttpAnnotation\Annotation\Parser;
use EasySwoole\HttpAnnotation\Annotation\PropertyAnnotation;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiFail;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiFailParam;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiRequestExample;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiSuccess;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiSuccessParam;
use EasySwoole\HttpAnnotation\AnnotationTag\CircuitBreaker;
use EasySwoole\HttpAnnotation\AnnotationTag\Context;
use EasySwoole\HttpAnnotation\AnnotationTag\Controller;
use EasySwoole\HttpAnnotation\AnnotationTag\Di;
use EasySwoole\HttpAnnotation\AnnotationTag\Inject;
use EasySwoole\HttpAnnotation\AnnotationTag\InjectParamsContext;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\HttpAnnotation\Tests\TestController\Annotation;
use EasySwoole\HttpAnnotation\Tests\TestController\RouterPath;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestResult;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup as ApiGroupTag;

class AnnotationParserTest extends TestCase
{
    /**
     * @var ObjectAnnotation
     */
    protected $apiGroup;
    /** @var ObjectAnnotation */
    protected $resultB;

    function run(TestResult $result = null): TestResult
    {
        $parse = new Parser();
        $this->apiGroup = $parse->parseObject(new \ReflectionClass(Annotation::class));
        return parent::run($result);
    }

    function testApiGroup()
    {
        $this->assertInstanceOf(ApiGroupTag::class, $this->apiGroup->getApiGroupTag());
        $this->assertEquals('GroupA', $this->apiGroup->getApiGroupTag()->groupName);
    }

    function testApiGroupDescription()
    {
        $this->assertInstanceOf(ApiGroupDescription::class, $this->apiGroup->getApiGroupDescriptionTag());
        $this->assertEquals('GroupA desc', $this->apiGroup->getApiGroupDescriptionTag()->value);
    }

    function testApiGroupAuth()
    {
        $this->assertIsArray($this->apiGroup->getGroupAuthTag());
        $this->assertEquals(2, count($this->apiGroup->getGroupAuthTag()));
        $this->assertEquals('groupParamA', $this->apiGroup->getGroupAuthTag('groupParamA')->name);
        $this->assertEquals('groupParamB', $this->apiGroup->getGroupAuthTag('groupParamB')->name);
    }

    function testAnnotationMethod()
    {
        $this->assertEquals(null, $this->apiGroup->getMethod('noneFunc'));
        $this->assertInstanceOf(MethodAnnotation::class, $this->apiGroup->getMethod('func'));
        $this->assertEquals('func', $this->apiGroup->getMethod('func')->getMethodName());
    }

    function testApi()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertInstanceOf(Api::class, $func->getApiTag());
        $this->assertEquals('func', $func->getApiTag()->name);
        $this->assertEquals('/apiGroup/func', $func->getApiTag()->path);
    }

    function testApiAuth()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertEquals(2, count($func->getApiAuth()));
        $this->assertInstanceOf(ApiAuth::class, $func->getApiAuth('apiAuth1'));
    }

    function testApiDescription()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertInstanceOf(ApiDescription::class, $func->getApiDescriptionTag());
        $this->assertEquals('func desc', $func->getApiDescriptionTag()->value);
    }

    function testApiFail()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertEquals(2, count($func->getApiFail()));
        $this->assertInstanceOf(ApiFail::class, $func->getApiFail()[0]);
        $this->assertEquals('func fail example1', $func->getApiFail()[0]->value);
    }

    function testApiFailParam()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertEquals(2, count($func->getApiFailParam()));
        $this->assertInstanceOf(ApiFailParam::class, $func->getApiFailParam('failParam1'));
        $this->assertEquals('failParam1', $func->getApiFailParam('failParam1')->name);
    }

    function testApiRequestExample()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertEquals(2, count($func->getApiRequestExample()));
        $this->assertInstanceOf(ApiRequestExample::class, $func->getApiRequestExample()[0]);
        $this->assertEquals('func request example1', $func->getApiRequestExample()[0]->value);
    }


    function testApiSuccess()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertEquals(2, count($func->getApiSuccess()));
        $this->assertInstanceOf(ApiSuccess::class, $func->getApiSuccess()[0]);
        $this->assertEquals('func success example1', $func->getApiSuccess()[0]->value);
    }


    function testApiSuccessParam()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertEquals(2, count($func->getApiSuccessParam()));
        $this->assertInstanceOf(ApiSuccessParam::class, $func->getApiSuccessParam('successParam1'));
        $this->assertEquals('successParam1', $func->getApiSuccessParam('successParam1')->name);
    }

    function testCircuitBreaker()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertInstanceOf(CircuitBreaker::class, $func->getCircuitBreakerTag());
        $this->assertEquals(5.0, $func->getCircuitBreakerTag()->timeout);
    }

    function testInjectParamsContext()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertInstanceOf(InjectParamsContext::class, $func->getInjectParamsContextTag());
        $this->assertEquals('requestData', $func->getInjectParamsContextTag()->key);
    }

    function testMethod()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertInstanceOf(Method::class, $func->getMethodTag());
        $this->assertEquals(['POST', 'GET'], $func->getMethodTag()->allow);
    }

    function testParam()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertEquals(2, count($func->getParamTag()));
        $this->assertInstanceOf(Param::class, $func->getParamTag('param1'));
        $this->assertEquals('param1', $func->getParamTag('param1')->name);
    }

    function testProperty()
    {
        /** @var PropertyAnnotation $di */
        $di = $this->apiGroup->getProperty('di');
        $this->assertInstanceOf(PropertyAnnotation::class, $di);
        $this->assertEquals('di', $di->getName());

        /** @var PropertyAnnotation $context */
        $context = $this->apiGroup->getProperty('context');
        $this->assertInstanceOf(PropertyAnnotation::class, $context);
        $this->assertEquals('context', $context->getName());
    }

    function testDi()
    {
        /** @var PropertyAnnotation $di */
        $di = $this->apiGroup->getProperty('di');
        $this->assertInstanceOf(Di::class, $di->getDiTag());
        $this->assertEquals('di', $di->getDiTag()->key);
    }

    function testContext()
    {
        /** @var PropertyAnnotation $context */
        $context = $this->apiGroup->getProperty('context');
        $this->assertInstanceOf(Context::class, $context->getContextTag());
        $this->assertEquals('context', $context->getContextTag()->key);
    }

    function testInject()
    {
        /** @var PropertyAnnotation $inject */
        $inject = $this->apiGroup->getProperty('inject');
        $this->assertInstanceOf(Inject::class, $inject->getInjectTag());
        $this->assertEquals('\EasySwoole\HttpAnnotation\Tests\TestController\Test', $inject->getInjectTag()->className);
        $this->assertEquals([1, [1, 2]], $inject->getInjectTag()->args);
    }

    function testPrefix()
    {
        $parse = new Parser();
        $objAnnotation = $parse->parseObject(new \ReflectionClass(RouterPath::class));
        $this->assertInstanceOf(Controller::class, $objAnnotation->getController());
        $this->assertEquals('/Router', $objAnnotation->getController()->prefix);
    }
}
