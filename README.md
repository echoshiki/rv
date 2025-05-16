# RV Mini Program Project

一个基于 Taro + Laravel 的小程序项目，包含管理后台和小程序端。

## 项目架构

### Laravel 后端

```
laravel/
├── app/
│   ├── Filament/          # 后台管理系统相关
│   ├── Http/             # HTTP层
│   │   ├── Controllers/  # 控制器
│   │   ├── Requests/     # 请求验证
│   │   └── Resources/    # API资源
│   ├── Models/           # 数据模型
│   │   ├── Article.php   # 文章模型
│   │   ├── ArticleCategory.php  # 文章分类
│   │   ├── Banner.php    # 轮播图
│   │   ├── MenuGroup.php # 菜单组
│   │   ├── MenuItem.php  # 菜单项
│   │   ├── SinglePage.php  # 单页面
│   │   ├── User.php      # 用户
│   │   └── WechatUser.php # 微信用户
│   ├── Policies/         # 权限策略
│   ├── Providers/        # 服务提供者
│   ├── Services/         # 服务层
│   └── Settings/         # 设置
├── bootstrap/              # 应用启动配置
├── config/                 # 配置文件
├── database/               # 数据库相关
│   ├── factories/         # 数据库工厂
│   ├── migrations/        # 数据库迁移
│   └── seeders/           # 数据库填充
├── public/                # 公开访问资源
├── routes/                # 路由定义
├── tests/                 # 测试文件
└── storage/                # 第三方依赖
```

- **框架**: Laravel 12
- **PHP 版本**: 8.2
- **主要依赖**:
  - Filament 3.2 (管理后台框架)
  - Filament Shield (权限管理)
  - Filament Settings Plugin (系统设置)
  - EasyWeChat 6.7 (微信开发SDK)
  - Laravel Sanctum (API 认证)
- **前端资源**:
  - Vite 6.0
  - Tailwind CSS 4.0

### Taro 小程序端

```
taro/src/
├── api/                  # API接口
├── assets/               # 静态资源
├── components/           # 公共组件
├── config/               # 配置文件
├── hooks/                # 自定义 Hooks
├── pages/                # 页面
│   ├── 404/             # 404页面
│   ├── activity/        # 活动页面
│   ├── article/         # 文章页面
│   │   ├── detail/     # 文章详情
│   │   └── list/       # 文章列表
│   ├── index/          # 首页
│   ├── login/          # 登录页面
│   ├── sale/           # 销售页面
│   ├── usage/          # 使用页面
│   └── user/           # 用户页面
├── stores/              # 状态管理
├── types/               # TypeScript类型定义
└── utils/               # 工具函数
```

- **框架**: Taro 4.0.9
- **开发框架**: React
- **构建工具**: Vite
- **样式方案**: Tailwind CSS
- **状态管理**: Zustand

## 开发命令

### Laravel

```bash
# 启动所有服务（开发模式）
composer dev

# 包含:
# - php artisan serve (API服务)
# - php artisan queue:listen (队列处理)
# - php artisan pail (日志监控)
# - npm run dev (Vite资源编译)

# 初始化数据
php artisan migrate:refresh
php artisan make:filament-user
php artisan shield:generate --all
php artisan shield:super-admin
php artisan db:seed

# 编辑器注释
php artisan ide:generate
```

### Taro

```bash
# 开发模式
npm run dev:weapp

# 构建
npm run build:weapp
```

## 部署要求

- PHP >= 8.2
- MySQL >= 8.0
- Redis >= 6.0
- Node.js >= 18
- Composer 2.x
- npm 或 yarn

## License

MIT