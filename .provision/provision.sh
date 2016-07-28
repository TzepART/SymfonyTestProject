sudo apt-get update
export LC_ALL="en_US.UTF-8"
dpkg-reconfigure locales
sudo apt-get install -y python-software-properties
sudo apt-add-repository ppa:ondrej/php5
sudo apt-get update
sudo apt-get install -y git php5-dev php5-memcached php5-common \
php5-json php5-cli php5-cgi php5-gmp php5-fpm php5 php5-curl php5-intl \
php5-xsl php5-mysqlnd php5-mcrypt php5-readline php5-gd
 
echo "mysql-server mysql-server/root_password password root" | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password root" | debconf-set-selections
 
sudo apt-get install -y mysql-server mysql-client

mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS symfony"

apt-get install -y language-pack-UTF-8
apt-get install -y nginx
rm -rf /etc/nginx/sites-enabled/default
cp /vagrant/.provision/symfony_project.conf /etc/nginx/sites-available/symfony_project.conf
ln -s /etc/nginx/sites-available/symfony_project.conf /etc/nginx/sites-enabled/symfony_project.conf
 
service nginx restart

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/bin/composer

/usr/bin/composer global require "fxp/composer-asset-plugin:1.0.0"

echo '{ "github-oauth": { "github.com": "4094b7769e58ae6585afe8d4dcc65fee038c6358"}}' | tee ~/.composer/auth.json
cp -R ~/.composer/ /home/vagrant/.

(cd /vagrant && /usr/bin/composer install)
 

