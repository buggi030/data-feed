docker-build:
	docker build -t data-feed . --build-arg filename=feed.xml

docker-run:
	docker run -v ${PWD}/app/logs:/app/logs data-feed

docker-run-tests:
	docker run data-feed php vendor/bin/phpunit --config phpunit.config.xml tests

