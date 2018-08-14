# Development environment for Drupal 8 build with Docker Compose

## Dependencies

* [Docker](https://www.docker.com/)
* [Docker Compose](https://docs.docker.com/compose/install/)
* [Composer](https://getcomposer.org/download/)

## How to use

### Download this repository

	$ git clone https://github.com/joaquinvargascr2018/d8-birthday.git d8-docker
	$ cd d8-docker

### Excecute composer command
Execute composer install

    $ cd project && composer instal

### Start the docker containers

This command will build the containers and start the web and mysql servers. All logging will be outputted on the screen.

	$ docker-compose up -d

### install Drupal composer updates

	$ docker-compose exec web composer install -o

### Install Drupal 8 by using Drupal console

	$ docker-compose exec php drupal site:install --db-host=database --db-name=drupal8 --db-user=drupal8 --db-pass=drupal8