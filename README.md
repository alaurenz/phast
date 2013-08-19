Last updated: August 18, 2013

Public Health Automatic System for Translation (PHAST) is a web-based
collaborative translation management system to enable public health workers 
to use machine translation for creating multilingual health materials.
It was originally conceived for use by public health workers, however the
software is general in scope and is suitable for any user interested in 
using machine-translation software to assist in translating written 
materials in a collaborative environment.

Please cite us at:  
A. Laurenzi, M. Brownstein, A.M. Turner, J.A. Kientz and K. Kirchhoff (2013) 
"A Web-Based Collaborative Translation Management System for Public Health 
Workers", Proceedings of CHI '13, pp. 511-516.

In this README you will find instructions for installing the application as well 
as information for developers interested in building on the application. See the 
LICENSE file in this same directory for the terms of redistribution and use. 

If you have any questions/comments/requests, please contact the author:

Adrian Laurenzi  
adrian.laurenzi@gmail.com


Contents
================================================================================

1. Installation guide  
    [1.1 Linux](#11-linux)  
    [1.2 Mac OS X](#12-mac-os-x)  
    [1.3 Windows XP/Vista/7](#13-windows-xpvista7)  
2. Contributing to the project  
    [2.1 General information for developers](#21-general-information-for-developers)  
    [2.2 Application structure](#22-application-structure)  
    [2.3 Microsoft Translate API](#microsoft-translate-api)  
    [2.4 Version control using GitHub](#version-control-using-github)  
3. [Unit Testing](#3-unit-testing)


1. Installation guide
================================================================================

--------------------------------------------------------------------------------
1.1 Linux
--------------------------------------------------------------------------------

1.1.1 Software dependencies 
----------------------------------------

Apache, PHP, and MySQL are required to run this application. This software
bundle is known as LAMP and there are many guides available that describe how to 
install it on Linux. Here are guides for a few of the most popular Linux distros:  
- Ubuntu: https://help.ubuntu.com/community/ApacheMySQLPHP  
- Fedora: http://fedorasolved.org/server-solutions/lamp-stack  
- Debian: http://wiki.debian.org/LaMp  

NOTE: If you are installing Apache, PHP, and MySQL individually it is probably 
easiest to install them in the following order: MySQL -> Apache -> PHP.

----------------------------------------

- Apache HTTP Server (version 1 or 2)
  Although the application should work on Apache 1 we highly recommend using the
  Apache 2, the most current version. You should follow the instructions for 
  installing PHP and Apache side-by-side according to the PHP installation guide
  provided below. The official Apache website is here: http://httpd.apache.org

- PHP (>= version 5.3)  
  UNIX installation guide for PHP/Apache:  
  http://www.php.net/manual/en/install.unix.php  
  Downloads: http://www.php.net/downloads.php  
  ***IMPORTANT NOTE: Be sure to install the cURL extension with PHP***

- MySQL Community Server  
  You should install each of the following MySQL Community Server packages:
  server, client, devel, and shared. Each of these packages can be downloaded 
  here: http://dev.mysql.com/downloads/mysql#downloads

- Kohana PHP Framework v3 (included in this package)  
  Official website: http://kohanaframework.org

- PHPDOCX Community  
  Official website: http://www.phpdocx.com

Other software included in this package that DOES NOT require installation:

- JQuery  
  Official website: http://jquery.com

- Twitter Bootstrap  
  Official website: http://twitter.github.io/bootstrap


1.1.2 Copying files
----------------------------------------

After installing MySQL, PHP, and Apache unzip the application package into the
your Apache DocumentRoot (the root HTTP directory of the web server). The 
DocumentRoot path should be listed in your apache configuration file which, if 
you are using apache2, should be listed in /usr/local/apache2/conf/httpd.conf 
or, if you created a new site, in the config file located in this directory:
/etc/apache2/sites-enabled

NOTE: Depending on your platform, the installation's subdirs may have lost 
their permissions due to the zip extraction. Chmod them all to 755 by running 
the following command from within the root directory of your application:
    
    find . -type d -exec chmod 0755 {} \;

In this guide ~/ is the root directory of the application.

The following directories MUST be made writeable:
~/docs
~/docs/download

Use the following UNIX command to make each of the above directories writeable:
    
    chmod 0755 <DIRECTORY>

We recommend that you do not modify the default directory structure
but if you choose to do so you must modify the application's configuration 
settings described in the 'Configuration settings' section (1.1.6).


1.1.3 Importing MySQL tables
----------------------------------------

To import the MySQL tables needed by the application you must import the 
mysql_tables.sql file provided in the root directory of this package.

First open the MySQL shell and enter your password:
    
    mysql -u <USERNAME> -p 

Replace <USERNAME> with your MySQL username (most likely this will be 'root')

While in the shell create a new MySQL database:
    
    create database <DATABASE NAME>;

Replace <DATABASE NAME> with whatever you wish to name your database.
 
Exit the MySQL shell (type 'exit') and import the MySQL tables using the 
following command:
    
    mysql -u <USERNAME> -p <DATABASE NAME> < mysql_tables.sql

It may be useful to install PHPMyAdmin to manage your MySQL database, however 
this is not essential. Download PHPMyAdmin here: http://www.phpmyadmin.net


1.1.4 Setting up Kohana (version 3)
----------------------------------------

NOTE: You may need to switch "short_open_tag = On" in your PHP.ini configuration 
file and restart Apache. If it is set to "Off", Kohana may not work correctly.

For your convenience we have included the Kohana PHP Framework version 3.0.7
in this software package. Instructions for setting up Kohana 3.0.7 (included 
in this package) are described below according to those described in official 
Kohana installation guide found on their website: 
http://kohanaframework.org/3.0/guide/kohana/install

(1) Open ~/kohana/application/bootstrap.php.example and make the following 
    changes:
    - Set the default timezone for your application.
    - Set the base_url in the Kohana::init call to reflect the location of the 
      kohana folder on your server.
    After making the changes save it the file as bootstrap.php in the same
    directory.

(2) Make sure the ~/kohana/application/cache and ~/kohana/application/logs 
    directories exist and are writable by the web server (chmod to 0755).

(3) Open ~/kohana/install.php.example and save it as install.php in the same
    directory.

(4) Test your installation by opening the URL you set as the base_url in a 
    browser. You should see the installation page. If it reports any errors, you 
    will need to correct them before continuing.
    ***IMPORTANT NOTE: it is required that cURL is enabled in PHP. So it 
    must say "Pass" next to "cURL Enabled" under "Optional Tests". If it does 
    not you must install cURL into PHP: http://php.net/manual/en/book.curl.php

(5) Once your install page reports that your environment is set up correctly you 
    need to either rename or delete install.php in the root directory.

(6) To configure the MySQL database module copy the database.php file from 
    ~/kohana/modules/database/config into the ~/kohana/application/config 
    directory. Open the newly copied file and edit the settings to point to your 
    MySQL database (hostname, username, password, and database).

(7) To make the application accessible from the root directory of the 
    application rather than ~/kohana you must rename ~/index.php.tmp to 
    index.php and delete ~/kohana/index.php. Then open 
    ~/kohana/application/bootstrap.php again and set the base_url to the root 
    directory of the application.

NOTE: by default Kohana is configured to be in 'development' mode so if you plan
to make this application available on a public web server for security reasons
you should put Kohana in 'production' mode:
http://kohanaframework.org/3.0/guide/kohana/security/deploying


1.1.6 Setting up Microsoft Translator API
----------------------------------------

The application uses the Microsoft Translator API to perform machine translations.
It is free to use but in order to enable it you must sign up at the Windows
Azure Marketpace and obtain authentication credentials. The following instructions
for obtaining an access token for the Translator API were taken from:
http://msdn.microsoft.com/en-us/library/hh454950.aspx

**Step 1: Subscribe to Microsoft Translator API at Windows Azure Marketplace**  
Visit this page: https://datamarket.azure.com/dataset/1899a118-d202-492c-aa16-ba21c33c06cb

To sign up for free API access scroll down to the bottom and click Sign Up next to 
the plan that says $0.00 per month. A direct link to Sign Up for the free plan is here:  
https://datamarket.azure.com/checkout/21075018-a3b5-4254-8ff2-1ec48002a4ec?ctpa=False

You will be prompted to sign in. If you do not already have a Microsoft Account you
will need to sign up for one. After logging in fill out the registration details 
for Windows Azure Marketplace and complete signing up for the Translator API service.

**Step 2: Register your application with Azure DataMarket**  
To register your application with Azure DataMarket, visit: https://datamarket.azure.com/developer/applications/ 
and login using your Microsoft Account / LiveID credentials from Step 1, then click on 
"Register". In the "Register your application" form, you must define your own 
Client ID and Name. These can be anything you like but copy the Client ID you use 
and also copy down the Client secret as you will need both of these for configuring 
the application when setting Configuration settings (section 1.1.8 below). You can 
fill in the redirect URI to anything as it will not affect the behavior of the 
application.


1.1.7 Installing PHPDOCX
----------------------------------------

PHPDOCX is a required dependency and installation is simple. Download the free 
community version of PHPDOCX here:
http://www.phpdocx.com/sites/default/files/phpdocx_community.tar.gz

If the above link does not work search for the community version download at 
http://www.phpdocx.com/

Unzip `phpdocx_community.tar.gz` and put it into the root directory of the 
application (~/phpdocx_community). Ensure that this path is used when you
set the Configuration settings (section 1.1.8 below).


1.1.8 Configuration settings
----------------------------------------

Configuration settings for the application are established in this file:
~/kohana/application/config/mainconf.php.example

You will have to modify all URLs and paths in this file in order for 
your application to work properly. You must change all file paths in 
mainconf.php.example to point to the full path to the root directory of the 
application. Replace '~/' with the full path to the root directory of your 
application. To find the full path open a terminal and go to the root directory 
of your application and enter the 'pwd' command. All URLs should begin with the 
HTTP path to the application (for example http://localhost/ if your application 
is in the root directory of your web server). All URLs and path should NOT 
include a trailing slash ('/') at the end.

You also need to set the clientID and clientSecret fields to those obtained
when setting up the Microsoft Translator API (section 1.1.6).

After making all the necessary changes to mainconf.php.example save the file 
as mainconf.php in the same directory.


1.1.9 Running the application
----------------------------------------

Once everything has been set up open the application in a web browser. 
http://localhost/kohana should work if you set up Apache with defaults. An
admin account exists with username 'admin' and password 'admin' that you can
use to login to the system.

To learn about how to use the application refer to the Help documentation:
http://localhost/kohana/index.php/help

--------------------------------------------------------------------------------
1.2 Mac OS X
--------------------------------------------------------------------------------

This application has not yet be tested on Mac OS X but all the required
software is available for Mac OS X so it should not be difficult to set up. 
After getting the software dependencies below installed you should be able to 
follow the Linux instructions above to finish the setup (starting at section
1.1.2). If you can provide more detailed instructions set up on Mac OS X please 
add them to this README and upload the changes to the github repository.


1.2.1 Software dependencies (Mac OS X)
----------------------------------------

- Apache HTTP Server (version 1 or 2)  
  You should follow the instructions for installing PHP and Apache side-by-side 
  according to the PHP installation guide provided below. The official Apache 
  website is here: http://httpd.apache.org/

- PHP (>= version 5.3)  
  Mac OS X installation guide for PHP/Apache:  
  http://www.php.net/manual/en/install.macosx.php  
  Downloads: http://www.php.net/downloads.php

- MySQL Community Server  
  You should install each of the following MySQL Community Server packages:
  server, client, devel, and shared. Each of these packages can be downloaded 
  for Windows here: http://dev.mysql.com/downloads/mysql#downloads

- Kohana PHP Framework v3 (included in this package)  
  Official website: http://kohanaframework.org

- PHPDOCX Community  
  Official website: http://www.phpdocx.com

Other software included in this package that DOES NOT require installation:

- JQuery  
  Official website: http://jquery.com

- Twitter Bootstrap  
  Official website: http://twitter.github.io/bootstrap


--------------------------------------------------------------------------------
1.3 Windows XP/Vista/7
--------------------------------------------------------------------------------

This application has not yet be tested on Windows but all the required
software is available for Windows XP (possibly also Vista or Windows 7) so you 
should be able to get it set up. Windows XP/Vista/7 should all run Apache, PHP, 
and MySQL. If you can provide instructions set up on Windows please add them to 
this README and upload the changes to the github repository.


1.3.1 Software dependencies (Windows)
----------------------------------------

- Apache HTTP Server (version 1 or 2)  
  You should follow the instructions for installing PHP and Apache side-by-side 
  according to the PHP installation guide provided below. The official Apache 
  website is here: http://httpd.apache.org/

- PHP (>= version 5.3)  
  Windows installation guide for PHP/Apache:  
  http://www.php.net/manual/en/install.windows.php
  Windows downloads: http://windows.php.net/download/

- MySQL Community Server  
  You should install each of the following MySQL Community Server packages:
  server, client, devel, and shared. Each of these packages can be downloaded 
  for Windows here: http://dev.mysql.com/downloads/mysql#downloads

- Kohana PHP Framework v3 (included in this package)  
  Official website: http://kohanaframework.org

- PHPDOCX Community  
  Official website: http://www.phpdocx.com

Other software included in this package that DOES NOT require installation:

- JQuery  
  Official website: http://jquery.com

- Twitter Bootstrap  
  Official website: http://twitter.github.io/bootstrap


================================================================================
2. Contributing to the project
================================================================================

--------------------------------------------------------------------------------
2.1 General information for developers
--------------------------------------------------------------------------------

This application is written using the Kohana PHP Framework (version 3) which is 
an HMVC framework. To contribute in the development of this application you will
have to familiarize with this framework. We chose this framework because it is 
open source, under active development, lightweight, secure, and relatively easy 
to learn. Be sure to learn Kohana version 3 (KO3) and not version 2, the older
version.

Primary Kohana resources:  
- User Guide: http://kohanaframework.org/3.0/guide/  
- API User Guide: http://kohanaframework.org/3.0/guide/api  
- Unofficial wiki: http://kerkness.ca/kowiki/doku.php  
- Community forum: http://forum.kohanaframework.org/  

Kohana is a popular framework so there are lots of resources available on the 
Internet so don't be afraid to search around. But be aware that a lot of the 
existing guides are for Kohana version 2.


--------------------------------------------------------------------------------
2.2 Application structure
--------------------------------------------------------------------------------

Important files & directories (~/ is the root directory of the application):

~/kohana/application/classes/controller - Main code for application  
~/kohana/application/classes/model - Generally used to communicate with database  
~/kohana/application/config - URL/path, Microsoft Translator, and database configuration settings  
~/kohana/application/messages - Error messages  
~/kohana/application/views/template.php - Page header & footer  
~/kohana/application/views/pages - Page content  
~/kohana/modules -  
    Potentially useful Kohana modules (enable these by modifying
    ~/kohana/application/bootstrap.php)  
~/kohana/system - Kohana system files; in general these should not be modified  
~/docs - All documents uploaded into the system are stored here  
~docs/download -  
    A directory used to store files that are generated temporarily when the 
    user downloads document while using the application
~/bootstrap - Files for Twitter Bootstrap  
~/jquery-ui-1.9.0 - Files for JQuery and JQuery UI  
~/extra.js - Miscellaneous 'global' JavaScript code  
~/extra.css - Miscellaneous 'global' CSS code  
~/images  


--------------------------------------------------------------------------------
2.3 Microsoft Translator API
--------------------------------------------------------------------------------

PHAST performs machine translations using the Microsoft Translate API. More 
information is available here:  
http://www.microsofttranslator.com/dev/

The application supports using the generic transltion engine or you can 
optionally train a custom translation model using Microsoft Translator HUB. 
More information here:  
http://research.microsoft.com/en-us/projects/microsofttranslatorhub/

Within the Admin Panel (http://phastsystem.org/index.php/admin) you can set a 
default translation model to your customized model.


--------------------------------------------------------------------------------
2.4 Version control using GitHub
--------------------------------------------------------------------------------

The GitHub repository for this application is located here:

https://github.com/alaurenz/phast

If you are interested in collaborating on the project please contact the 
author at: adrian.laurenzi [at] gmail.com

If you are unfamiliar with git or GitHub you may wish to read the following
before working with the repository:

- Forking the repository to build on the project:  
  https://help.github.com/articles/fork-a-repo  
- Using SSH keys with GitHub:  
  https://help.github.com/articles/working-with-ssh-key-passphrases  
- Dealing with newline character issues:  
  https://help.github.com/articles/dealing-with-line-endings  
- Good, comprehensive tutorial on git:  
  http://git-scm.com/book


================================================================================
3. Unit Testing
================================================================================

Unit tests are implemented using a module that comes with Kohana called 
unittest. The module comes with this installation and exists in this directory:  
~/kohana/modules/unittest

Currently only the unit testing framework is in place and unit tests still need
to be written to test the application thoroughly. Please help by writing unit 
tests.

--------------------------------------------------------------------------------
3.2 Software dependencies
--------------------------------------------------------------------------------

You should be able to run the unittest module on Linux, Mac, and Windows 
systems because the only dependency is PHPUnit which can be installed on all
three platforms.

- PHPUnit (>= version 3.4):  
  https://github.com/sebastianbergmann/phpunit  
  We recommend using PEAR (http://pear.php.net) to install PHPUnit which is 
  discussed in the PHPUnit README.


--------------------------------------------------------------------------------
3.3 Running unit tests
--------------------------------------------------------------------------------

To enable the Kohana unittest module simple open:
~/kohana/application/bootstrap.php

and uncomment the following line to enable the unittest module:
```php
'unittest' => MODPATH.'unittest',  // PHPUnit integration
```
WARNING: when this line is uncommented the application will not function. 
The application will resume functioning after recommenting out the line.

To run all the unit tests run the following commands:
```
cd ~/kohana/application/tests
phpunit
```

--------------------------------------------------------------------------------
3.1.3 Adding unit tests
--------------------------------------------------------------------------------

Good intro for how to write tests:  
http://embrangler.com/2010/04/introduction-to-testing-in-kohana-3/#writing-tests  
http://embrangler.com/2010/04/introduction-to-testing-in-kohana-3/#test-suites

Might want to try to install directly from:  
https://github.com/kohana/unittest


