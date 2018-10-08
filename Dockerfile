FROM buildpack-deps:stretch-scm
ARG PHP_VERSION
ARG WP_VERSION
ARG WC_VERSION

RUN curl -sL https://deb.nodesource.com/setup_10.x | bash - \
 && apt-get update && apt-get install -y --no-install-recommends \
    apache2 \
    apt-transport-https \
    lsb-release \
    ca-certificates \
    sudo \
    nodejs \
    xvfb \
    xauth \
    vim

RUN curl https://packages.sury.org/php/apt.gpg | apt-key add - \
 && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list \
 && apt-get update && apt-get install -y --no-install-recommends \
    php${PHP_VERSION} \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-soap \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-dom

RUN wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
RUN sh -c 'echo "deb https://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
RUN apt-get update && apt-get install -y --no-install-recommends \
    google-chrome-stable

RUN sed -i "172,\$s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf \
    && sed -i "s/ServerAdmin/ServerName localhost\nServerAdmin/" /etc/apache2/sites-enabled/000-default.conf \
    && sed -i "s/Include ports.conf/Include ports.conf\nServerName localhost\n/" /etc/apache2/apache2.conf

RUN echo "mysql-server-5.6 mysql-server/root_password password password" | sudo debconf-set-selections \
 && echo "mysql-server-5.6 mysql-server/root_password_again password password" | sudo debconf-set-selections \
 && sudo apt-get install -y mysql-server
RUN /etc/init.d/mysql start \
 && sudo mysql -u root -ppassword -e "CREATE USER 'dbadmin'@'localhost' IDENTIFIED BY 'dbpass';" \
 && sudo mysql -u root -ppassword -e "GRANT ALL PRIVILEGES ON *.* TO 'dbadmin'@'localhost' WITH GRANT OPTION;" \
 && sudo mysql -u root -ppassword -e "FLUSH PRIVILEGES;"

RUN useradd -m docker && echo "docker:docker" | chpasswd && adduser docker sudo \
 && echo "docker ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

ENV HOME=/home/docker
WORKDIR $HOME
RUN chown -R docker:docker $HOME

COPY wp-cli.yml $HOME
COPY factory/common/install-wp.sh $HOME/factory/common/
RUN /etc/init.d/apache2 start \
 && /etc/init.d/mysql start \
 && /bin/bash $HOME/factory/common/install-wp.sh $WP_VERSION $WC_VERSION

COPY . $HOME
RUN chown -R docker:docker $HOME
RUN chmod -R +x $HOME/factory/common
RUN $HOME/factory/common/sync.sh
RUN $HOME/factory/docker/install-hook-tester.sh

USER docker
ENTRYPOINT $HOME/factory/docker/entrypoint.sh
