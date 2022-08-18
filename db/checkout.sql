SET FOREIGN_KEY_CHECKS=0;

--
-- users table
--
-- Role = 1 --> admin
-- Role = 2 --> user
-- Role = 3 --> NOT USED
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	first_name VARCHAR(32) NOT NULL,
	last_name VARCHAR(32) NOT NULL,
	gender TINYINT(1) NOT NULL,
	phone VARCHAR(12) UNIQUE NOT NULL,
	email VARCHAR(64) UNIQUE,
	password VARCHAR(255) NOT NULL,
	is_active TINYINT DEFAULT 0,
	is_blocked TINYINT DEFAULT 0,
	photo VARCHAR(255),
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	verified_at TIMESTAMP NULL DEFAULT NULL,
	activated_at TIMESTAMP NULL DEFAULT NULL,

	role TINYINT(1) NOT NULL,

	INDEX(first_name, last_name),
	INDEX(phone),
	INDEX(email),

	PRIMARY KEY(id)
)ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- coupons table
--
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	coupon VARCHAR(16),
	value INT UNSIGNED NOT NULL,
	is_active TINYINT(1) DEFAULT 0,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(id)
)ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- storages table
-- a storage place where application can dump contnet with key as indentifier
-- multiple storage can be handled
--
DROP TABLE IF EXISTS `storages`;
CREATE TABLE `storages` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	storage VARCHAR(64) NOT NULL UNIQUE, 

	PRIMARY KEY(id)
)ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- store_items table
-- place for each storage to store items in the storage
-- multiple storage can be handled
--
DROP TABLE IF EXISTS `store_items`;
CREATE TABLE `store_items` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`storage_id` INT UNSIGNED NOT NULL,
	`skey` VARCHAR(64) NOT NULL,
	`value` VARCHAR(8192),

	INDEX(storage_id, `skey`),
	UNIQUE KEY(`storage_id`, `skey`),

	PRIMARY KEY(id),
	FOREIGN KEY (storage_id) REFERENCES storages(id)
)ENGINE=InnoDB CHARACTER SET=utf8mb4;


--
-- categories table
--
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	category VARCHAR(64) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(id)
)ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- products table
-- Here product is just a package containing courses
-- One product can have multiple courses
--
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	title VARCHAR(256) NOT NULL,
	subtitle VARCHAR(256),
	description VARCHAR(512),
	price_mp INT NOT NULL,
	price_sp INT NOT NULL,
	discount INT DEFAULT 0,
	slug VARCHAR(256) NOT NULL,
	image VARCHAR(64),
	category_id INT UNSIGNED NOT NULL,
	is_returnable TINYINT(1) DEFAULT 0,

	INDEX(title),
	INDEX(category_id),
	INDEX(slug),
	FULLTEXT(title, description),

	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	published_at DATETIME DEFAULT NULL,

	PRIMARY KEY(id),
	FOREIGN KEY(category_id) REFERENCES categories(id)
)ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- orders table
-- order_hash is for razory pay receipt id
-- status = 0 = created, 1 = success, 2 = failed, 3 = processed
-- is_open represents order is open for consideration in Point Activation Target calculation
--   default is closed (i.e. 0)
-- is_repurchase_order: 0 = JPKit order, 1 = regular order (loose/repurchase)
--
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	amount INT UNSIGNED NOT NULL,
	gross_total INT UNSIGNED NOT NULL DEFAULT 0,
	discount INT UNSIGNED NOT NULL,
	sub_total INT UNSIGNED NOT NULL,
	tax INT UNSIGNED NOT NULL DEFAULT 0,
	shipment INT UNSIGNED NOT NULL DEFAULT 0,
	rzp_order_id VARCHAR(64),
	status INT DEFAULT 0,
	user_id INT UNSIGNED NOT NULL,
	applied_coupon_id INT UNSIGNED DEFAULT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	INDEX(rzp_order_id),
	INDEX(created_at),
	INDEX(status),
	INDEX(user_id),

	PRIMARY KEY(id),
	FOREIGN KEY (user_id) REFERENCES users(id),
	FOREIGN KEY (applied_coupon_id) REFERENCES coupons(id)
)ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- order_items table
-- status: 1 = created, 2 = cancellation requested, 4 = cancelled, 8 = shipped
--         16 = delivered, 32 = return requested, 64 = returned
--         128 = refund requested, 256 = refunded, 512 = finished processing
-- flags: contains all the state an order_item went through
--
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	order_id INT UNSIGNED NOT NULL,
	product_id INT UNSIGNED NOT NULL,
	user_id INT UNSIGNED NOT NULL,
	qty INT NOT NULL,
	price_mp INT UNSIGNED NOT NULL DEFAULT 0,
	price_sp INT UNSIGNED NOT NULL DEFAULT 0,
	discount INT UNSIGNED NOT NULL DEFAULT 0,
	status SMALLINT UNSIGNED DEFAULT 0,
	flags SMALLINT UNSIGNED DEFAULT 0,
	shipped_at DATETIME DEFAULT NULL,
	delivered_at DATETIME DEFAULT NULL,
	cancellation_requested_at DATETIME DEFAULT NULL,
	cancelled_at DATETIME DEFAULT NULL,
	return_requested_at DATETIME DEFAULT NULL,
	returned_at DATETIME DEFAULT NULL,
	refund_requested_at DATETIME DEFAULT NULL,
	refunded_at DATETIME DEFAULT NULL,

	INDEX(order_id),
	INDEX(product_id),
	INDEX(user_id),
	INDEX(status),

	PRIMARY KEY(id),
	FOREIGN KEY (order_id) REFERENCES orders(id),
	FOREIGN KEY (product_id) REFERENCES products(id),
	FOREIGN KEY (user_id) REFERENCES users(id)
)ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- payments table
-- status = 1 = success, 2 = failed
--
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	amount INT UNSIGNED NOT NULL,
	order_id INT UNSIGNED NOT NULL,
	rzp_payment_id VARCHAR(64),
	rzp_signature VARCHAR(256),
	status INT DEFAULT 0,
	user_id INT UNSIGNED NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	INDEX(order_id),
	INDEX(rzp_payment_id),
	INDEX(user_id),

	PRIMARY KEY(id),
	FOREIGN KEY (order_id) REFERENCES orders(id),
	FOREIGN KEY (user_id) REFERENCES users(id)
)ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- refunds table
-- amount in Indian paise
--
DROP TABLE IF EXISTS `refunds`;
CREATE TABLE `refunds` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	user_id INT UNSIGNED NOT NULL,
	order_id INT UNSIGNED NOT NULL,
	order_item_id INT UNSIGNED NOT NULL,
	payment_id INT UNSIGNED NOT NULL,
	amount INT NULL NULL,
	rzp_refund_id VARCHAR(256) NOT NULL,
	rzp_status VARCHAR(32) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	INDEX(order_id),
	INDEX(order_item_id),
	INDEX(user_id),
	INDEX(rzp_refund_id),
	INDEX(created_at),

	PRIMARY KEY(id),
	FOREIGN KEY (order_id) REFERENCES orders(id),
	FOREIGN KEY (order_item_id) REFERENCES order_items(id),
	FOREIGN KEY (payment_id) REFERENCES payments(id),
	FOREIGN KEY (user_id) REFERENCES users(id)
)ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- invoices table
--
DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	order_id INT UNSIGNED NOT NULL,
	shipping_id INT UNSIGNED NOT NULL,
	address_id INT UNSIGNED NOT NULL,
	user_id INT UNSIGNED NOT NULL,

	PRIMARY KEY(id),
	FOREIGN KEY (order_id) REFERENCES orders(id),
	FOREIGN KEY (shipping_id) REFERENCES shippings(id),
	FOREIGN KEY (address_id) REFERENCES addresses(id),
	FOREIGN KEY (user_id) REFERENCES users(id)
)ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- users table data
--
INSERT INTO users(first_name, last_name, gender, phone, email, password, photo, role) VALUES
('User', 'One', 1, '9331920001','one@example.com',  '$2y$10$m10VlTg6o2yYt3SRW92AZOJBIoPNmAWP2/x7nuzt17rgqVhWWzMbW', 'avatar-male.jpg', 2),
('User', 'Two', 1, '9331920002', 'two@example.com', '$2y$10$m10VlTg6o2yYt3SRW92AZOJBIoPNmAWP2/x7nuzt17rgqVhWWzMbW', 'avatar-male.jpg', 2);

--
-- categories table data
--
INSERT INTO categories(category) VALUES
('Product category');



SET FOREIGN_KEY_CHECKS=1;
