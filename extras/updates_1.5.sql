-- Formulari seguiment
ALTER TABLE `formulariseguiment` CHANGE `grauSatisfaccio` `grauSatisfaccio` CHAR( 2 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;

-- Formulari productor
DROP TABLE IF EXISTS `formulariproductor`;
DROP TABLE IF EXISTS `productors`;
CREATE TABLE `productors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuari` int(11) NOT NULL,
  `municipi` int(11) NOT NULL,
  `alta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nom` varchar(255) NOT NULL,
  `tipusEstabliment` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `telefon` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `personaContacte` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `horariInici` time NOT NULL,
  `horariFi` time NOT NULL,
  `diesDescans` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `suggerimentsComentaris` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DELETE FROM `infoauxiliar` WHERE `grup` = 'VersioBD';
INSERT INTO `infoauxiliar` (
    `id` ,
    `grup` ,
    `valor`
) VALUES (
    NULL , 'VersioBD', '1.5'
);

-- Entrega de contenidors
DROP TABLE `entregacontenidors`;
CREATE TABLE `entregacontenidors` (
  `productor` int(11) NOT NULL,
  `material` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `capacitat` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `entrega` int(11) NOT NULL,
  PRIMARY KEY (`productor`,`material`,`capacitat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Necessitat de contenidors
DROP TABLE `necessitatscontenidors`;
CREATE TABLE `necessitatscontenidors` (
  `productor` int(11) NOT NULL,
  `material` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `capacitat` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `demanda` int(11) NOT NULL,
  PRIMARY KEY (`productor`,`material`,`capacitat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Formulari poblacio
ALTER TABLE `formularipoblacio` CHANGE `data` `data` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- Sistemes de recollida
DROP TABLE IF EXISTS `sistemesrecollida`;
CREATE TABLE `sistemesrecollida` (
  `productor` int(11) NOT NULL,
  `material` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sistema` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`productor`,`material`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;