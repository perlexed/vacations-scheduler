FROM php:8.1-cli
RUN apt-get update && apt-get install unzip
COPY . /usr/src/vacation-scheduler
WORKDIR /usr/src/vacation-scheduler
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN ["/usr/bin/composer", "install"]
ENTRYPOINT [ "php", "./start.php" ]
