前后端分离的后台管理系统
-

### 本项目为后端部分

#### 后端语言： PHP

#### 后端框架： [EasySwoole](https://github.com/easy-swoole/easyswoole)

#### 核心逻辑库：[https://github.com/wonder-game/es-utility](https://github.com/wonder-game/es-utility)

## 前端部分

> https://github.com/Joyboo/Joyboo-vben-admin-thin

#### 前端语言: TypeScript + vue3.2

#### 前端框架： [Vben-admin](https://github.com/anncwb/vue-vben-admin)

## 运行

- 在项目目录下执行：

>composer install

- 建好vben_admin库

- 修改easwoole配置文件中的数据库相关配置

- 建表
> php easyswoole migrate run

- 填充数据
> php easyswoole migrate seed
    
- 开发环境下启动：

>php easyswoole server start

- 登录

      账号： admin
      密码:  123456

#### 实现功能

- [x] 客户端动态路由
- [x] jwt登录认证
- [x] 基于Policy实现的权限认证（菜单级别、按钮级别、table单元格级别）
- [x] 单元测试
- [x] 客户端错误日志
- [x] 后台登录、操作日志
- [x] 自定义异常处理器、log处理器
- [x] migrate数据库管理
- [x] Mysql连接池
- [x] 封装CURD业务（继承Auth即可实现基本的CURD）
- [x] Crontab定时任务（可视化操作动态添加、删除、传参）
- [x] CustomProcess消费队列进程
- [x] WebSocket给管理员发消息、踢下线(可批量)
- [x] WebSocket自动无感刷新jwt的token
- [x] WebSocket连接符管理 [FdManager.php](https://github.com/wonder-game/es-utility/blob/master/src/Common/Classes/FdManager.php)
- [x] 监听系统错误Wechat推送、钉钉推送
- [x] HttpTracker链路追踪
- [x] I18N国际化
- [ ] 第三方OAuth扫码登录

还有很多功能正在火热开发中，对应的单元测试和系统demo会慢慢完善，欢迎 [issues](https://github.com/Joyboo/Joyboo-admin-easyswoole/issues) 交流和pr
