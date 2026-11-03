# Updates the published image in place: same base, same PHP, same configuration - only the
# testbed's own files are replaced. PHP 5.5 still evaluates create_function(), assert() on a string
# and the preg_replace() "e" modifier, so every scenario works here.
FROM commixproject/commix-testbed:latest

COPY . /var/www/commix-testbed.com/public_html/

# This image is rebuilt from the one it publishes, so the document root only ever accretes: a file
# that reached it in some earlier build stays there, and '.dockerignore' cannot take it back out
# again. The repository itself is what that left behind - served whole, directory listing included,
# by every running copy of the testbed - so the build files are cleared out here by name.
RUN rm -rf /var/www/commix-testbed.com/public_html/.git \
           /var/www/commix-testbed.com/public_html/.gitignore \
           /var/www/commix-testbed.com/public_html/.dockerignore \
           /var/www/commix-testbed.com/public_html/Dockerfile \
           /var/www/commix-testbed.com/public_html/docker-compose.yml \
           /var/www/commix-testbed.com/public_html/README.md \
 && find /var/www/commix-testbed.com/public_html -name .DS_Store -delete

# COPY carries the permissions the files had on the build host, and Apache runs as www-data.
RUN chmod -R a+rX /var/www/commix-testbed.com/public_html

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
RUN apt-get update && apt-get install -y --no-install-recommends ruby \
 && rm -rf /var/lib/apt/lists/* \
 && printf '<Directory /var/www/commix-testbed.com/public_html/scenarios/ctf>\n\
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
RUN apt-get update && apt-get install -y --no-install-recommends lua5.1 \
 && rm -rf /var/lib/apt/lists/* \
 && printf '<Directory /var/www/commix-testbed.com/public_html/scenarios/lua>\n\
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
