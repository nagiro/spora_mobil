-- Update: elimina la columna `id` a barriscarrer => utilitza les altres 2 com a PK
ALTER TABLE `barriscarrer` DROP `id`;
ALTER TABLE `barriscarrer` ADD PRIMARY KEY ( `barri` , `carrer` ) ;

-- Si hi ha entrades duplicades eliminar amb SELECT * FROM `barriscarrer` GROUP BY `barri`, `carrer` HAVING COUNT(*) > 1