# Welcome to Retro Seller

This is a simple stock, purchase, sales manager i built for personal use

Uses https://github.com/daveh/php-mvc as its MVC engine as its simple and quick to get building. 

Doctrine handles entities and database

# Concept

This handles the concept of 2 or more people, who buy things, and sell them...

IE Person A buys Item 1, Person B buys a replacement part to fix Item 2

When Item 1 sells, the costs that Person A and B have paid in (expenses) are paid back, and profit from the sale is split to 
each persons accounts

Each account has a balance and the amount can be withdrawn from the account as needed. (assuming everyone is using a shared bank account for sales)

# Install via Apache

- Clone this repo and setup to run under PHP7/8
- Ensure you have the following PHP Extensions installed and enabled [ext-zip,ext-dom,ext-gd,ext-curl,ext-mbstring]
- run `composer update` to download all required components
- When first launching, you will be asked to choose your initial user details
- You will be asked for your database details (MySQL) If the database does not already exist, it will be created (if user has permission)
- Setup will install the database schema and when complete you will be free to login 

# Install via Docker
- Clone only the 'Docker' folder from this repo 
- run `docker build --no-cache -t retroseller .` to create your image, this takes about 5 minutes.
- run `docker-compose up -d --build --force-recreate` to create your two containers
- open http://0.0.0.0:9092 

Initial Login details for the docker version are

- Username `admin@retroseller.com`
- Password `admin`

# Updating

- When a new version is avaliable from this repo, you can update from settings / update
- Update will automatically create any new schema changes as well as new composer dependencies
- It is HIGHLY recomended you use settings / backup database, to create a SQL dump of your data before any updates

# Additional Notes

- `.update.sh` will manually update without using the WebUI
- `.composer.sh` runs a composer dump autoload, loading any changes to internal classes into composer

# Manual Database Import

- If you have made a backup, you can import this using standard MySQL Import processes or MySQL Workbench. If your backup is missing schema changes, RetroSeller will create the missing tables for you.