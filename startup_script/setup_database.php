<?php
/*
 * Script for setting up MySql database for term project
 * Simply run in the command line using php
 * On windows with wamp64, your php executable is located at /c/wamp64/bin/php/php7.3.1/php
 * run script like: /c/wamp64/bin/php/php7.3.1/php setup_database.php
 * the database.php file in this directory needs your root password in order to connect and create everything -- mine is blank so it is currently blank
*/

require_once('database.php');

// create shopping_db database
$query = "DROP DATABASE IF EXISTS shopping_db";
Database::runQuery($query);

$query = "CREATE DATABASE shopping_db";
Database::runQuery($query);

// create admin user for shopping_db database
$query = "DROP USER IF EXISTS 'admin'@'localhost'";
Database::runQuery($query);

$query = "CREATE USER 'admin'@'localhost' IDENTIFIED BY 'admin_password'";
Database::runQuery($query);

$query = "GRANT ALL PRIVILEGES ON shopping_db.* TO 'admin'@'localhost'";
Database::runQuery($query);

// setup products table
$query = "CREATE TABLE shopping_db.products (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 name varchar(255),
			 description varchar(255),
			 price decimal(13,2),
			 image_path varchar(255)
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.products
			(name, description, price, image_path)
          VALUES
			('Laptop', 'Lenovo Ideapad', 300.25, 'lenovo-ideapad.png'),
			('USB', '8gb', 12.50, 'usb.jpg'),
			('Joystick', 'Atari', 8.99, 'atari-joystick.jpg')";
Database::runQuery($query);

// setup customers table
$query = "CREATE TABLE shopping_db.customers (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 user_id int not null,
			 first_name varchar(255),
			 last_name varchar(255),
			 email varchar(255),
			 
			 FOREIGN KEY fk_user_id(user_id)
			 REFERENCES shopping_db.users(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.customers
			(user_id, first_name, last_name, email)
          VALUES
			(1, 'Brian', 'Carducci', 'mcbroiz@qu.edu'),
			(3, 'Arden', 'Ricciardone', 'wisdomteeth4sale@notvirgina.edu')";
Database::runQuery($query);

// setup carts table
$query = "CREATE TABLE shopping_db.carts (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 customer_id int UNIQUE,
		 
			 FOREIGN KEY fk_customer_id(customer_id)
			 REFERENCES shopping_db.customers(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.carts
			(customer_id)
          VALUES
			(1)";
Database::runQuery($query);

// setup carts_products table
$query = "CREATE TABLE shopping_db.carts_products (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 cart_id int,
			 product_id int,
			 quantity int,
		 
			 FOREIGN KEY fk_cart_id(cart_id)
			 REFERENCES shopping_db.carts(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT,
			 
			 FOREIGN KEY fk_product_id(product_id)
			 REFERENCES shopping_db.products(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.carts_products
			(cart_id, product_id, quantity)
          VALUES
			(1, 1, 1)";
Database::runQuery($query);

// setup products_store table
$query = "CREATE TABLE shopping_db.products_store (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 product_id int UNIQUE,
			 quantity int,
		 
			 FOREIGN KEY fk_product_id(product_id)
			 REFERENCES shopping_db.products(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.products_store
			(product_id, quantity)
          VALUES
			(1, 10),
			(2, 50),
			(3, 8)";
Database::runQuery($query);

// setup billing_information table
$query = "CREATE TABLE shopping_db.billing_information (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 customer_id int not null,
			 country varchar(255),
			 address_1 varchar(255),
			 address_2 varchar(255),
			 city varchar(255),
			 state varchar(255),
			 zip_code varchar(5),
			 card_type varchar(255),
			 card_name varchar(255),
			 card_last_four_digits varchar(4),
			 card_hash varchar(255),
			 
			 FOREIGN KEY fk_customer_id(customer_id)
			 REFERENCES shopping_db.customers(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.billing_information
			(customer_id, country, address_1, address_2, city, state, zip_code, card_type, card_name, card_last_four_digits, card_hash)
          VALUES
			(1, 'United States', '304 Longmeadow Rd', '', 'Orange', 'CT', '06477', 'Visa', 'Brian Carducci', '0000', 'abj47slk09487dfsuiosd')";
Database::runQuery($query);

// setup shipping_information table
$query = "CREATE TABLE shopping_db.shipping_information (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 customer_id int not null,
			 country varchar(255),
			 address_1 varchar(255),
			 address_2 varchar(255),
			 city varchar(255),
			 state varchar(255),
			 zip_code varchar(5),
			 name varchar(255) UNIQUE,
			 
			 FOREIGN KEY fk_customer_id(customer_id)
			 REFERENCES shopping_db.customers(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.shipping_information
			(customer_id, country, address_1, address_2, city, state, zip_code, name)
          VALUES
			(1, 'United States', '304 Longmeadow Rd', '', 'Orange', 'CT', '06477', 'home')";
Database::runQuery($query);

// setup orders table
$query = "CREATE TABLE shopping_db.orders (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 customer_id int not null,
			 billing_information_id int not null,
			 shipping_information_id int not null,
			 order_timestamp datetime not null, 
			
			 FOREIGN KEY fk_customer_id(customer_id)
			 REFERENCES shopping_db.customers(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT,

			 FOREIGN KEY fk_billing_information_id(billing_information_id)
			 REFERENCES shopping_db.billing_information(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT,
			 
			 FOREIGN KEY fk_shipping_information_id(shipping_information_id)
			 REFERENCES shopping_db.shipping_information(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.orders
			(customer_id, billing_information_id, shipping_information_id, order_timestamp)
          VALUES
			(1, 1, 1, '2019-2-11 4:30:07'),
			(1, 1, 1, '2019-1-1 13:10:30')";
Database::runQuery($query);

// setup orders_products table
$query = "CREATE TABLE shopping_db.orders_products (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 order_id int not null,
			 product_id int not null,
			 quantity int not null,
			
			 FOREIGN KEY fk_order_id(order_id)
			 REFERENCES shopping_db.orders(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT,
			 
			 FOREIGN KEY fk_product_id(product_id)
			 REFERENCES shopping_db.products(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.orders_products
			(order_id, product_id, quantity)
          VALUES
			(1, 2, 2),
			(2, 1, 1),
			(2, 3, 3)";
Database::runQuery($query);

// setup roles table
$query = "CREATE TABLE shopping_db.roles (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 role varchar(255) not null
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.roles
			(role)
          VALUES
			('customer'),
			('admin')";
Database::runQuery($query);

// setup users table
$query = "CREATE TABLE shopping_db.users (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 login_hash varchar(255) not null
		 )";
Database::runQuery($query);

$login_hash_1 = '04d4142f6a79d0ac0bf86ea3d7d362d8'; // username: customer1 | password: imacustomer
$login_hash_2 = '8f9203fc7c1a168a480cab6619db00af'; // username: admin1 | password: imanadmin
$login_hash_3 = 'ed62e4f6557c0d0ef8f8712f17934862'; // username: customer2 | password: imacustomer2
$query = "INSERT INTO shopping_db.users
			(login_hash)
          VALUES
			(:login_hash1),
			(:login_hash2),
			(:login_hash3)";
$bind_values = [
	":login_hash1" => $login_hash_1,
	":login_hash2" => $login_hash_2,
	":login_hash3" => $login_hash_3
];
Database::runQuery($query, $bind_values=$bind_values);

// setup users_roles table
$query = "CREATE TABLE shopping_db.users_roles (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 user_id int not null,
			 role_id int not null,
			 
			 FOREIGN KEY fk_user_id(user_id)
			 REFERENCES shopping_db.users(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT,
			 
			 FOREIGN KEY fk_role_id(role_id)
			 REFERENCES shopping_db.roles(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.users_roles
			(user_id, role_id)
          VALUES
			(1, 1),
			(2, 2),
			(3, 1)";
Database::runQuery($query);

// setup admins table
$query = "CREATE TABLE shopping_db.admins (
			 id int not null AUTO_INCREMENT PRIMARY KEY,
			 user_id int not null,
			 first_name varchar(255),
			 last_name varchar(255),
			 email varchar(255),
			 
			 FOREIGN KEY fk_user_id(user_id)
			 REFERENCES shopping_db.users(id)
			 ON UPDATE CASCADE
			 ON DELETE RESTRICT
		 )";
Database::runQuery($query);

$query = "INSERT INTO shopping_db.admins
			(user_id, first_name, last_name, email)
          VALUES
			(2, 'Callie', 'Thimineur', 'calleus@gmail.com')";
Database::runQuery($query);

?>