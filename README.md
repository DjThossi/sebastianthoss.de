# Development environment 

This is a description how to set up a development environment on a Linux/Mac. I've no clue what happens if you try this on a windows machine.

## Requirements
- git
- docker

## Installation and start
1. Clone this repo `git clone git@github.com:DjThossi/sebastianthoss.de.git sebastianthoss.de`
1. go into the dir `cd sebastianthoss.de`
1. boot docker containers `docker-compose up -d`
1. log into php container `docker-compose exec php bash`
1. Install dependencies `composer install`
1. Run markup generation `vendor/bin/sculpin generate` or `vendor/bin/sculpin generate --watch`
1. You can see the result by opening [http://localhost:8081](http://localhost:8081)
1. Code changes are done in 'source' directory. For example 'source/de/index.html.twig' if you want to change German homepage [http://localhost:8081/de/](http://localhost:8081/de/)

In most cases watch mode helps while working on pages or single blog posts. 
It will not render blog overview page again if you just change a single blog entry. 
In this case you need to restart the command.
The result of these commands will be saved in 'output_dev' directory which is connected to the web server.

## Run ImageGenerator
With this script you can resize images, put them into the correct folders, add them to the files needed for generating fotos page and run production generation
`php _scripts/ImageGenerator/run.php`