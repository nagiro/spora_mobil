UPDATE `infoauxiliar` SET `valor` = '1.7' WHERE `infoauxiliar`.`id` =55;
ALTER TABLE `carrers` CHANGE `via` `via` VARCHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

DELETE FROM municipis WHERE actiu = 0;
UPDATE `municipis` SET `nom` = 'Arenys de Mar' WHERE `municipis`.`id` =11413;

-- Activar municipi Lloret de Mar
INSERT INTO `municipis` (
    `id` ,
    `nom` ,
    `actiu`
)
VALUES (
    11787 , 'Lloret de Mar', '1'
);
INSERT INTO `barris` VALUES(49, 'Sector 1', 11787);
INSERT INTO `barrisagrupats` VALUES(NULL, 14, 49, 1);

--Pla de l'estany
INSERT INTO `municipis` VALUES(11788, 'Banyoles', 1);
INSERT INTO `municipis` VALUES(11789, 'Porqueres', 1);
INSERT INTO `municipis` VALUES(11790, 'Camós', 1);
INSERT INTO `municipis` VALUES(11791, 'Palol de Revardit', 1);
INSERT INTO `municipis` VALUES(11792, 'Sant Miquel de Campmajor', 1);
INSERT INTO `municipis` VALUES(11793, 'Crespià', 1);
INSERT INTO `municipis` VALUES(11794, 'Serinyà', 1);
INSERT INTO `municipis` VALUES(11795, 'Esponellà', 1);
INSERT INTO `municipis` VALUES(11796, 'Vilademuls', 1);
INSERT INTO `municipis` VALUES(11797, 'Fontcoberta', 1);
INSERT INTO `municipis` VALUES(11798, 'Cornellà del Terri', 1);

INSERT INTO `barris` VALUES(50, 'Sector 1', 11788);
INSERT INTO `barris` VALUES(51, 'Sector 1', 11789);
INSERT INTO `barris` VALUES(53, 'Sector 1', 11790);
INSERT INTO `barris` VALUES(54, 'Sector 1', 11791);
INSERT INTO `barris` VALUES(55, 'Sector 1', 11792);
INSERT INTO `barris` VALUES(56, 'Sector 1', 11793);
INSERT INTO `barris` VALUES(57, 'Sector 1', 11794);
INSERT INTO `barris` VALUES(58, 'Sector 1', 11795);
INSERT INTO `barris` VALUES(59, 'Sector 1', 11796);
INSERT INTO `barris` VALUES(60, 'Sector 1', 11797);
INSERT INTO `barris` VALUES(61, 'Sector 1', 11798);

INSERT INTO `barrisagrupats` VALUES(47, 15, 50, 1);
INSERT INTO `barrisagrupats` VALUES(48, 16, 51, 1);
INSERT INTO `barrisagrupats` VALUES(49, 17, 53, 1);
INSERT INTO `barrisagrupats` VALUES(50, 18, 54, 1);
INSERT INTO `barrisagrupats` VALUES(51, 19, 55, 1);
INSERT INTO `barrisagrupats` VALUES(52, 20, 56, 1);
INSERT INTO `barrisagrupats` VALUES(53, 21, 57, 1);
INSERT INTO `barrisagrupats` VALUES(54, 22, 58, 1);
INSERT INTO `barrisagrupats` VALUES(55, 23, 59, 1);
INSERT INTO `barrisagrupats` VALUES(56, 24, 60, 1);
INSERT INTO `barrisagrupats` VALUES(57, 25, 61, 1);