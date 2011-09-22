# Awesome IRC Bot Framework
#### Powerful, User Friendly PHP IRC Bot Framework
#### Created by AwesomezGuy
#### Follow me on [Twitter](http://twitter.com/AwesomezGuy)
#### v0.4.0

Introduction
-------------
Awesome IRC Bot is a powerful framework which I have created for running a stable PHP IRC Bot. 
With easily customizable features such as modules, it's simple to use, yet has the capabilities for developers to hook advanced plugins into.

Beta Notice
-------------
The bot is stable, however it is lacking a few features and there will be a fair bit more work done before v1.0.
You can consider any version after v0.3.0 to be safe for use in a production environment.
I recommend only using tagged versions, and upgrading with each new tag.

Prerequisites
-------------
* PHP 5.3+ CLI (will NOT run on 5.2) (If you use Ubuntu/Debian, apt-get probably won't have PHP 5.3 unless you modify your source list)
* UNIX based system for backgrounding
* SQLite PDO Extension (apt-get install php5-sqlite)

Installation
-------------
1. Copy all the files to a directory of your choice
2. Rename "config/config.example.php" to "config/config.php" and edit it

Startup with Backgrounding (UNIX based systems ONLY, e.g. Mac OS X, Ubuntu, Debian, CentOS, etc.)
-------------
1. Navigate to the directory where the script is stored in a shell
2. Type "php start.php" into the shell and hit Enter

Startup
-------------
1. Navigate to the directory where the script is stored in a shell
2. Type "php bot.php" into the shell and hit Enter

Installing Modules From The modules-extras Folder
-------------
The /modules-extras/ folder contains contributed and other non essential modules for your use.
To install a module pack, follow the instructions below:

1. Copy the module folder you want from /modules-extras/ into /modules/
2. Restart the bot

Installing Other Modules
-------------
If a developer has sent you a module, or you've found it on the internet somewhere, follow the below instructions to install it

1. Copy the module folder into /modules/
2. Restart the bot

Please note that while modules in the /modules-extras/ folder have been checked and fixed to make absolutely sure drag and drop installation will work, this is not the case for modules you may obtain from other sources.

Uninstalling Modules
-------------
To uninstall a module set, follow the instructions below

1. Delete the module folder from /modules/
2. Restart the bot

Using the Bot
-------------
Type .help on a channel the bot is on (replace . with your command prefix), to get information on the commands and functions available for use

Legal
-------------
By using Awesome IRC Bot, you agree to the license in LICENSE.md