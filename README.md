# RV Mini Program Project

一个基于 Taro + Laravel 的小程序项目，包含管理后台和小程序端。

## 项目架构

### 后端 (Laravel)

位置：`/laravel`

- **框架**: Laravel 12
- **PHP 版本**: 8.2
- **主要依赖**:
  - Filament 3.2 (管理后台框架)
  - Filament Shield (权限管理)
  - Filament Settings Plugin (系统设置)
  - EasyWeChat 6.7 (微信开发SDK)
  - Laravel Sanctum (API 认证)
  
- **开发工具**:
  - Laravel Pail (日志实时查看)
  - Laravel Pint (代码格式化)
  - Laravel Sail (Docker 开发环境)
  
- **前端资源**:
  - Vite 6.0
  - Tailwind CSS 4.0

- **数据存储**:
  - MySQL (主数据库)
  - Redis (缓存)
  - 文件存储: 本地磁盘

- **队列系统**: Database Driver

### 小程序端 (Taro)

位置：`/taro`

- **框架**: Taro 4.0.9
- **开发框架**: React
- **构建工具**: Vite
- **样式方案**: Tailwind CSS
- **状态管理**: Zustand
- **开发环境**:
  - Node.js
  - 微信开发者工具

## 环境配置

### Laravel 环境变量

```env
APP_URL=http://rv.lc
APP_LOCALE=zh_CN
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=rv
QUEUE_CONNECTION=database
CACHE_STORE=database
```

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
```

### Taro

```bash
# 开发模式
npm run dev:weapp

# 构建
npm run build:weapp
```

## Docker 环境

项目使用 Docker 进行开发和部署：

- PHP 8.2
- MySQL
- Redis
- Nginx

## 目录结构

```
/
├── laravel/            # Laravel 后端
│   ├── app/
│   │   ├── Filament/   # Filament 管理后台
│   │   ├── Http/       # 控制器和中间件
│   │   └── Models/     # 数据模型
│   └── ...
└── taro/              # Taro 小程序
    ├── src/
    │   ├── pages/     # 页面组件
    │   ├── components/# 通用组件
    │   └── stores/    # Zustand 状态管理
    └── ...
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