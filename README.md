# envres-website
This website uses HTML, CSS, and PHP to create a reservation booking system for server environments. This was a work assignment (Co-op Fall/Summer 2016).

## Prerequisites
These instructions are for Ubuntu-based Linux distributions. A text editor is required for writing code (I use Komodo, but you can use whatever you'd like such as Notepad++). A web server is required to run PHP based programs so we will install a LAMP (Linux, Apache, MySQL, PHP) stack. The website stores and retrieves information from databases so we will install and use phpMyAdmin (a web interface).

## Installation
** Root privileges are required for these installations **
### LAMP Stack
1. Install Apache:

  a. Open a terminal session and type the following commands:
  ```
  
  sudo apt-get update
  sudo apt-get install apache2
  
  ```
  b. Check if Apache is installed by finding your IP address and entering it into a web browser. 
  
  Find your IP:
  ```
  ifconfig
  ```
  and use the IP address under eth0. Type it into a web browser and an Apache page should display stating that the installation was a success.
  
2. Install MySQL

  a. Install MySQL server and other requirements:
  ```
  
  sudo apt-get install mysql-server libapache2-mod-auth-mysql php5-mysql
  
  ```
  You will have the option to set a root password. You may do so now or later. 
  
  b. Activate MySQL and finish it up by running the script:
  ```
  
  sudo mysql_install_db
  sudo /usr/bin/mysql_secure_installation
  
  ```
  You will be prompted for a root password. Enter it, and you will again have the option to change it. Change it or press N to continue.
  
  c. The installation will ask you a series of questions. Just press enter for each for a default set up. 
  
3. Install PHP

  a. Install PHP and other requirements:
  ```
  
  sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt
  
  ```
  After you answer yes to the prompt twice, PHP will install itself.
  
  b. Add php to the directory index. Open /etc/apache2/mods-enabled/dir.conf with your favourite text editor (here is vim).
  ```
  
  sudo vim etc/apache2/mods-enabled/dir.conf
  
  ```
  
  c. Add index.php to  the beginning of index files. It should look like this:
  ```
  
  <IfModule mod_dir.c>

          DirectoryIndex index.php index.html index.cgi index.pl index.php index.xhtml index.htm

  </IfModule>
  
  ```
  
  d. (OPTIONAL) You may install PHP modules if you would like them. PHP provides some useful modules. View them with:
  ```
  
  apt-cache search php5-
  
  ```
  
  and to install:
  ```
  
  sudo apt-get install name-of-module
  
  ```
  
That's it! The LAMP stack is now installed. You can verify this by creating a .php file in the /var/www/html directory of your computer. The files in this directory are the only ones you can view in your browser. To view a .php file in your web browser, go to:
```

http://YOUR.IP.ADDRESS/name-of-file.php

```

### phpMyAdmin

We will install phpMyAdmin from a package.

1. Install the package:
  ```

  sudo apt-get install phpmyadmin

  ```

2. To set up under Apache, add this line line to the end of the /etc/apache2/apache2.conf file:
  ```

  Include /etc/phpmyadmin/apache.conf

  ```

3. phpMyAdmin should now be installed. To verify, open a web browser and type in:
  ```

  http://localhost/phpMyAdmin

  ```

That's it! You should now have a web interface for handling databases.




