FROM trafex/php-nginx:3.0.0

LABEL description="Monitoring test application"


COPY . /app
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --chown=root /docker/config/nginx/sites-enabled/* /etc/nginx/conf.d/

USER root
RUN apk add --no-cache php81-tokenizer
USER nobody

EXPOSE 80 9000

WORKDIR /app
