apt-get update
apt-get install -y imagemagick
rm /usr/bin/node > /dev/null 2>&1 || true
ln -s /usr/bin/nodejs /usr/bin/node

rm /etc/nginx/sites-enabled/default > /dev/null 2>&1 || true
cp /vagrant/nginx.site.conf /etc/nginx/sites-enabled/nginx.site.conf
service nginx restart