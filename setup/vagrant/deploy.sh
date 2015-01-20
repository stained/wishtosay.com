#!/bin/bash
BASE_CACHE_DIR=/vagrant/.cache/
for f in "apt/archives"; do 
    rm -rf ${BASE_CACHE_DIR}$f; #remove symlink
    rm -rf /var/cache/$f; #remove directory and contents if it exists
    mkdir -p ${BASE_CACHE_DIR}$f;
    ln -sf ${BASE_CACHE_DIR}$f /var/cache/$f;
done;

cd ${0%/*}

PROJECT_NAME=$(cat /vagrant/setup/config/project_name)

ln -s /vagrant /var/${PROJECT_NAME}

# Add the required host entries
if [ `cat /etc/hosts | grep ${PROJECT_NAME}.local | wc -l` = 0 ]
then
    echo "192.168.33.111   ${PROJECT_NAME}.local" >> /etc/hosts
fi

# and run the common parts of the deployment process
/var/${PROJECT_NAME}/setup/vagrant/common.sh

# and copy the nginx dev config
cp /var/${PROJECT_NAME}/setup/config/nginx.dev.conf /etc/nginx/sites-available/${PROJECT_NAME}.conf
service nginx restart


