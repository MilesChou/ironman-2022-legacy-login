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
			--callbacks http://127.0.0.1:8000/callback \
			--post-logout-callbacks "http://127.0.0.1:8000/logout/callback"
	hydra --endpoint http://127.0.0.1:4445/ clients --skip-tls-verify \
		create \
			--id rp1 \
			--secret secret1 \
			--grant-types authorization_code,implicit,client_credentials,refresh_token \
			--response-types "code,token,id_token,token code,code id_token,id_token token,id_token token code" \
			--scope openid \
			--token-endpoint-auth-method client_secret_basic \
			--callbacks http://127.0.0.1:8000/rp1/callback \
			--post-logout-callbacks "http://127.0.0.1:8000/rp1/logout/callback" \
			--backchannel-logout-callback "http://127.0.0.1:8000/api/rp1/logout/backchannel" \
			--backchannel-logout-session-required true
	hydra --endpoint http://127.0.0.1:4445/ clients --skip-tls-verify \
		create \
			--id rp2 \
			--secret secret2 \
			--grant-types authorization_code,implicit,client_credentials,refresh_token \
			--response-types "code,token,id_token,token code,code id_token,id_token token,id_token token code" \
			--scope openid \
			--token-endpoint-auth-method client_secret_basic \
			--callbacks http://127.0.0.1:8000/rp2/callback \
			--post-logout-callbacks "http://127.0.0.1:8000/rp2/logout/callback" \
			--backchannel-logout-callback "http://127.0.0.1:8000/api/rp2/logout/backchannel"
#			--backchannel-logout-session-required true

teardown:
	hydra --endpoint http://127.0.0.1:4445/ clients delete my-rp
	hydra --endpoint http://127.0.0.1:4445/ clients delete rp1
	hydra --endpoint http://127.0.0.1:4445/ clients delete rp2

open:
	open "http://127.0.0.1:8000/"

login:
	open "http://127.0.0.1:8000/login"

logout:
	open "http://127.0.0.1:8000/logout"

login-rp1:
	open "http://127.0.0.1:8000/rp1/login"

logout-rp1:
	open "http://127.0.0.1:8000/rp1/logout"

login-rp2:
	open "http://127.0.0.1:8000/rp2/login"

logout-rp2:
	open "http://127.0.0.1:8000/rp2/logout"
