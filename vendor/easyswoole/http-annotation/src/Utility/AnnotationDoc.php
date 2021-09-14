<?php


namespace EasySwoole\HttpAnnotation\Utility;


use EasySwoole\HttpAnnotation\Annotation\MethodAnnotation;
use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;
use EasySwoole\HttpAnnotation\Annotation\ParserInterface;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\ParserDown\ParserDown;
use EasySwoole\Validate\Error;

class AnnotationDoc
{
    private $scanner;
    private $CLRF = "\n\n";
    private $projectName = 'API 接口文档';

    function __construct(?ParserInterface $parser = null)
    {
        $this->scanner = new Scanner($parser);
    }

    function setProjectName(string $name): AnnotationDoc
    {
        $this->projectName = $name;
        return $this;
    }

    function scan2Html(string $dirOrFile, ?string $extMd = null)
    {
        if ($extMd) {
            $md = new ParserDown();
            $extMd = $md->text($extMd);
        } else {
            $extMd = '';
        }
        $ret = file_get_contents(__DIR__ . "/docPage.tpl");
        $ret = str_replace('{{$extra}}', $extMd, $ret);

        $ret = str_replace('{{$projectName}}', $this->projectName, $ret);

        $info = $this->buildAnnotationHtml($dirOrFile);

        $navArr = $info['methodGroup'];
        ksort($navArr);
        // 处理导航
        $nav = '<ul>';
        foreach ($navArr as $fNavK => $fNavV) {
            if (empty($fNavV)) {
                continue;
            }
            ksort($fNavV);
            $tempNav = "<li><a href='#{$fNavK}'>{$fNavK}</a><ul>%secondNav%</ul></li>";
            $secondNav = '';
            /**
             * @var string $sk
             * @var MethodAnnotation $sv
             */
            foreach ($fNavV as $sk => $sv) {
                if ($sv->getApiTag()->deprecated) {
                    $secondNav .= "<li><a href='#{$fNavK}-{$sk}'>{$sv->getApiTag()->name}<sup class='deprecated'>已废弃</sup></a></li>";
                } else {
                    $secondNav .= "<li><a href='#{$fNavK}-{$sk}'>{$sv->getApiTag()->name}</a></li>";
                }
            }
            $nav .= str_replace('%secondNav%', $secondNav, $tempNav);
        }
        $nav .= '</ul>';

        $ret = str_replace('{{$nav}}', $nav, $ret);
        return str_replace('{{$apiDoc}}', $info['html'], $ret);
    }

    function buildAnnotationHtml(string $dirOrFile): array
    {
        $groupList = [];
        $list = $this->scanner->scanAnnotations($dirOrFile);
        $htmls = [];

        /** @var ObjectAnnotation $objectAnnotation */
        foreach ($list as $k => $objectAnnotation) {
            $html = '';
            $currentGroupName = $objectAnnotation->getApiGroupTag() ? $objectAnnotation->getApiGroupTag()->groupName : 'default';

            //第一次构建分全局信息
            if (!isset($groupList[$currentGroupName])) {
                $globalInfo = '';
                $groupList[$currentGroupName] = [];
                $globalInfo .= "<h1 class='group-title' id='{$currentGroupName}'>{$currentGroupName}</h1>{$this->CLRF}";
                $groupDescTag = $objectAnnotation->getApiGroupDescriptionTag();
                if ($groupDescTag) {
                    $globalInfo .= "<h3 class='group-description'>全局描述</h3>{$this->CLRF}";
                    $description = $this->parseDescTagContent($groupDescTag);
                    $globalInfo .= $description . "{$this->CLRF}";
                }


                $onRequest = $objectAnnotation->getMethod('onRequest');
                $groupAuthTagList = $objectAnnotation->getGroupAuthTag();
                $paramTags = $objectAnnotation->getParamTag();
                if ($onRequest instanceof MethodAnnotation) {
                    $groupAuthTagList = array_merge($groupAuthTagList, $onRequest->getApiAuth());
                    $paramTags = array_merge($paramTags, $onRequest->getParamTag());
                }

                if (!empty($groupAuthTagList)) {
                    $globalInfo .= "<h3 class='group-auth'>全局权限说明</h3>{$this->CLRF}";
                    $globalInfo .= $this->buildClassParam($groupAuthTagList);
                }

                if (!empty($paramTags)) {
                    $globalInfo .= "<h3 class='group-param'>全局参数说明</h3>{$this->CLRF}";
                    $globalInfo .= $this->buildClassParam($paramTags);
                }
            }

            //遍历全部方法
            /**
             * @var  $methodName
             * @var MethodAnnotation $method
             */
            $methods = $objectAnnotation->getMethod();
            ksort($methods);
            $controllerAnnotation = $objectAnnotation->getController();
            foreach ($methods as $methodName => $method) {
                //仅仅渲染有api标记的方法
                $apiTag = $method->getApiTag();
                if ($apiTag) {
                    if (!empty($globalInfo)) {
                        $html .= $globalInfo;
                        $globalInfo = '';
                    }

                    $groupList[$currentGroupName][$method->getMethodName()] = $method;
                    $deprecated = '';
                    if ($apiTag->deprecated) {
                        $deprecated .= "<sup class='deprecated'>已废弃</sup>";
                    }
                    $html .= "<h2 class='api-method {$currentGroupName}' id='{$currentGroupName}-{$methodName}'>{$apiTag->name}{$deprecated}</h2>{$this->CLRF}";

                    $html .= "<h3 class='method-description'>基本信息</h3>{$this->CLRF}";
                    //兼容api指定
                    if ($method->getApiDescriptionTag()) {
                        $description = $this->parseDescTagContent($method->getApiDescriptionTag());
                    } else if (!empty($apiTag->description)) {
                        trigger_error('@Api tag description property is deprecated,use @ApiDescription tag instead', E_USER_DEPRECATED);
                        $description = $apiTag->description;
                    } else {
                        $description = '暂无描述';
                    }

                    $path = Scanner::getRoutePath($controllerAnnotation, $apiTag);
                    // 请求路径
                    $html .= "<p><strong>Path：</strong> {$path}</p>{$this->CLRF}";

                    // 请求方法
                    $allow = $method->getMethodTag();
                    if ($allow) {
                        $allow = implode(",", $allow->allow);
                    } else {
                        $allow = '不限制';
                    }
                    $html .= "<p><strong>Method：</strong> {$allow}</p>{$this->CLRF}";

                    // 接口描述
                    $html .= "<p><strong>接口描述：</strong> {$description}</p>{$this->CLRF}";


                    $authParams = $method->getApiAuth();
                    if (!empty($authParams)) {
                        $html .= "<h3 class='auth-params'>权限字段</h3> {$this->CLRF}";
                        $html .= $this->buildMethodParams($authParams);
                    }

                    $requestParams = $method->getParamTag();
                    if (!empty($requestParams)) {
                        $html .= "<h3 class='request-params'>请求字段</h3> {$this->CLRF}";
                        $html .= $this->buildMethodParams($requestParams);
                    }

                    if (!empty($method->getApiRequestExample())) {
                        $html .= "<h3 class='request-example'>请求示例</h3> {$this->CLRF}";
                        $index = 1;
                        foreach ($method->getApiRequestExample() as $example) {
                            $example = $this->parseDescTagContent($example);
                            if (!empty($example)) {
                                $html .= "<p><strong>请求示例{$index}</strong></p>{$this->CLRF}";
                                $html .= "<pre><code class='lang-'>{$example}</code></pre>{$this->CLRF}";
                                $index++;
                            }
                        }
                    }

                    $params = $method->getApiSuccessParam();
                    if (!empty($params)) {
                        $html .= "<h3 class='response-part'>响应</h3>{$this->CLRF}";
                        $html .= "<h4 class='response-params'>成功响应字段</h4> {$this->CLRF}";
                        $html .= $this->buildMethodParams($params);
                    }
                    if (!empty($method->getApiSuccess())) {
                        $html .= "<h4 class='api-success-example'>成功响应示例</h4> {$this->CLRF}";
                        $index = 1;
                        foreach ($method->getApiSuccess() as $example) {
                            $example = $this->parseDescTagContent($example);
                            if (!empty($example)) {
                                $html .= "<p><strong>成功响应示例{$index}</strong></p>{$this->CLRF}";
                                $html .= "<pre><code class='lang-'>{$example}</code></pre>{$this->CLRF}";
                                $index++;
                            }
                        }
                    }
                    $params = $method->getApiFailParam();
                    if (!empty($params)) {
                        $html .= "<h4 class='response-params'>失败响应字段</h4> {$this->CLRF}";
                        $html .= $this->buildMethodParams($params);
                    }

                    if (!empty($method->getApiFail())) {
                        $html .= "<h4 class='api-fail-example'>失败响应示例</h4> {$this->CLRF}";
                        $index = 1;
                        foreach ($method->getApiFail() as $example) {
                            $example = $this->parseDescTagContent($example);
                            if (!empty($example)) {
                                $html .= "<p><strong>失败响应示例{$index}</strong></p>{$this->CLRF}";
                                $html .= "<pre><code class='lang-'>{$example}</code></pre>{$this->CLRF}";
                                $index++;
                            }
                        }
                    }
                }
            }

            if (array_key_exists($currentGroupName, $htmls)) {
                $htmls[$currentGroupName] .= $html;
            } else {
                $htmls[$currentGroupName] = $html;
            }
        }

        ksort($htmls);
        $ret = '';
        foreach ($htmls as $groupName => $html) {
            $ret .= $html;
        }

        return ['html' => $ret, 'methodGroup' => $groupList];
    }

    private function parseDescTagContent(?ApiDescription $apiDescription = null)
    {
        if ($apiDescription == null) {
            return null;
        }
        $ret = null;
        if ($apiDescription->type == 'file' && file_exists($apiDescription->value)) {
            $ret = file_get_contents($apiDescription->value);
        } else {
            $ret = $apiDescription->value;
        }
        $ret = $this->descTagContentFormat($ret);
        if (empty($ret)) {
            $ret = '暂无描述';
        }
        return $ret;
    }

    private function descTagContentFormat($content)
    {
        if (is_array($content)) {
            return json_encode($content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        $json = json_decode($content, true);
        if ($json) {
            $content = json_encode($json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            libxml_disable_entity_loader(true);
            $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOCDATA);
            if ($xml) {
                $content = $xml->saveXML();
            }
        }
        return $content;
    }

    private function buildClassParam($params)
    {
        $html = '';
        if (!empty($params)) {
            $html .= <<<HTML
<table>
    <thead>
    <tr>
        <th>字段</th>
        <th>来源</th>
        <th>类型</th>
        <th>默认值</th>
        <th>描述</th>
        <th>验证规则</th>
        <th>忽略Action</th>
    </tr>
    </thead>
    <tbody>\n
HTML;
            /** @var Param $param */
            foreach ($params as $param) {
                // 已废弃字段处理
                if ($param->deprecated) {
                    $name = $param->name . "<sup class='deprecated'>已废弃</sup>";
                } else {
                    $name = $param->name;
                }

                // 类型
                if (!empty($param->type)) {
                    $type = $param->type;
                } else {
                    $type = '默认';
                }

                // 来源
                if (!empty($param->from)) {
                    $from = implode(",", $param->from);
                } else {
                    $from = "不限";
                }

                // 默认值
                if ($param->defaultValue !== null) {
                    $defaultValue = $param->defaultValue;
                } else {
                    $defaultValue = '-';
                }

                // 描述
                if (!empty($param->description)) {
                    $description = $param->description;
                } else {
                    $description = '-';
                }

                // 验证规则
                if (empty($param->validateRuleList)) {
                    $rule = '-';
                } else {
                    $rule = '';
                    foreach ($param->validateRuleList as $ruleName => $conf) {
                        $arrayCheckFunc = ['inArray', 'notInArray', 'allowFile', 'allowFileType'];
                        if (in_array($ruleName, $arrayCheckFunc)) {
                            if (!is_array($conf[0])) {
                                $conf = [$conf];
                            }
                        }
                        $err = new Error($param->name, null, null, $ruleName, null, $conf);
                        $temp = $err->__toString();
                        $temp = "{$ruleName}: " . substr($temp, strlen($param->name));
                        $rule .= $temp . " <br />";
                    }
                }

                if (isset($param->ignoreAction)) {
                    $ignoreAction = implode(',', $param->ignoreAction);
                    if (empty($ignoreAction)) {
                        $ignoreAction = '-';
                    }
                } else {
                    $ignoreAction = '-';
                }


                $html .= <<<HTML
    <tr>
        <td>{$name}</td>
        <td>{$from}</td>
        <td>{$type}</td>
        <td>{$defaultValue}</td>
        <td>{$description}</td>
        <td>{$rule}</td>
        <td>{$ignoreAction}</td>
    </tr>\n
HTML;
            }
            $html .= <<<HTML
    </tbody>
</table>\n\n
HTML;
        }
        return $html;
    }

    /**
     * 方法仅仅执行@Param()   @ApiAuth()
     */
    private function buildMethodParams($params): string
    {
        $markdown = '';
        if (!empty($params)) {
            $markdown .= <<<HTML
<table>
    <thead>
    <tr>
        <th>字段</th>
        <th>来源</th>
        <th>类型</th>
        <th>默认值</th>
        <th>描述</th>
        <th>验证规则</th>
    </tr>
    </thead>
    <tbody>\n
HTML;
            /** @var Param $param */
            foreach ($params as $param) {
                if ($param instanceof Param || $param instanceof ApiAuth) {
                    // 已废弃字段处理
                    if ($param->deprecated) {
                        $name = $param->name . "<sup class='deprecated'>已废弃</sup>";
                    } else {
                        $name = $param->name;
                    }

                    // 类型
                    if (!empty($param->type)) {
                        $type = $param->type;
                    } else {
                        $type = '默认';
                    }

                    // 来源
                    if (!empty($param->from)) {
                        $from = implode(",", $param->from);
                    } else {
                        $from = "不限";
                    }

                    // 默认值
                    if ($param->defaultValue !== null) {
                        $defaultValue = $param->defaultValue;
                    } else {
                        $defaultValue = '-';
                    }

                    // 描述
                    if (!empty($param->description)) {
                        $description = $param->description;
                    } else {
                        $description = '-';
                    }

                    // 验证规则
                    if (empty($param->validateRuleList)) {
                        $rule = '-';
                    } else {
                        $rule = '';
                        foreach ($param->validateRuleList as $ruleName => $conf) {
                            $arrayCheckFunc = ['inArray', 'notInArray', 'allowFile', 'allowFileType'];
                            if (in_array($ruleName, $arrayCheckFunc)) {
                                if (!is_array($conf[0])) {
                                    $conf = [$conf];
                                }
                            }
                            $err = new Error($param->name, null, null, $ruleName, null, $conf);
                            $temp = $err->__toString();
                            $temp = "{$ruleName}: " . substr($temp, strlen($param->name));
                            $rule .= $temp . " <br />";
                        }
                    }
                    $markdown .= <<<HTML
    <tr>
        <td>{$name}</td>
        <td>{$from}</td>
        <td>{$type}</td>
        <td>{$defaultValue}</td>
        <td>{$description}</td>
        <td>{$rule}</td>
    </tr>\n
HTML;
                }
            }
            $markdown .= <<<HTML
    </tbody>
</table>\n\n
HTML;
        }
        return $markdown;
    }
}
