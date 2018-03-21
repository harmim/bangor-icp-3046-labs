-- Author: Dominik Harmim <harmim6@gmail.com>

SET NAMES utf8mb4;


CREATE DATABASE `icp3046_eshop`
	DEFAULT CHARACTER SET utf8mb4
	COLLATE utf8mb4_unicode_520_ci;

USE `icp3046_eshop`;


-- Created and edited columns tells when the product has been created or edited, auto insert current datetime,
-- moreover edited column is automatically updated on item update.

-- Prices are numeric (decimal) data types because it is useless storing them as float or double value.


CREATE TABLE `product` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`price` NUMERIC(15, 5) NOT NULL,
	`description` LONGTEXT COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	`image` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`edited` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
		ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;



CREATE TABLE `user` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`password` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`forename` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`surname` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`edited` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
		ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `email` (`email`)
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


CREATE TABLE `shipping_method` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(60) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`price` NUMERIC(15, 5) NOT NULL DEFAULT 0.0,
	PRIMARY KEY (`id`)
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


CREATE TABLE `payment_method` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(60) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`price` NUMERIC(15, 5) NOT NULL DEFAULT 0.0,
	PRIMARY KEY (`id`)
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


-- Stores reference to user and user details to order table again because user details can be changed and in order
-- table must be user details which user had in time of creating order.
CREATE TABLE `order` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user` INT UNSIGNED NOT NULL,
	`status` ENUM('processing', 'complete', 'failed', 'canceled')
		COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'processing',
	`is_paid` TINYINT(1) NOT NULL DEFAULT 0,
	`ip` VARCHAR(60) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL, -- IP address of user's computer which created order.
	`email` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`forename` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`surname` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`address` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`city` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`zip` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`shipping_forename` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	`shipping_surname` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	`shipping_address` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	`shipping_city` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	`shipping_zip` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`edited` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
		ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `user` (`user`),
	CONSTRAINT `order_fk_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`)
		ON DELETE RESTRICT
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


-- This table order_item stores either product, shipping or payment and column type indicates which one is it.
-- Stores reference to product, shipping, payment and it's details to order_item table again because it's details can
-- be changed and in order_item table must be it's details which that items had in time of creating order.
CREATE TABLE `order_item` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`order` INT UNSIGNED NOT NULL,
	`type` ENUM('product', 'shipping', 'payment')
		COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'product',
	`product` INT UNSIGNED DEFAULT NULL,
	`shipping` INT UNSIGNED DEFAULT NULL,
	`payment` INT UNSIGNED DEFAULT NULL,
	`quantity` INT UNSIGNED NOT NULL DEFAULT 1,
	`price` NUMERIC(15, 5) NOT NULL,
	`name` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	PRIMARY KEY (`id`),
	KEY `order` (`order`),
	KEY `payment` (`payment`),
	KEY `shipping` (`shipping`),
	KEY `product` (`product`),
	CONSTRAINT `order_item_fk_order` FOREIGN KEY (`order`) REFERENCES `order` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	CONSTRAINT `order_item_fk_product` FOREIGN KEY (`product`) REFERENCES `product` (`id`)
		ON DELETE SET NULL
		ON UPDATE CASCADE,
	CONSTRAINT `order_item_fk_shipping` FOREIGN KEY (`shipping`) REFERENCES `shipping_method` (`id`)
		ON DELETE SET NULL
		ON UPDATE CASCADE,
	CONSTRAINT `order_item_fk_payment` FOREIGN KEY (`payment`) REFERENCES `payment_method` (`id`)
		ON DELETE SET NULL
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;
