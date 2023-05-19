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

然後就能在 http://127.0.0.1:8000/ 看到內容了。

Hydra 的啟動方法如下：

```
# 建資料表
hydra migrate sql -c hydra.yml -e --yes

# 啟動服務
hydra serve all -c hydra.yml --dangerous-force-http
```

最後最麻煩的事，要建 OAuth 2 Client：

```
make setup
```

產出來的 client_id 與 client_secret 會是亂數，無法控制，因此最後必須手動把這兩個值放入 .env 最下面的兩個參數：

```
HYDRA_CLIENT_ID=
HYDRA_CLIENT_SECRET=
```
