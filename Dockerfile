FROM php:7-fpm
RUN apt-get update -y && \
    apt-get install -y imagemagick && \
    apt-get install -y python && \
    apt-get install -y nginx && \
    apt-get install -y fcgiwrap
RUN mkdir -p /var/www/htdocs
RUN mkdir -p /konbu
WORKDIR /konbu
ADD konbu.cc /konbu
ADD konbu.hh /konbu
ADD konbu_init.h /konbu
ADD simplelin.hh /konbu
ADD p0.cc /konbu
ADD p0.hh /konbu
ADD p1.cc /konbu
ADD p1.hh /konbu
ADD goki.cc /konbu
ADD p0-goki.hh /konbu
ADD enlarge.hh /konbu
ADD fileio.hh /konbu
ADD ifloat.hh /konbu
ADD match.hh /konbu
ADD redig.hh /konbu
ADD puts.cc /konbu
ADD corpus.hh /konbu
ADD lword.hh /konbu
ADD sparse.hh /konbu
ADD init.sh /konbu
ADD index.php /var/www/htdocs
ADD nattoh.css /var/www/htdocs
ADD screen.css /var/www/htdocs
ADD style.css /var/www/htdocs
ADD log.cgi /var/www/htdocs
ADD log_header.html /var/www/htdocs
ADD log_footer.html /var/www/htdocs
ADD nattoh.js /var/www/htdocs
ADD words.txt /var/www/htdocs
COPY nginx-site.conf /etc/nginx/nginx.conf
RUN cc -O3 -lm -lstdc++ -DWITHOUT_EIGEN -DACC_DOUBLE -o konbu konbu.cc
RUN cc -O3 -lm -lstdc++ -o p0 p0.cc
RUN cc -O3 -lm -lstdc++ -o p1 p1.cc
RUN cc -O3 -lm -lstdc++ -D_WITHOUT_EIGEN_ -o goki goki.cc
RUN cc -O3 -lm -lstdc++ -D_WITHOUT_EIGEN_ -o puts puts.cc
RUN cp /konbu/konbu /var/www/htdocs
RUN cp /konbu/p0 /var/www/htdocs
RUN cp /konbu/p1 /var/www/htdocs
RUN cp /konbu/goki /var/www/htdocs
RUN cp /konbu/puts /var/www/htdocs
RUN chown -R www-data:www-data /var/www/htdocs
RUN chmod 111 /var/www/htdocs/konbu /var/www/htdocs/p0 /var/www/htdocs/p1 /var/www/htdocs/goki /var/www/htdocs/puts
RUN chmod 555 /var/www/htdocs/log.cgi
RUN mkdir -p /var/run/fcgiwrap
RUN chown www-data:www-data /var/run/fcgiwrap `which fcgiwrap`
RUN chmod ug+s `which fcgiwrap`
EXPOSE 80
EXPOSE 443
ENTRYPOINT ["./init.sh"]

