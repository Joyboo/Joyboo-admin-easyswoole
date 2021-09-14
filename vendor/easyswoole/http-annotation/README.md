# HttpAnnotation

## 安装
```bash
composer require easyswoole/http-annotation
```
## 注解规范

### Example
```php
/**
 * Class ControllerA
 * @package EasySwoole\HttpAnnotation\Tests\TestController
 * @ApiGroup(groupName="A")
 * @ApiGroupDescription()
 */
class ControllerA extends AnnotationController
{
    /**
     * @Api(path="/A/test")
     */
    function test()
    {

    }

    /**
     * @Api(path="")
     */
    function test2()
    {
        
    }
}
```