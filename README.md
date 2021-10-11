
- 在项目目录下执行：

>composer install

- 建好vben_admin库

- 建表
> php easyswoole migrate run

- 生成数据
> php easyswoole migrate seed
    
- 开发环境下启动：

>php easyswoole server start

- 登录

      账号： admin
      密码:  123456

#### todolist

- [x] 自定义异常处理器、log处理器
- [ ] 合并migrate配置项
- [x] 权限控制
- [x] 封装CURD
- [ ] i18n按模块命名
