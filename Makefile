.PHONY: test test-coverage test-unit test-unit-coverage test-functional test-functional-coverage install

test: test-unit test-functional

test-coverage:
	@docker-compose run --rm dal vendor/bin/phpunit --coverage-text --coverage-html ./tests/report

test-unit:
	@docker-compose run --rm dal vendor/bin/phpunit --testsuite unit

test-unit-coverage:
	@docker-compose run --rm dal vendor/bin/phpunit --testsuite unit --coverage-text --coverage-html ./tests/report

test-functional:
	@docker-compose run --rm dal vendor/bin/phpunit --testsuite functional

test-functional-coverage:
	@docker-compose run --rm dal vendor/bin/phpunit --testsuite functional --coverage-text --coverage-html ./tests/report

install:
	@docker-compose run --rm dal composer install
