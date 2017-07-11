Log Viewer
==========
View log files using web browser

Running in System:
------------------
**Requirements**
1. php 5.6+
1. Apache2
1. phpUnit6.2+

Running in Docker:
-----------------
1. Install Docker with docker compose [(reference link)](https://docs.docker.com/engine/installation/linux/ubuntulinux/)
1. Unzip the folder or Clone this repository: `$ git clone https://gitlab.com/prochito/amujamu.git`
1. cd to project directory: `$ cd log-viewer`
1. Build docker images: `$ docker-compose build`
1. Start docker containers: `$ docker-compose up -d`
1. Fire up test suite: `$ docker exec -it --user www-data logviewer phpunit ReadLogFileTest`
1. Navigate to explore: `http://localhost:8383`
