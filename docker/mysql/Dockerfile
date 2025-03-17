FROM mysql:latest

ENV MYSQL_ROOT_PASSWORD=root
ENV MYSQL_DATABASE=reserve_beach_db
ENV MYSQL_USER=symfony
ENV MYSQL_PASSWORD=symfony

COPY ./init.sql /docker-entrypoint-initdb.d/