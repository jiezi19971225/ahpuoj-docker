FROM ubuntu:18.04
#COPY ./sources.list /etc/apt/sources.list
#FROM debian:jessie-slim

COPY sources.list /etc/apt/sources.list
VOLUME 

RUN set -ex \
	&& export DEBIAN_FRONTEND=noninteractive \
	&& apt-get update \
	&& apt-get install -y tzdata
	
RUN set -ex \
	&& apt-get install -y \
		git \
		make flex g++ libmysqlclient-dev libmysql++-dev \
		php7.2 \
		memcached \
		php-fpm \
		nginx \
		fp-compiler \
		openjdk-8-jdk \
		clang
	#&& apt-get remove --purge git
	# && rm -rf /var/lib/apt/lists/* 

RUN set -ex \
	&& apt-get install -y mysql-client-5.7 \
	nano \
	php7.2-mysql \
	php-mbstring

RUN set -ex \
	&& ln -fs /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
	&& dpkg-reconfigure -f noninteractive tzdata
	
RUN set -ex \
	&& /usr/sbin/useradd -m -u 1536 judge \
	&& cd / \

RUN set -ex \
	&& USER=`cat /etc/mysql/debian.cnf |grep user|head -1|awk  '{print $3}'` \
	&& PASSWORD=`cat /etc/mysql/debian.cnf |grep password|head -1|awk  '{print $3}'` \
	&& CPU=`grep "cpu cores" /proc/cpuinfo |head -1|awk '{print $4}'`   \	
	&& cd /home/judge/                  \
	&& mkdir etc data log   \
	&& mkdir run0 run1 run2 run3   \
	&& chown judge run0 run1 run2 run3   \
	&& sed -i "s/OJ_RUNNING=1/OJ_RUNNING=$CPU/g" etc/judge.conf   \
	&& chown www-data src/web/upload data   \
	&& sed -i "s:include /etc/nginx/mime.types;:client_max_body_size    80m;\n\tinclude /etc/nginx/mime.types;:g" /etc/nginx/nginx.conf   \
	&& sed -i "s:root /usr/share/nginx/html;:root /home/judge/src/web;:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:index index.html:index index.php:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:#location ~ \\\.php\\$:location ~ \\\.php\\$:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:#\tfastcgi_split_path_info:\tfastcgi_split_path_info:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:#\tfastcgi_pass unix:\tfastcgi_pass unix:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:#\tfastcgi_index:\tfastcgi_index:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:#\tinclude fastcgi_params;:\tinclude fastcgi_params;\n\t}:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s/;extension=pdo_mysql/extension=pdo_mysql/g" /etc/php/7.2/fpm/php.ini     \
	&& sed -i "s/;extension=mbstring/extension=mbstring/g" /etc/php/7.2/fpm/php.ini     \
	&& sed -i "s/post_max_size = 8M/post_max_size = 80M/g" /etc/php/7.2/fpm/php.ini     \
	&& sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 80M/g" /etc/php/7.2/fpm/php.ini

RUN set -ex \
	&& cd /home/judge/src/core/judged \
    && chmod +x judged \
    && cp judged /usr/bin \
    && cd ../judge_client \
    && chmod +x judge_client \
    && cp judge_client /usr/bin \
	&& cd /home/judge/ 


	
RUN chmod +x /home/judge/src/core/make.sh
RUN cd /home/judge/src/core/ && ./make.sh
RUN cd /usr/bin && rm awk && cp -s mawk awk

WORKDIR /home/judge/

COPY ./default /etc/nginx/sites-enabled/default
COPY ./docker-entrypoint.sh /usr/local/bin/

RUN set -ex \
	&& chmod +x /usr/local/bin/docker-entrypoint.sh \
	&& ln -s /usr/local/bin/docker-entrypoint.sh  /docker-entrypoint.sh

#CMD judged /home/judge debug && 
#CMD tail -f /dev/null