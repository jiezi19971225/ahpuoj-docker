#!/bin/bash

DIRECTORY="/data/data/"
if [ ! -d $DIRECTORY ]; then
	mv  /home/judge/data/ /data/
else
	rm -R /home/judge/data/
fi
ln -s $DIRECTORY /home/judge/data
	
DIRECTORY="/data/judge.conf"
if [ ! -f $DIRECTORY ]; then
	mv /home/judge/etc/judge.conf /data/
else
	rm /home/judge/etc/judge.conf
fi
ln -s $DIRECTORY /home/judge/etc/judge.conf

DIRECTORY="/data/db_info.inc.php"
if [ ! -f $DIRECTORY ]; then
	mv /home/judge/src/web/include/db_info.inc.php /data/
else
	rm /home/judge/src/web/include/db_info.inc.php
fi
ln -s $DIRECTORY /home/judge/src/web/include/db_info.inc.php

DIRECTORY="/data/mysql"
if [ ! -d $DIRECTORY ]; then
	mv  /var/lib/mysql /data
else
	rm -R /var/lib/mysql
fi
ln -s $DIRECTORY /var/lib/mysql

sed -i "s/OJ_USER_NAME=root/OJ_USER_NAME=$MYSQL_USER/g" etc/judge.conf
sed -i "s/OJ_PASSWORD=root/OJ_PASSWORD=$MYSQL_PASSWORD/g" etc/judge.conf
sed -i "s/DB_USER=\"root\"/DB_USER=\"$MYSQL_USER\"/g" src/web/include/db_info.inc.php
sed -i "s/DB_PASS=\"root\"/DB_PASS=\"$MYSQL_PASSWORD\"/g" src/web/include/db_info.inc.php

chmod 775 -R /data/data 
chgrp -R www-data /data/data
chmod 770 -R /data/upload 
chgrp -R www-data /data/upload
chmod 770 -R /data/judge.conf 
chgrp -R www-data /data/judge.conf
chmod 770 -R /data/db_info.inc.php
chgrp -R www-data /data/db_info.inc.php

echo 123 > test.txt

# wait for db to start up

while true;do 
		mysql -h db -u$MYSQL_USER -p$MYSQL_PASSWORD< /home/judge/src/install/jol.sql 2>> error.txt
		if [  $? -eq 0  ]; then
			break
		fi
		echo $?
		sleep 1
done


echo "insert into jol.privilege values('admin','administrator','N');"|mysql -h db -u$MYSQL_USER -p$MYSQL_PASSWORD
mkdir /logs
/usr/bin/judged
service php7.2-fpm start
service nginx start

# USER=`cat /etc/mysql/debian.cnf |grep user|head -1|awk  '{print $3}'` \
# PASSWORD=`cat /etc/mysql/debian.cnf |grep password|head -1|awk  '{print $3}'` \

/bin/bash  
exit 0 