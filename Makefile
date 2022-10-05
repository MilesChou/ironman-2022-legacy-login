up:
	docker-compose up -d healthy
	sleep 10
	php artisan migrate
	hydra -c hydra.yml migrate sql -e --yes

down:
	docker-compose down -v

setup:
	hydra --endpoint http://127.0.0.1:4445/ clients --skip-tls-verify \
		create \
			--id my-rp \
			--secret my-secret \
			--grant-types authorization_code,implicit,client_credentials,refresh_token \
			--response-types "code,token,id_token,token code,code id_token,id_token token,id_token token code" \
			--scope openid \
			--token-endpoint-auth-method client_secret_basic \
			--callbacks http://127.0.0.1:8000/callback

open:
	open "http://127.0.0.1:8000/"

login:
	open "http://127.0.0.1:8000/login"

logout:
	open "http://127.0.0.1:8000/logout"
