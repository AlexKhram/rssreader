CREATE TABLE `rss`.`chanels` ( `id` INT NOT NULL AUTO_INCREMENT , `url` VARCHAR(40) NOT NULL , PRIMARY KEY (`id`), UNIQUE (`url`)) ENGINE = InnoDB;

CREATE TABLE `rss`.`user_chanels` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `chanel_id` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;