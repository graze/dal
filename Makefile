.PHONY: test test-coverage test-unit test-unit-coverage test-functional test-functional-coverage install

test:
	@./vendor/bin/phpunit

test-coverage:
	@./vendor/bin/phpunit --coverage-text --coverage-html ./tests/report

test-unit:
	@./vendor/bin/phpunit --testsuite unit

test-unit-coverage:
	@./vendor/bin/phpunit --testsuite unit --coverage-text --coverage-html ./tests/report

test-functional:
	@./vendor/bin/phpunit --testsuite functional

test-functional-coverage:
	@./vendor/bin/phpunit --testsuite functional --coverage-text --coverage-html ./tests/report

docs:
	@php sami.phar update sami.php

install:
	@composer install
