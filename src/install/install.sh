#!/bin/bash

/usr/sbin/useradd -m -u 1536 judge
cd /
cd /home/judge/src/core/judged
chmod +x judged
cp judged /usr/bin
cd ../judge_client
chmod +x judge_client
cp judge_client /usr/bin
cd /home/judge/ 

chmod +x /home/judge/src/core/make.sh
cd /home/judge/src/core/ && ./make.sh
cd /usr/bin && rm awk && cp -s mawk awk

CPU=`grep "cpu cores" /proc/cpuinfo |head -1|awk '{print $4}'`
cd /home/judge/

mkdir etc log
mkdir run0 run1 run2 run3
chown judge run0 run1 run2 run3

touch /home/judge/src/web/admin/config.txt
touch /home/judge/src/web/admin/msg.txt

chmod 775 -R /home/judge/data
chmod 770 -R /home/judge/src/web/admin/config.txt
chmod 770 -R /home/judge/src/web/admin/msg.txt
chmod 775 -R /home/judge/src/web/upload

chgrp -R www-data /home/judge/data
chgrp -R www-data /home/judge/src/web/admin/config.txt
chgrp -R www-data /home/judge/src/web/admin/msg.txt
chgrp -R www-data /home/judge/src/web/upload
chgrp -R www-data /home/judge/src/install/judge.conf
chgrp -R www-data /home/judge/src/install/java0.policy


ln -s  /home/judge/src/install/db_info.inc.php /home/judge/src/web/include/db_info.inc.php
ln -s  /home/judge/src/install/judge.conf /home/judge/etc/judge.conf
ln -s  /home/judge/src/install/java0.policy /home/judge/etc/java0.policy

if [ `grep -c "client_max_body_size"  /etc/nginx/nginx.conf;` -eq 0 ];then
	sed -i "s:include /etc/nginx/mime.types;:client_max_body_size    80m;\n\tinclude /etc/nginx/mime.types;:g" /etc/nginx/nginx.conf
fi
sed -i "s:root /usr/share/nginx/html;:root /home/judge/src/web;:g" /etc/nginx/sites-enabled/default
sed -i "s:index index.html:index index.php:g" /etc/nginx/sites-enabled/default
sed -i "s:#location ~ \\\.php\\$:location ~ \\\.php\\$:g" /etc/nginx/sites-enabled/default
sed -i "s:#\tfastcgi_split_path_info:\tfastcgi_split_path_info:g" /etc/nginx/sites-enabled/default
sed -i "s:#\tfastcgi_pass unix:\tfastcgi_pass unix:g" /etc/nginx/sites-enabled/default
sed -i "s:#\tfastcgi_index:\tfastcgi_index:g" /etc/nginx/sites-enabled/default
sed -i "s:#\tinclude fastcgi_params;:\tinclude fastcgi_params;\n\t}:g" /etc/nginx/sites-enabled/default
sed -i "s/;extension=pdo_mysql/extension=pdo_mysql/g" /etc/php/7.2/fpm/php.ini
sed -i "s/;extension=mbstring/extension=mbstring/g" /etc/php/7.2/fpm/php.ini
sed -i "s/post_max_size = 8M/post_max_size = 80M/g" /etc/php/7.2/fpm/php.ini
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 80M/g" /etc/php/7.2/fpm/php.ini
sed -i --follow-symlinks "s/OJ_RUNNING=1/OJ_RUNNING=$CPU/g" etc/judge.conf
sed -i --follow-symlinks "s/OJ_USER_NAME=root/OJ_USER_NAME=$MYSQL_USER/g" etc/judge.conf
sed -i --follow-symlinks "s/OJ_PASSWORD=root/OJ_PASSWORD=$MYSQL_PASSWORD/g" etc/judge.conf
sed -i --follow-symlinks "s/DB_USER=\"root\"/DB_USER=\"$MYSQL_USER\"/g" src/web/include/db_info.inc.php
sed -i --follow-symlinks "s/DB_PASS=\"root\"/DB_PASS=\"$MYSQL_PASSWORD\"/g" src/web/include/db_info.inc.php

mysql -h db -u$MYSQL_USER -p$MYSQL_PASSWORD< /home/judge/src/install/jol.sql
echo "insert into jol.privilege values('admin','administrator','N');"|mysql -h db -u$MYSQL_USER -p$MYSQL_PASSWORD

mkdir /logs
echo /usr/lib/jvm/java-8-openjdk-amd64/lib/amd64/jli/ >> /etc/ld.so.conf
idconfig

ln -s /usr/bin/python3.7 /usr/bin/python3

service php7.2-fpm restart
service nginx restart
/usr/bin/judged
