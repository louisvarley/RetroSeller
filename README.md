# Welcome to Retro Seller

This is a simple stock, purchase, sales manager i built for personal use


Uses https://github.com/daveh/php-mvc as its MVC engine as its simple and quick to get building. 

Doctrine handles entities and database

I've avoided using AJAX calls .. no idea why really

# Concept

This handles the concept of 2 or more people, who buy things, and sell them...

IE Person A buys Item 1, Person B buys a replacement part to fix Item 2

When Item 1 sells, the costs that Person A and B have paid in (expenses) are paid back, and profit from the sale is split to 
each persons accounts

Each account has a balance and the amount can be withdrawn from the account as needed. (assuming everyone is using a shared bank account for sales)

# Install

- Clone this repo and setup to run under PHP7/8
- Ensure you have the following PHP Extensions installed and enabled [ext-zip,ext-dom,ext-gd,ext-curl,ext-mbstring]
- run `composer update` to download all required components
- run `./.update.sh` to prepare all entities
- When first launching, you will be asked to choose your initial user details
- You will be asked for your database details (MySQL)
- Setup will install the database schema and when complete you will be free to login 

# Updating

- If you pull a newer version and it includes any schema changes these are only applied if you run `/setup` again
- Setup will automatically create a backup of your current database in a `dumps` folder
- You can also run `.update.sh` Manually to install the new schema changes to your database

# Additional Notes

- `.update.sh` will manually update without using the WebUI
- `.composer.sh` runs a composer dump autoload, loading any changes to internal classes into composer

# Manual Backup and Dumping

- `php dump.php` will manually create a dump of your database