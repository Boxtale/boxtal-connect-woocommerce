FROM buildpack-deps:stretch-scm
ARG PHP_VERSION
ARG WP_VERSION
ARG WC_VERSION

RUN apt-get update && apt-get install -y --no-install-recommends \
      apache2 \
      apt-transport-https \
      lsb-release \
      ca-certificates \
      sudo

RUN echo "mysql-server-5.6 mysql-server/root_password password password" | sudo debconf-set-selections \
 && echo "mysql-server-5.6 mysql-server/root_password_again password password" | sudo debconf-set-selections \
 && sudo apt-get install -y mysql-server
RUN /etc/init.d/mysql start \
 && sudo mysql -u root -ppassword -e "CREATE USER 'dbadmin'@'localhost' IDENTIFIED BY 'dbpass';" \
 && sudo mysql -u root -ppassword -e "GRANT ALL PRIVILEGES ON *.* TO 'dbadmin'@'localhost' WITH GRANT OPTION;" \
 && sudo mysql -u root -ppassword -e "FLUSH PRIVILEGES;"

RUN curl https://packages.sury.org/php/apt.gpg | apt-key add - \
 && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list \
 &&  apt-get update && apt-get install -y --no-install-recommends \
      php${PHP_VERSION} \
      php${PHP_VERSION}-mysql \
      php${PHP_VERSION}-mbstring \
      php${PHP_VERSION}-soap \
      php${PHP_VERSION}-curl \
      php${PHP_VERSION}-dom

RUN useradd -m docker && echo "docker:docker" | chpasswd && adduser docker sudo \
 && echo "docker ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

ENV HOME=/home/docker
WORKDIR $HOME
ADD . $HOME
RUN chown -R docker:docker $HOME
RUN chmod 777 $HOME/build/entrypoint.sh

ENTRYPOINT $HOME/build/entrypoint.sh

USER docker
