#!/bin/bash
cd ${0%/*}

PROJECT_NAME=$(cat ../config/project_name)

BASE_PROJECT_DIR=/var/${PROJECT_NAME}/

# update the box
apt-get update
apt-get -y install make g++

# need proper locale setting otherwise some things fail
update-locale LANG=en_US.UTF-8
export LC_ALL=en_US.UTF-8

# configure nginx & php
apt-get -y install nginx php5-fpm

# install mysql and php
export DEBIAN_FRONTEND=noninteractive
apt-get -y install php5-dev php5-json mysql-server php5-mysqlnd php5-curl php5-gd php5-intl php-pear php5-imagick php5-imap php5-mcrypt php5-memcache php5-recode php5-snmp snmp php5-sqlite php5-tidy php-apc

sed -i.bak '
/^listen = 127.0.0.1:9000/ {
a\
listen = /var/run/php5-fpm.sock
c\
;listen = 127.0.0.1:9000
}' /etc/php5/fpm/pool.d/www.conf

# configure nginx
cp ${BASE_PROJECT_DIR}setup/config/nginx.conf /etc/nginx/sites-available/${PROJECT_NAME}.conf
ln -s /etc/nginx/sites-available/${PROJECT_NAME}.conf /etc/nginx/sites-enabled/

mkdir /var/www
ln -s ${BASE_PROJECT_DIR}public/ /var/www/${PROJECT_NAME}
mkdir /var/log/${PROJECT_NAME}
chmod ugo+w /var/log/${PROJECT_NAME}

# create dir for file uploads
mkdir ${BASE_PROJECT_DIR}public/files
chmod ugo+w ${BASE_PROJECT_DIR}public/files

# get npm
apt-get install npm

# get git
apt-get install git

# build Redis if necessary
# install redis (if not installed)
if [ `which redis-server | grep "redis-server" | wc -l` = 0 ]
then
    cd /tmp
	wget http://download.redis.io/redis-stable.tar.gz
	tar xvzf redis-stable.tar.gz
	cd redis-stable
	make
	make install
	mkdir /etc/redis
	cp redis.conf /etc/redis/6379.conf
	mkdir /var/redis/ && mkdir /var/redis/6379
	sed -i -e "s/daemonize no/daemonize yes/g" /etc/redis/6379.conf # daemonize redis
	sed -i -e "s/redis.pid/redis_6379.pid/g" /etc/redis/6379.conf # set PID file
	sed -i -e "s/logfile stdout/logfile \/var\/log\/redis_6379.log/g" /etc/redis/6379.conf # set log file
	sed -i -e "s/dir .\//dir \/var\/redis\/6379/g" /etc/redis/6379.conf # redis directory
	cp  utils/redis_init_script /etc/init.d/redis_6379
	sudo update-rc.d redis_6379 defaults # add init script
	cd ~
	wget https://github.com/nicolasff/phpredis/archive/master.zip
	apt-get install unzip
	unzip master.zip
	cd phpredis-master
	phpize
	./configure
	make && make install

    # the php 5.5 way
    touch /etc/php5/mods-available/redis.ini
    echo extension=redis.so > /etc/php5/mods-available/redis.ini
    ln -s /etc/php5/mods-available/redis.ini /etc/php5/fpm/conf.d
    ln -s /etc/php5/mods-available/redis.ini /etc/php5/cli/conf.d
fi

# install elasticsearch
sudo add-apt-repository ppa:webupd8team/java
sudo apt-get update
wget -qO - https://packages.elasticsearch.org/GPG-KEY-elasticsearch | sudo apt-key add -
sudo add-apt-repository "deb http://packages.elasticsearch.org/elasticsearch/1.4/debian stable main"
sudo apt-get update
sudo apt-get install oracle-java7-installer
sudo apt-get install elasticsearch
sudo update-rc.d elasticsearch defaults 95 10
# find at http://wishtosay.local:9200/

cd ${BASE_PROJECT_DIR}

# PNG Optimization
apt-get -y install advancecomp pngnq

# install composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# install all composer libs
cd ${BASE_PROJECT_DIR}setup
composer install --no-dev

# re-start all required services
service nginx restart
service php5-fpm restart
service redis_6379 stop && service redis_6379 start
service elasticsearch start