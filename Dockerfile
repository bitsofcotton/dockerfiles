FROM php:fpm-alpine
RUN apk update && \
    apk add imagemagick && \
    apk add python3 && \
    apk add nginx && \
    apk add fcgiwrap && \
    apk add alpine-sdk && \
    apk add clang
RUN mkdir -p /var/www/htdocs
RUN mkdir -p /konbu
COPY lieonn.hh /konbu
COPY konbu.cc /konbu
COPY p0.cc /konbu
COPY p0.hh /konbu
COPY p1.cc /konbu
COPY p1.hh /konbu
COPY goki.cc /konbu
COPY goki.hh /konbu
COPY catg.cc /konbu
COPY catgr.cc /konbu
COPY catg.hh /konbu
COPY decompose.cc /konbu
COPY decompose.hh /konbu
COPY puts.cc /konbu
COPY corpus.hh /konbu
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
RUN cd /konbu && g++ -Ofast -o konbu konbu.cc
RUN cd /konbu && g++ -Ofast -o p0 p0.cc
RUN cd /konbu && g++ -Ofast -o p1 p1.cc
RUN cd /konbu && g++ -Ofast -o catg catg.cc
RUN cd /konbu && g++ -Ofast -o catgr catgr.cc
RUN cd /konbu && g++ -Ofast -o decompose decompose.cc
RUN cd /konbu && g++ -Ofast -o goki goki.cc
RUN cd /konbu && g++ -Ofast -o puts puts.cc
RUN cp /konbu/konbu /var/www/htdocs
RUN cp /konbu/p0 /var/www/htdocs
RUN cp /konbu/p1 /var/www/htdocs
RUN cp /konbu/catg /var/www/htdocs
RUN cp /konbu/catgr /var/www/htdocs
RUN cp /konbu/decompose /var/www/htdocs
RUN cp /konbu/goki /var/www/htdocs
RUN cp /konbu/puts /var/www/htdocs
RUN chown -R root:wheel /var/www/htdocs
RUN chmod 111 /var/www/htdocs/konbu /var/www/htdocs/p0 /var/www/htdocs/p1 /var/www/htdocs/catg /var/www/htdocs/catgr /var/www/htdocs/decompose /var/www/htdocs/goki /var/www/htdocs/puts
RUN chmod 555 /var/www/htdocs/log.cgi
RUN mkdir -p /var/www/htdocs/.cache/lieonn
RUN mkdir -p /var/run/fcgiwrap
RUN chown www-data:www-data /var/run/fcgiwrap `which fcgiwrap`
RUN chmod ug+s `which fcgiwrap`
EXPOSE 80
ENTRYPOINT ["./init.sh"]

