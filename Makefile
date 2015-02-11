test: test-unit

test-unit:
	./vendor/bin/phpunit -c .

coverage:
	./vendor/bin/phpunit -c . --coverage-html build/coverage