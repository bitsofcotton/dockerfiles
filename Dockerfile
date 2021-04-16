FROM php:7-fpm
RUN apt-get update -y && \
    apt-get install -y imagemagick && \
    apt-get install -y python && \
    apt-get install -y nginx && \
    apt-get install -y fcgiwrap
RUN mkdir -p /var/www/htdocs
RUN mkdir -p /konbu
COPY ifloat.hh /konbu
COPY konbu.cc /konbu
COPY simplelin.hh /konbu
COPY p0.cc /konbu
COPY p0.hh /konbu
COPY p1.cc /konbu
COPY p1.hh /konbu
COPY goki.cc /konbu
COPY enlarge.hh /konbu
COPY fileio.hh /konbu
COPY match.hh /konbu
COPY redig.hh /konbu
COPY catg.hh /konbu
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
RUN cc -O3 -lm -lstdc++ -o konbu konbu.cc
RUN cc -O3 -lm -lstdc++ -o p0 p0.cc
RUN cc -O3 -lm -lstdc++ -o p1 p1.cc
RUN cc -O3 -lm -lstdc++ -o goki goki.cc
RUN cc -O3 -lm -lstdc++ -o puts puts.cc
RUN cp konbu /var/www/htdocs
RUN cp p0 /var/www/htdocs
RUN cp p1 /var/www/htdocs
RUN cp goki /var/www/htdocs
RUN cp puts /var/www/htdocs
RUN chown -R root:wheel /var/www/htdocs
RUN chmod 111 /var/www/htdocs/konbu /var/www/htdocs/p0 /var/www/htdocs/p1 /var/www/htdocs/goki /var/www/htdocs/puts
RUN chmod 555 /var/www/htdocs/log.cgi
RUN mkdir -p /var/run/fcgiwrap
RUN chown www-data:www-data /var/run/fcgiwrap `which fcgiwrap`
RUN chmod ug+s `which fcgiwrap`
EXPOSE 80
ENTRYPOINT ["./init.sh"]

