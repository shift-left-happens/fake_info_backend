.PHONY: up-prod up-test up-all down test test-warnings lint check

up-prod:
	docker compose -p prod up -d

up-test:
	docker compose -p test --env-file .env.test up -d --build

up-all: up-prod up-test

down:
	docker compose -p prod down
	docker compose -p test down

test:
	docker compose -p test exec -e XDEBUG_MODE=coverage web bash -c "mkdir -p coverage && ./vendor/bin/phpunit --coverage-clover coverage/clover.xml"

test-warnings:
	docker compose -p test exec web ./vendor/bin/phpunit --display-warnings

lint:
	phpcs --standard=PSR12 --extensions=php src/ index.php

lint-fix:
	phpcbf --standard=PSR12 --extensions=php src/ index.php

check: lint test
