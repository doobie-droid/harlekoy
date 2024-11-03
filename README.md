# SETUP PROJECT FOR LOCAL DEVELOPMENT ON WINDOWS WSL OR UBUNTU LINUX

## Pre-Requisites
The following software programs are required for this project:
| Software | Minimum Version             | Guides                                                                  |
| -------- | --------------------------- | ----------------------------------------------------------------------- |
| PHP      | `8.2.x`                     | See Below                                                               |
| MySQL    | `5.7` | See Below                                                               |
| Composer | `2.x`                       | [Composer Download](https://getcomposer.org/download/)                  |
### PHP
To install PHP on Ubuntu, run the following commands: 
```console
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2-fpm php8.2-common php8.2-mysql php8.2-xml php8.2-xmlrpc php8.2-curl php8.2-gd php8.2-imagick php8.2-cli php8.2-intl php8.2-dev php8.2-imap php8.2-mbstring php8.2-opcache php8.2-soap php8.2-zip unzip -y
```
## MySQL
You may install MySQL on Ubuntu using the following command if you are using Ubuntu 18.10 or below :
If you are using Ubuntu 18.10 or below, you may install MySQL using :
```console
sudo apt update
sudo apt install mysql
```
Set the full version of MySQL in the command line
```console
sudo apt install -f mysql-client=5.7.38-1ubuntu18.04 mysql-community-server=5.7.38-1ubuntu18.04 mysql-server=5.7.38-1ubuntu18.04
```
If you are using Ubuntu 20 or above, read [this](https://medium.com/@lesliedouglas23/how-to-install-mysql-5-0-on-ubuntu-20-04-or-later-4d27de464eef) to install MySQL. 


## Project Setup

1. Clone the repository:

```
git clone git@github.com:doobie-droid/laravel_developer_showdown-Leslie_Douglas.git && cd laravel_developer_showdown-Leslie_Douglas
```

2. Create a copy from .env.example file:

```
cp .env.example .env
```

3. Fill in the requirements keys for local development as follows:

* APP_URL: Your local app URL like localhost:8000
* DB_USERNAME: Local DB username
* DB_PASSWORD: Local DB password

4. Run the composer:

```
composer install
```

# Database

Run the following command to create and seed DB:

```
php artisan migrate:fresh --seed
```

# Testing

1. Create an env for testing  from .env.example file:

```
cp .env.example .env.testing
```
2. Change the DB_DATABASE value in your .env.testing to the DB_TEST_DATABASE value

```
DB_DATABASE=harlekoy_testing
```
3. Create your testing database 
```
php artisan db:create {database_name} e.g. php artisan db:create harlekoy_testing
```
4. RUN YOUR TESTS USING PHP UNIT

```
php artisan test --filter {string}
```