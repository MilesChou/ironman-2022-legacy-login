# 頭被夾到的 repo

## 環境建置

Clone 專案後，執行 composer 指令下載

```
composer install
```

接著 Docker 啟動資料庫服務：

```
docker-compose up -d
```

然後要建立金鑰，以及跑 migration：

```
php artisan key:generate
php artisan migrate
```

最後啟動 Laravel 專案

```
php artisan serve
```

然後就能在 http://127.0.0.1/ 看到內容了。
