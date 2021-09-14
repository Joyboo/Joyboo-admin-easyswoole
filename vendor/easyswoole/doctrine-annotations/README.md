# doctrine-annotations

## 版权声明
本项目修改自 [doctrine/annotation](https://github.com/doctrine/annotations) , 原项目开源协议为MIT协议。为满足无法实现[plainText解析](https://github.com/doctrine/annotations/issues/358)
与json array,故此实现专版。

## 使用
其他使用方法与原库一致
### plaintText
用法为：
```
 MyAnnotation(myProperty=r"{"code":200}")
```
也就是格式为
```
r"{RAW}"
```
字母r+双引号。

### json Array
用法为：
```
@PropertyTag(input={"code":2,"result":[{"name":1}]})
```
可以直接解析为完整的数组。

## 单元测试
关于单元测试已经迁移了原库的单元测试，并增补了plainText与json array的解析