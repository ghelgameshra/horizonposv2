
## Horizon POS 
A modern web-based point of sales (POS) application built with Laravel 11.


## 💻 Server Requirements
**PHP Version:** php 8.3.

**Node Version:** php 20.

**OS Server:** Linux/ Windows.

**Web Server:** Apache, Nginx, or OpenLiteSpeed.

Note: Tested on Linux With Apache or Nginx

## 🖥️ Server Configuration Guide
1. Install php 8.3.* with extensions
~~~bash  
# Add Ondrej's repo source and signing key along with dependencies
sudo apt install apt-transport-https
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php
sudo apt update

# Install new PHP 8.3 packages and extensions
sudo apt install php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl php8.3-soap php8.3-readline php8.3-imagick php8.3-xsl
~~~

2. Install apache or nginx
~~~bash  
sudo apt update

sudo apt install apache -y
//or
sudo apt install nginx -y
~~~

3. Install Nodejs
https://nodejs.org/en/download/package-manager

4. Install Composer
https://getcomposer.org/download/

5. Config static IP
~~~bash
# List netplan configuration file, ex. 1-network-manager-all.yaml
ls /etc/netplan
sudo nano /etc/netplan/1-network-manager-all.yaml
~~~

6. Config webserver with nginx
~~~bash  
sudo nano /etc/nginx/sites-available/pos.conf

# Setup your server
server {
    listen 85;
    listen [::]:85;
    server_name example.com;
    root /var/www/app/LaravelApp/horizonposv2/public;
 
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
 
    index index.php;
 
    charset utf-8;
 
    location / {
        # Header and cors option
        add_header 'Access-Control-Allow-Origin' '*' always;
        add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS, DELETE, PUT' always;
        add_header 'Access-Control-Allow-Headers' 'Authorization, Content-Type, X-Requested-With' always;
        add_header 'Access-Control-Allow-Credentials' 'true' always;
        try_files $uri $uri/ /index.php?$query_string;

        if ($request_method = OPTIONS) {
            add_header 'Access-Control-Allow-Origin' '*' always;
            add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS, DELETE, PUT' always;
            add_header 'Access-Control-Allow-Headers' 'Authorization, Content-Type, X-Requested-With' always;
            add_header 'Access-Control-Allow-Credentials' 'true' always;
            add_header 'Content-Length' 0;
            add_header 'Content-Type' 'text/plain; charset=utf-8';
            return 204;
        }
    }
 
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
 
    error_page 404 /index.php;
 
    # php fpm process
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
 
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # this is route for static file
    location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
        try_files /storage$uri /storage$uri/;
        access_log off;
        log_not_found off;
    }
}

sudo ln -s /etc/nginx/sites-available/pos.conf /etc/nginx/sites-enabled
sudo systemctl restart nginx
~~~

# Use this to config static IP in terminal linux
network:
  version: 2
  ethernets:
    enp0s3:                   # Ganti "enp0s3" dengan nama antarmuka jaringan yang benar
      dhcp4: no
      addresses:
        - 192.168.1.253/24
      routes:
        - to: 0.0.0.0/0
          via: 192.168.1.1      # Sesuaikan dengan gateway jaringan kamu
      nameservers:
        addresses:
          - 8.8.8.8
          - 8.8.4.4
~~~  


## 🚀 Installation Guide

1. Clone the Repository
~~~bash  
git clone https://github.com/ghelgameshra/horizonposv2.git
~~~

2. Navigate to the Project Directory
~~~bash  
cd horizonposv2
~~~

3. Install Dependencies
~~~bash  
composer install --no-dev --optimize-autoloader

npm install
npm run build
~~~

4. Generate Laravel Application Key
~~~bash  
php artisan key:generate
~~~ 

5. Configure Environment Variables
~~~bash  
# Copy the example environment file and update the necessary configurations:
cp .env.example .env

# setting .env configuration file
APP_URL=http://your_url
ASSET_URL=http://your_url
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

# just use MySQL or MariaDB
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
~~~ 


6. Setup File Storage
~~~bash  
# Create a symbolic link to the storage directory:
php artisan storage:link

# Update the .env file to:
FILESYSTEM_DISK=public
~~~


7. Run Database Migrations
~~~bash  
php artisan migrate --seed
# or, if you want to refresh the database
php artisan migrate:fresh --seed
~~~

8. Download And Install Ngrok For Tunneling
~~~bash
curl -sSL https://ngrok-agent.s3.amazonaws.com/ngrok.asc \
| sudo tee /etc/apt/trusted.gpg.d/ngrok.asc >/dev/null \
&& echo "deb https://ngrok-agent.s3.amazonaws.com buster main" \
| sudo tee /etc/apt/sources.list.d/ngrok.list \
&& sudo apt update \
&& sudo apt install ngrok
~~~

9. Set Your Ngrok Token
~~~bash
ngrok config add-authtoken <your-token-here>
~~~

10. Config Tunneling Agent
~~~bash
# In linux configuration file is in ~/.config/ngrok/ngrok.yml
nano ~/.config/ngrok/ngrok.yml

# Setup your tunneling, ex. mysql service
version: "3"
agent:
  authtoken: <your-token-here>
tunnels:
  mysql:
    addr: 3306
    proto: tcp
~~~

10. Test Tunneling With Ngrok
~~~bash
ngrok start --all
~~~

11. Set Up Cron Job for Scheduler Running Upload Tunnelling Address
~~~bash
# This step is optional
# set up new crontab
crontab -e

# set up with this line (this running upload tunnel addres to API every 30 minutes)
*/30 * * * * php /path-to-your-root-project/artisan ngrok:upload-tunnel-data >> /dev/null 2>&1
@reboot ngrok start --all
~~~

## Tech Stack  
**Client:** blade, js

**Server:** Laravel 11.21.0

## 🔑 First Login Credentials

~~~bash
# Use the following default login details to access the system:
Default user    : admin@admin.com
Defult password : Admin.exe
~~~  

Enjoy using Horizon POS, and feel free to contribute or suggest improvements! 😊
