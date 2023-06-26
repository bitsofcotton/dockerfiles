FROM php:fpm-alpine
RUN apk update && \
    apk add imagemagick python3 nginx fcgiwrap musl-dev g++ && \
    mkdir -p /konbu && \
    mkdir -p /var/www/htdocs/.cache/lieonn && \
    mkdir -p /var/run/fcgiwrap
COPY lieonn.hh /konbu
COPY goki.hh /konbu
COPY corpus.hh /konbu
COPY konbu.cc /konbu
COPY p0.cc /konbu
COPY p1.cc /konbu
COPY p.cc /konbu
COPY goki.cc /konbu
COPY catg.cc /konbu
COPY catgr.cc /konbu
COPY decompose.cc /konbu
COPY puts.cc /konbu
COPY init.sh /konbu
COPY index.php /var/www/htdocs
COPY nattoh.css /var/www/htdocs
COPY screen.css /var/www/htdocs
COPY style.css /var/www/htdocs
COPY log.cgi /var/www/htdocs
COPY log_header.html /var/www/htdocs
COPY log_footer.html /var/www/htdocs
COPY nattoh.js /var/www/htdocs
COPY words.txt /var/www/htdocs
COPY nginx-site.conf /etc/nginx/nginx.conf
WORKDIR /konbu
RUN g++ -o /var/www/htdocs/konbu konbu.cc && \
    g++ -o /var/www/htdocs/p0 p0.cc && \
    g++ -o /var/www/htdocs/p1 p1.cc && \
    g++ -o /var/www/htdocs/p p.cc && \
    g++ -o /var/www/htdocs/catg catg.cc && \
    g++ -o /var/www/htdocs/catgr catgr.cc && \
    g++ -o /var/www/htdocs/decompose decompose.cc && \
    g++ -o /var/www/htdocs/goki goki.cc && \
    g++ -o /var/www/htdocs/puts puts.cc && \
    chown -R root:wheel /var/www/htdocs && \
    chmod 111 /var/www/htdocs/konbu /var/www/htdocs/p0 /var/www/htdocs/p1 /var/www/htdocs/p /var/www/htdocs/catg /var/www/htdocs/catgr /var/www/htdocs/decompose /var/www/htdocs/goki /var/www/htdocs/puts && \
    chmod 555 /var/www/htdocs/log.cgi && \
    chown www-data:www-data /var/run/fcgiwrap `which fcgiwrap` && \
    chmod ug+s `which fcgiwrap`
EXPOSE 80
ENTRYPOINT ["./init.sh"]

