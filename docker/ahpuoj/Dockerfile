FROM ubuntu:18.04
#COPY ./sources.list /etc/apt/sources.list
#FROM debian:jessie-slim

COPY sources.list /etc/apt/sources.list

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

RUN set -ex \
	&& apt-get install -y mysql-client-5.7 \
	nano \
	php7.2-mysql \
	php-mbstring


COPY ustc-sources.list /etc/apt/sources.list

RUN set -ex \
	&&  apt-get update \
	&& apt-get install -y python3.7 \
	golang-1.10 \
	busybox

# RUN set -ex \
# 	&& rm -rf /var/lib/apt/lists/*

RUN set -ex \
	&& ln -fs /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
	&& dpkg-reconfigure -f noninteractive tzdata
	
WORKDIR /home/judge/

COPY ./default /etc/nginx/sites-enabled/default
COPY ./docker-entrypoint.sh /usr/local/bin/

RUN set -ex \
	&& chmod +x /usr/local/bin/docker-entrypoint.sh \
	&& ln -s /usr/local/bin/docker-entrypoint.sh  /docker-entrypoint.sh