#Dockerfile
FROM php:7.4-cli-alpine
COPY . /var/www/html
WORKDIR /var/www/html
ENTRYPOINT [ "php", "./index.php" ]