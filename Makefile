.PHONY: test test-coverage test-unit test-unit-coverage test-functional test-functional-coverage install

test: test-unit test-functional

test-coverage:
	@docker-compose run --rm dal vendor/bin/phpunit --coverage-text --coverage-html ./tests/report

test-unit:
	@docker-compose run --rm dal vendor/bin/phpunit --testsuite unit

test-unit-coverage:
	@docker-compose run --rm dal vendor/bin/phpunit --testsuite unit --coverage-text --coverage-html ./tests/report

test-functional:
	docker-compose up -d db
	docker-compose run --rm dal build/db.sh dal_db 3306
	@docker-compose run --rm dal vendor/bin/phpunit --testsuite functional

test-functional-coverage:
	docker-compose up -d db
	docker-compose run --rm dal build/db.sh dal_db 3306
	@docker-compose run --rm dal vendor/bin/phpunit --testsuite functional --coverage-text --coverage-html ./tests/report

install:
	docker-compose build dal
	docker-compose up -d --force-recreate dal db
	@docker-compose run --rm composer install

lint:
	docker-compose run --rm dal vendor/bin/phpcs -p --warning-severity=0 src/ tests/

lint-fix:
	docker-compose run --rm dal vendor/bin/phpcbf -p --no-patch src/
