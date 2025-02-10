ARG VERSION=2.4

FROM httpd:${VERSION}-alpine AS base

LABEL version="1.0.0" \
      authors="IDMarinas"

RUN apk add --no-cache \
        ca-certificates \
        openssl \
    && rm -rf /var/lib/apt/lists/*

RUN sed -i \
        -e 's/^#\(LoadModule .*mod_proxy_fcgi.so\)/\1/' \
        -e 's/^#\(LoadModule .*mod_proxy.so\)/\1/' \
        -e 's/^#\(LoadModule .*mod_rewrite.so\)/\1/' \
        -e 's/^#\(LoadModule .*mod_ssl.so\)/\1/' \
        -e 's/^#\(LoadModule .*mod_socache_shmcb.so\)/\1/' \
        -e 's/\/usr\/local\/apache2\/htdocs/\/var\/www\/html/g' \
        -e '$aIncludeOptional conf/sites/*.conf'  \
        -e '$aIncludeOptional conf/d/*.conf'  \
        conf/httpd.conf \
    && mkdir -p /var/www/html

#
# Imagen para desarrollo
#
FROM base AS dev

# Instead of the dots (...) fill in your 2-letter country code (/C), the name of your state or province (/ST),
# the name of your locality (/L), the name of your organization (/O) and your server FQDN (/CN)
RUN openssl req -new -newkey rsa:4096 -days 3650 -nodes -x509 -subj \
    "/C=ES/ST=...../L=..../O=..../CN=localhost" \
    -keyout /usr/local/apache2/conf/server.key \
    -out /usr/local/apache2/conf/server.crt

RUN sed -i \
        -e 's/^#\(ServerName www.example.com:80\)/\1/' \
        -e 's/ServerName www.example.com:80/ServerName localhost:80/g' \
        conf/httpd.conf

#
# Imagen para producción
#
FROM base AS prod