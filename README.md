# Data Feed
A command-line program, based on the Symfony CLI component.

The program process a local XML file (feed.xml) and push the data of that XML file to a
DB (SQLite)


## Commands
```bash
php bin/doctrine orm:schema-tool:drop --force # drop database
php bin/doctrine orm:schema-tool:create # create database
php bin/console parse-data  # run application
```
## Config
app/.env file has all configurable values.
* XML_SOURCE - path to the data source file relative to the 'app' directory.
* DB_DRIVER - database driver
* DB_PATH - sqlite database path (relative to the 'app' directory)

## Run
### in docker container
```bash
docker build -t data-feed . --build-arg filename=feed.xml
docker run -v ${PWD}/app/logs:/app/logs data-feed
```
### outside docker
You can also run this application outside docker. It uses PHP 8.2.3 and composer 2.5.4.
Don't forget to change XML_SOURCE variable in .env.

```bash
cd app
composer install
php bin/doctrine orm:schema-tool:create
php bin/console parse-data
```
## Tests
### in docker
```bash
docker build -t data-feed . --build-arg filename=feed.xml
docker run data-feed php vendor/bin/phpunit --config phpunit.config.xml tests
```

### outside docker
```bash
cd app
composer install
php vendor/bin/phpunit --config phpunit.config.xml tests
```

## Possible improvements
### Database normalisation.

I implemented importing all data into one table, as it was the fastest solution. But I think such a database needs to be normalized.

***Category and Brand should be saved in separate tables.***

During denormalization, categoryName must be transformed to a Category Entity. We have to look into the database and find a Category with the same name. If it does not exist, to create a new Category.

Looking up a Category in the database for each Coffee-Record is a bad idea. We can reduce the number of queries by storing the categories in a hash table [categoryName => categoryEntity]. In this case, the process will be: look in the hash table -> look in the database -> create a new category. This approach also has a bad side - an increase in memory consumption.

***Import data have also some fields, that have only few different values.*** 

For example „flavored“ with values „yes“, „no“ and „variety“.
I suggest storing this data in the database as a tinyint type. (0 for „no“, 1 for „yes“ etc.)
Thus, in the application, we could work with these values as enums. And for external services, this data will be normalised to meaningful string values.

Some fields have only two possible values „no“ and „yes“. Such data should be stored as boolean.

I made these suggestions based on the input I had. It may also be that some other inputs will have also different values. Then this logic should be reconsidered.