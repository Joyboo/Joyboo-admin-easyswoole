<?php


namespace EasySwoole\HttpAnnotation\Utility;


use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Http\UrlParser;
use EasySwoole\HttpAnnotation\Annotation\MethodAnnotation;
use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;
use EasySwoole\HttpAnnotation\Annotation\Parser;
use EasySwoole\HttpAnnotation\Annotation\ParserInterface;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\Controller;
use EasySwoole\Utility\File;
use FastRoute\RouteCollector;

class Scanner
{
    /** @var Parser|ParserInterface|null */
    protected $parser;

    function __construct(?ParserInterface $parser = null)
    {
        if (!$parser) {
            $parser = new Parser();
        }
        $this->parser = $parser;
    }

    /**
     * @return Parser|ParserInterface|null
     */
    public function getParser()
    {
        return $this->parser;
    }

    function getObjectAnnotation(string $class): ObjectAnnotation
    {
        $ref = new \ReflectionClass($class);
        return $this->parser->parseObject($ref);
    }

    function mappingRouter(RouteCollector $routeCollector, string $controllerPath, string $controllerNameSpace = 'App\\HttpController\\')
    {
        //用于psr规范去除命名空间
        $prefixLen = strlen(trim($controllerNameSpace, '\\'));
        $annotations = $this->scanAnnotations($controllerPath);
        /**
         * @var string $class
         * @var ObjectAnnotation $classAnnotation
         */
        foreach ($annotations as $class => $classAnnotation) {
            $controllerAnnotation = $classAnnotation->getController();
            /**
             * @var  $methodName
             * @var MethodAnnotation $method
             */
            foreach ($classAnnotation->getMethod() as $methodName => $method) {
                $apiTag = $method->getApiTag();
                if ($apiTag && !empty($apiTag->path)) {
                    $allow = $method->getMethodTag();
                    if (!empty($allow->allow)) {
                        $allow = $allow->allow;
                    } else {
                        $allow = ['POST', 'GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
                    }
                    if ($apiTag->deprecated !== true) {
                        $handler = '/' . substr($class, $prefixLen + 1) . '/' . $methodName;
                    } else {
                        $handler = function (Request $request, Response $response) {
                            $response->withStatus(Status::CODE_LOCKED);
                            return false;
                        };
                    }
                    $routeCollector->addRoute($allow, UrlParser::pathInfo(self::getRoutePath($controllerAnnotation, $apiTag)), $handler);
                }
            }
        }
    }

    function scanAnnotations(string $dirOrFile): array
    {
        $ret = [];
        $files = [];
        if (!is_dir($dirOrFile)) {
            $files[] = $dirOrFile;
        } else {
            $files = File::scanDirectory($dirOrFile)['files'];
        }
        foreach ($files as $file) {
            $fileExtension = pathinfo($file)['extension'] ?? '';

            if (!$fileExtension || $fileExtension !== 'php') {
                continue;
            }

            $class = static::getFileDeclaredClass($file);
            if (!$class) {
                continue;
            }

            $ret[$class] = $this->getObjectAnnotation($class);
        }
        return $ret;
    }

    public static function getRoutePath(?Controller $controller = null, ?Api $api = null): ?string
    {
        $prefix = $controller ? $controller->prefix : null;
        $path = $api ? $api->path : null;
        if (!$prefix && !$path) {
            return '';
        }

        if ($prefix && !$path) {
            return $prefix;
        }

        if (!$prefix && $path) {
            return $path;
        }

        if ($api->ignorePrefix === true) {
            return $path;
        }

        return $prefix . $path;
    }

    public static function getFileDeclaredClass(string $file): ?string
    {
        $namespace = '';
        $class = NULL;
        $phpCode = file_get_contents($file);
        $tokens = token_get_all($phpCode);
        for ($i = 0; $i < count($tokens); $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= '\\' . $tokens[$j][1];
                    } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }
            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j] === '{') {
                        $class = $tokens[$i + 2][1];
                        break;
                    }
                }
            } elseif ($class) {
                break;
            }
        }
        if (!empty($class)) {
            if (!empty($namespace)) {
                //去除第一个\
                $namespace = substr($namespace, 1);
            }
            return $namespace . '\\' . $class;
        } else {
            return null;
        }
    }
}
