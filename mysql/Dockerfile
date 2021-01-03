FROM mysql:5.6.29 as base


FROM base as suzanne-mysql5

RUN apt-get update && apt-get install -y vim

#COPY my.ini /etc/mysql/