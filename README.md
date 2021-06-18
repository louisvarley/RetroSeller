# Welcome to Retro Seller

This is a simple stock, purchase, sales manager i built for personal use


Uses a https://github.com/daveh/php-mvc as its MVC engine as its simple and quick to get building. 

Doctrine handles entities and database

I've avoided using AJAX calls .. no idea why really

# Concept

This handles the concept of 2 or more people, who buy things, and sell them...

IE Person A buys Item 1, Person B buys a replacement part to fix Item 2

When Item 1 sells, the costs that Person A and B have paid in (expenses) are paid back, and profit from the sale is split to 
each persons accounts

Each account has a balance and the amount can be withdrawn from the account as needed. (assuming everyone is using a shared bank account for sales)