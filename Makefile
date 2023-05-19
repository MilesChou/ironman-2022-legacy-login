up:
	docker-compose up -d healthy
	sleep 10
	php artisan migrate
	hydra -c hydra.yml migrate sql -e --yes

down:
	docker-compose down -v

setup:
	hydra create oauth2-client --endpoint http://127.0.0.1:4445/ --skip-tls-verify \
			--grant-type authorization_code \
			--grant-type client_credentials \
			--grant-type refresh_token \
			--response-type code \
			--scope openid \
			--token-endpoint-auth-method client_secret_basic \
			--redirect-uri http://127.0.0.1:8000/callback \
			--post-logout-callback "http://127.0.0.1:8000/logout/callback"

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
