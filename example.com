server {

  listen 80; # listen also for IPv4 traffic on "regular" IPv4 sockets

  server_name "example.com";

  access_log  /var/log/nginx/$host.access.log;
  error_log   /var/log/nginx/error.log;

  root    /var/www/example/public;
  index   index.php index.html;

  location ~ /\. {
    deny all;
  }

  ## Favicon Not Found
    access_log off;
    log_not_found off;
  }

  ## Robots.txt Not Found
  location = /robots.txt {
    access_log off;
    log_not_found off;
  }

  location / {
    try_files $uri $uri/ /index.php;
  }

  location ~ \.php$ {
    include /etc/nginx/fastcgi.conf;
    fastcgi_pass unix:/var/run/php5-fpm.sock;
  }

}

#for subdomains on a SparkyCMS install
server {
  listen 80;

  server_name ~^(?<domain>.+)\.example\.com$;
  root /var/www/$domain/public;
  index index.php;

  #access_log /var/log/domains/$domain.access.log;
  #error_log /var/log/domains/$domain.access.log;

  location / {
    try_files $uri $uri/ /index.php;
  }

  location ~ \.php$ {
    include /etc/nginx/fastcgi.conf;
    fastcgi_pass unix:/var/run/php5-fpm.sock;
  }
}

