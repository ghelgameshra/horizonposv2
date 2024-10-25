
## Horizon POS 
A modern web-based point of sales (POS) application built with Laravel 11.



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
  
  npm install --production
  npm run build
~~~

4. Generate Laravel Application Key
~~~bash  
  php artisan key:generate
~~~ 

5. Configure Environment Variables
~~~bash  
  // Copy the example environment file and update the necessary configurations:
  cp .env.example .env
  
  // setting .env configuration file
  APP_URL=http://your_url
  ASSET_URL=http://your_url
  APP_DEBUG=false
  APP_TIMEZONE=Asia/Jakarta
  APP_LOCALE=id
  APP_FALLBACK_LOCALE=id
  APP_FAKER_LOCALE=id_ID

  COMPANY_NAME="COMPANY NAME" // you should change this of company name

  // just use MySQL or MariaDB
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=your_db
  DB_USERNAME=your_username
  DB_PASSWORD=your_password
~~~ 


6. Setup File Storage
~~~bash  
  // Create a symbolic link to the storage directory:
  php artisan storage:link

  // Update the .env file to:
  FILESYSTEM_DISK=public
~~~


7. Run Database Migrations
~~~bash  
  php artisan migrate --seed
  // or, if you want to refresh the database
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
  // In linux configuration file is in ~/.config/ngrok/ngrok.yml
  nano ~/.config/ngrok/ngrok.yml

  // Setup your tunneling, ex. mysql service
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
    // set up new crontab
  crontab -e

  // set up with this line (this running upload tunnel addres to API every 5 minutes)
  */1 * * * * php /path-to-your-root-project/artisan ngrok:upload-tunnel-data >> /dev/null 2>&1
~~~

## Tech Stack  
**Client:** blade, js  

**Server:** Laravel 11.21.0

## 🔑 First Login Credentials

~~~bash
  // Use the following default login details to access the system:
  Default user    : admin@admin.com
  Defult password : Admin.exe
~~~  

Enjoy using Horizon POS, and feel free to contribute or suggest improvements! 😊

