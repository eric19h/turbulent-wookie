MortgageWare
========================

Welcome to the MortgageWare application. MortgageWare uses the Symfony 2.1 PHP Framework and Bootstrap 2 front end framework

This document contains information on how to download, install, and start
using MortgageWare.

1) Installing Symfony 2.1 Dependencies
----------------------------------

Start off my checking to make sure you have LAMP and Symfony configured correctly.


2) Installing MortgageWare
----------------------------------

When it comes to installing MortgageWare, the primary installation method is through Git.

### Git installation

Make sure an administrator has created a git account for you on the git server and the firewall has been open for your IP.

    git clone ssh://myuser@soulreaper.sudoux.com:5689/server/var/git/mortgageware.git

Then, use the Composer to download and install the dependencies:

    composer update
    
You will be prompted for a password for the dev user on soulreaper.
    
    enter yawRe2Re
    
If an error is thrown "An error occurred when executing the "'cache:clear --no-warmup'" command.". Follow the instructions below:
    
    cd vendor/sudoux/cms
    git pull
    cd ../../..
    
Setting up your website:

    Rename your app/config/parameters.sample.yml to parameters.yml
    Create a blank database
    Modify your database information to the parameters.yml
    
    
Proceed with the following commands:
    
    php app/console assets:install web
    
Run the following commands to create additional directories

    mkdir app/cache app/logs app/spool
    

3) Checking your System Configuration
-------------------------------------

Before starting coding, make sure that your local system is properly
configured for Symfony and MortgageWare.

Execute the `check.php` script from the command line:

    php app/check.php

Access the `config.php` script from a browser:

    http://localhost/path/to/symfony/app/web/config.php

If you get any warnings or recommendations, fix them before moving on.

3) Configuring Supervisor
-------------------------------------

