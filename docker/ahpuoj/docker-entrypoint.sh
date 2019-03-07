#!/bin/bash
/usr/bin/judged
service php7.2-fpm start
service nginx start
update-alternatives --auto java
update-alternatives --auto javac

/bin/bash  
exit 0