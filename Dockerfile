# Built from the distribution rather than from the image this one publishes. Building an image from
# its own published tag means it can only ever be rebuilt while that tag exists, and the document
# root only accretes - a file that reached it in some earlier build stays there, which is how the
# repository itself once ended up served by every running copy. From the distribution the result is
# reproducible, and anything no longer in the repository is simply absent.
#
# Trusty is what the scenarios need rather than nostalgia: its php5 is 5.5, which still evaluates
# create_function(), assert() on a string and the preg_replace() "e" modifier. Four scenarios load
# on a modern interpreter but can never be exploited there.
FROM ubuntu:14.04

ENV DEBIAN_FRONTEND=noninteractive

# Apache and PHP, the interpreters the other scenarios are written in, and 'ping' - which most pages
# shell out to, and which a bare image does not carry.
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      apache2 libapache2-mod-php5 php5 php5-cli php5-json \
      python3 ruby perl lua5.1 iputils-ping ca-certificates \
 && rm -rf /var/lib/apt/lists/*

# The site itself, on the host name the scenarios were written against.
RUN mkdir -p /var/www/commix-testbed.com/public_html /var/www/commix-testbed.com/log \
 && printf '<VirtualHost *:80>\n\
  ServerAdmin webmaster@commix-testbed.com\n\
  ServerName  www.commix-testbed.com\n\
  ServerAlias commix-testbed.com\n\
  DirectoryIndex index.html index.php\n\
  DocumentRoot /var/www/commix-testbed.com/public_html\n\
  LogLevel warn\n\
  ErrorLog  /var/www/commix-testbed.com/log/error.log\n\
  CustomLog /var/www/commix-testbed.com/log/access.log combined\n\
</VirtualHost>\n' > /etc/apache2/sites-available/commix-testbed.com.conf \
 && a2dissite 000-default \
 && a2ensite commix-testbed.com \
 && printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf \
 && a2enconf servername

COPY . /var/www/commix-testbed.com/public_html/

# '.dockerignore' keeps these out of the build context; cleared here as well so an image built
# without it - or with an older copy of it - still does not serve the repository.
RUN rm -rf /var/www/commix-testbed.com/public_html/.git \
           /var/www/commix-testbed.com/public_html/.gitignore \
           /var/www/commix-testbed.com/public_html/.dockerignore \
           /var/www/commix-testbed.com/public_html/Dockerfile \
           /var/www/commix-testbed.com/public_html/docker-compose.yml \
           /var/www/commix-testbed.com/public_html/README.md \
 && find /var/www/commix-testbed.com/public_html -name .DS_Store -delete

# COPY carries the permissions the files had on the build host, and Apache runs as www-data.
#
# The document root itself is writable on top of that, which is what the file-based technique needs:
# it writes its output into the web root and then reads it back over HTTP, so a root Apache can only
# read leaves that technique with nowhere to put the file. Only this directory, as the published
# image has always had it - the scenarios below it stay read-only.
RUN chmod -R a+rX /var/www/commix-testbed.com/public_html \
 && chmod a+rwx /var/www/commix-testbed.com/public_html

# The signing keys the 'kid' scenario looks up. Outside the document root, as a real one would be -
# the scenario names a file in here and resolves it through a shell, so it has to exist to resolve.
RUN mkdir -p /var/www/keys \
 && printf -- '-----BEGIN PUBLIC KEY-----\nnot a real key - commix-testbed\n-----END PUBLIC KEY-----\n' > /var/www/keys/prod.pem \
 && printf -- '-----BEGIN PUBLIC KEY-----\nnot a real key - commix-testbed\n-----END PUBLIC KEY-----\n' > /var/www/keys/legacy.pem \
 && chmod 755 /var/www/keys \
 && chmod 644 /var/www/keys/*.pem

# One scenario is served from outside the document root, so the path cannot be inferred from the URL.
RUN mkdir -p /srv/hidden \
 && mv /var/www/commix-testbed.com/public_html/hidden/blind.php /srv/hidden/ \
 && rmdir /var/www/commix-testbed.com/public_html/hidden \
 && chown -R www-data:www-data /srv/hidden \
 && chmod 755 /srv/hidden \
 && printf 'Alias /hidden /srv/hidden\n\
<Directory /srv/hidden>\n\
    Options -Indexes\n\
    AllowOverride None\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/testbed-hidden.conf \
 && a2enconf testbed-hidden

# The Python scenarios are CGI scripts, which this image can run but does not have switched on.
RUN a2enmod cgid \
 && printf '<Directory /var/www/commix-testbed.com/public_html/scenarios/python>\n\
    Options +ExecCGI\n\
    AddHandler cgi-script .py\n\
</Directory>\n\
<Files _layout.py>\n\
    Require all denied\n\
</Files>\n' > /etc/apache2/conf-available/testbed-python.conf \
 && a2enconf testbed-python

# The CTF scenarios recreate the target language of the challenge they are drawn from, not just
# PHP - one of them needs a real Ruby to reproduce a Ruby-specific parsing quirk faithfully.
RUN printf '<Directory /var/www/commix-testbed.com/public_html/scenarios/ctf>\n\
    Options +ExecCGI\n\
    AddHandler cgi-script .py .rb .pl\n\
</Directory>\n' > /etc/apache2/conf-available/testbed-ctf.conf \
 && a2enconf testbed-ctf

# The Ruby scenarios are CGI scripts too, same as the Python ones.
RUN printf '<Directory /var/www/commix-testbed.com/public_html/scenarios/ruby>\n\
    Options +ExecCGI\n\
    AddHandler cgi-script .rb\n\
</Directory>\n\
<Files _layout.rb>\n\
    Require all denied\n\
</Files>\n' > /etc/apache2/conf-available/testbed-ruby.conf \
 && a2enconf testbed-ruby

# The Perl scenario is CGI too - Perl itself already ships with this base image, nothing extra to
# install for it the way Ruby needed.
RUN printf '<Directory /var/www/commix-testbed.com/public_html/scenarios/perl>\n\
    Options +ExecCGI\n\
    AddHandler cgi-script .pl\n\
</Directory>\n\
<Files Layout.pm>\n\
    Require all denied\n\
</Files>\n' > /etc/apache2/conf-available/testbed-perl.conf \
 && a2enconf testbed-perl

# The Lua scenario is CGI too, but Lua itself is not on this base image the way Perl is - 5.1 is
# what it still gets, the last one whose own 'load' cannot compile a string directly.
RUN printf '<Directory /var/www/commix-testbed.com/public_html/scenarios/lua>\n\
    Options +ExecCGI\n\
    AddHandler cgi-script .lua\n\
</Directory>\n\
<Files layout.lua>\n\
    Require all denied\n\
</Files>\n' > /etc/apache2/conf-available/testbed-lua.conf \
 && a2enconf testbed-lua

# A path a filter strips '-' from is corrupted along with the payload if it is one of the
# characters the path is written with - the default vhost's own document root has none, so a scan
# writing its output under it never runs into this by accident.
RUN rm -rf /var/www/html \
 && ln -s /var/www/commix-testbed.com/public_html /var/www/html

EXPOSE 80
CMD ["apache2ctl", "-D", "FOREGROUND"]
