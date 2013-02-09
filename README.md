# Denton Bible Church Website

**[Read the Documentation](https://github.com/Denton-Bible-Church/Website/wiki)**

This repository contains nearly the entire http://dentonbible.org website with the exception of configuration files and media uploads. See below for more.

## Getting started

First, you will need Git installed on your computer. These guides will help you get setup:

* [Git on Mac](http://guides.beanstalkapp.com/version-control/git-on-mac.html)

* [Git on Windows](http://guides.beanstalkapp.com/version-control/git-on-windows.html)

* [Git on Linux](http://guides.beanstalkapp.com/version-control/git-on-linux.html)

### Request configuration files

Once finished with these steps you'll be able to make file changes, commit and push to the repository, but you won't have a database yet or any DBC content.

Request configuration files from [webmaster@dentonbible.org](mailto:webmaster@dentonbible.org). With these files you'll instantly be linked to the staging database and be able to run the website locally.

### Setup local environemnt

1. Install a local server like MAMP, WAMP, or XAMPP.
2. Edit your `hosts` file to point `127.0.0.1` to `local.dentonbible.org`.
3. Edit the Apache `httpd.conf` file and uncomment the `Include conf/extra/httpd-vhosts.conf` line, if not already.
4. Add the following to the `httpd-vhosts.conf` file:

  ```
	<VirtualHost *:80>
		DocumentRoot "path/to/where/you/want/the/repository"
		ServerName local.dentonbible.org
		ServerAlias local.dentonbible.org  admin.local.dentonbible.org
		<Directory "path/to/where/you/want/the/repository">
			Options Indexes FollowSymLinks ExecCGI Includes
			Order allow,deny
			Allow from all
		</Directory>
	</VirtualHost>
	```
    
5. Start (or restart) Apache.

### Clone and initiate the repository

Open up Terminal

    $ cd path/to/where/you/want/the/repository
    $ git clone https://github.com/Denton-Bible-Church/Website.git
    $ git submodule init
    $ git submodule update
    $ cd wp
    $ git pull
