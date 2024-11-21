CREATE TABLE `diet_generator_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
)

CREATE TABLE `diet_generator_dog` (
  `id` varchar(255) NOT NULL,
  `name_owner` varchar(100) NOT NULL,
  `surname_owner` varchar(100) NOT NULL,
  `dog_name` varchar(100) NOT NULL,
  `dog_race` varchar(100) NOT NULL,
  `weight_kg` smallint NOT NULL,
  `age` tinyint NOT NULL,
  `status` varchar(100) NOT NULL,
  `user_id` int NOT NULL,
  `num_changes` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `diet_generator_user` (`id`) ON DELETE CASCADE
)

CREATE TABLE `diet_generator_dog_body` (
  `id` int NOT NULL AUTO_INCREMENT,
  `height_cm` double DEFAULT NULL,
  `circ_chest_cm` double DEFAULT NULL,
  `circ_abs_cm` double DEFAULT NULL,
  `dog_user_think` varchar(50) NOT NULL,
  `dog_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dog_id` (`dog_id`),
  FOREIGN KEY (`dog_id`) REFERENCES `diet_generator_dog` (`id`) ON DELETE CASCADE
)

CREATE TABLE `diet_generator_dog_tab` (
  `id` int NOT NULL AUTO_INCREMENT,
  `daily_kcal` double NOT NULL,
  `omega_3` double NOT NULL,
  `dog_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dog_id` (`dog_id`),
  FOREIGN KEY (`dog_id`) REFERENCES `diet_generator_dog` (`id`) ON DELETE CASCADE
)

CREATE TABLE `diet_generator_foods` (
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`carbs` double NOT NULL,
	`proteins` double NOT NULL,
	`fats` double NOT NULL,
	`omega_3` double NOT NULL,
	`omega_6` double NOT NULL,
	`calcium` double NOT NULL,
	`phosphorus` double NOT NULL,
	`total_kcal` double NOT NULL,
	`percentage_carbs` int NOT NULL,
	`percentage_proteins` int NOT NULL,
	`percentage_fats` int NOT NULL,
	
	`approved` boolean DEFAULT false NOT NULL,
	
	PRIMARY KEY (`id`)
)

CREATE TABLE `diet_generator_sicks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sicks` text,
  `dog_id` varchar(255) NOT NULL,

  PRIMARY KEY (`id`),

  FOREIGN KEY (`dog_id`) REFERENCES `diet_generator_dog` (`id`) ON DELETE CASCADE
)