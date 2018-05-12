# Development environment 

This is a description how to set up a development environment on a Linux/Mac. I've no clue what happens if you try this on a windows machine.

## Requirements
- git
- VirtualBox (I'm running an old version 5.1.18)
- Vagrant (I'm running an old version 1.9.3)
- 4 cores
- 1GB free RAM

## Installation and start
1. Clone this repo `git clone git@github.com:DjThossi/sebastianthoss.de.git sebastianthoss.de`
1. go into the dir `cd sebastianthoss.de`
1. create a new branch `git checkout -b your-new-branch` because master branch is protected and everything needs to be merged via Pull Requests 
1. boot up Vagrant `vagrant up`. The first time it will take a while because it needs to download a PHP7.1 box.
1. SSH into the box `vagrant ssh`
1. Go to correct directory `cd /vagrant`
1. Install dependencies `composer install`
1. Run markup generation `vendor/bin/sculpin generate` or `vendor/bin/sculpin generate --watch`
1. You can see the result by opening [http://192.168.10.12/](http://192.168.10.12/)
1. Code changes are done in 'source' directory. For example 'source/de/index.html.twig' if you want to change German homepage [http://192.168.10.12/de/](http://192.168.10.12/de/)

In most cases watch mode helps while working on pages or single blog posts. 
It will not render blog overview page again if you just change a single blog entry. 
In this case you need to restart the command.
The result of these commands will be saved in 'output_dev' directory which is connected to the webserver.

## Run ImageGenerator
With this script you can resize images, put them into the correct folders, add them to the files needed for generating fotos page and run production generation
`php _scripts/ImageGenerator/run.php`