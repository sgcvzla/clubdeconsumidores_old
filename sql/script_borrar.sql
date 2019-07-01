ALTER TABLE `socios` 
	ADD `dianacimiento` INT(2) NOT NULL AFTER `apellidos`, 
	ADD `mesnacimiento` INT(2) NOT NULL AFTER `dianacimiento`, 
	ADD `anonacimiento` INT(4) NOT NULL AFTER `mesnacimiento`, 
	ADD `fechanacimiento` DATE NOT NULL AFTER `anonacimiento`, 
	ADD `sexo` VARCHAR(20) NOT NULL AFTER `fechanacimiento`;