<?php

abstract class Productors {
    public static function llistaProductors() {
        $db = Database::getInstance();

        $query = '
        SELECT
            `p`.`id`,
            `p`.`alta`,
            `p`.`nom`,
            `m`.`nom` AS municipi
        FROM `productors` `p`
            LEFT JOIN `municipis` `m` ON `m`.`id` = `p`.`municipi`
        ORDER BY `nom` ASC
        ';

        return $db->query($query);
    }

    public static function obteProductor($id) {
        if(empty($id)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            `id`,
            `nom`,
            `alta`,
            `educador`,
            `tipusEstabliment`,
            `tipusEstablimentAltres`,
            `telefon`,
            `personaContacte`,
            `diesDescans`,
            `suggerimentsComentaris`
        FROM `formulariproductor`
        WHERE `id` = :id
        ';
        $params = array(':id' => $id);

        return $db->query($query, $params);
    }

    public static function creaProductor($nom, $municipi, $establiment, $telefon, $contacte, $horariInici, $horariFi, $dies, $suggeriments) {
        if(empty($nom) || !is_string($nom)) {
            throw new Exception('Has d\'escriure un nom pel productor');
        }

        if(!is_numeric($municipi) || $municipi < 1) {
            throw new Exception('Has de triar un municipi de la llista');
        }

        $db = Database::getInstance();

        $query = '
        INSERT INTO `productors` (
            `usuari`,
            `municipi`,
            `alta`,
            `nom`,
            `tipusEstabliment`,
            `telefon`,
            `personaContacte`,
            `horariInici`,
            `horariFi`,
            `diesDescans`,
            `suggerimentsComentaris`
        ) VALUES (
            :usuari,
            :municipi,
            NOW(),
            :nom,
            :tipusEstabliment,
            :telefon,
            :personaContacte,
            :horariInici,
            :horariFi,
            :diesDescans,
            :suggerimentsComentaris
        )';

        return $db->insert($query, array(
            ':usuari' => $_SESSION['userID'],
            ':municipi' => $municipi,
            ':nom' => $nom,
            ':tipusEstabliment' => $establiment,
            ':telefon' => $telefon,
            ':personaContacte' => $contacte,
            ':horariInici' => $horariInici,
            ':horariFi' => $horariFi,
            ':diesDescans' => $dies,
            ':suggerimentsComentaris' => $suggeriments
        ));
     }

    public static function modificaProductor($id, $nom, $municipi, $establiment, $telefon, $contacte, $horariInici, $horariFi, $dies, $suggeriments) {
        $db = Database::getInstance();

        $query = '
        UPDATE `productors` SET
            `municipi` = :municipi,
            `nom` = :nom,
            `tipusEstabliment` = :tipusEstabliment,
            `telefon` = :telefon,
            `personaContacte` = :personaContacte,
            `horariInici` = :horariInici,
            `horariFi` = :horariFi,
            `diesDescans` = :diesDescans,
            `suggerimentsComentaris` = :suggerimentsComentaris
        WHERE `id` = :id';

        return $db->exec($query, array(
            ':id' => $id,
            ':municipi' => $municipi,
            ':nom' => $nom,
            ':tipusEstabliment' => $establiment,
            ':telefon' => $telefon,
            ':personaContacte' => $contacte,
            ':horariInici' => $horariInici,
            ':horariFi' => $horariFi,
            ':diesDescans' => $dies,
            ':suggerimentsComentaris' => $suggeriments
        ));
    }

    //Sistemes de recollida
    public static function desaSistemesRecollidaProductor($idProductor, array $sistemes) {
        if(!is_int($idProductor) || $idProductor < 1) {
            throw new Exception('El productor no és vàlid');
        }

        $query = '
        INSERT INTO `sistemesrecollida` (
            `productor`,
            `material`,
            `sistema`
        ) VALUES (
            :productor,
            :material,
            :sistema
        )
        ';
        $db = Database::getInstance();
        $exec = true;

        foreach($sistemes as $sr) {
            $exec&= $db->exec($query, array(
                ':productor' => $idProductor,
                ':material' => $sr[0],
                ':sistema' => $sr[1]
            ));
        }

        return $exec;
    }

    public static function obteSistemesRecollidaProductor($idProductor) {
        if(!is_int($idProductor) || $idProductor < 1) {
            throw new Exception('El productor no és vàlid');
        }

        $query = '
        SELECT
            `material`,
            `sistema`
        FROM `sistemesrecollida`
        WHERE `productor` = :productor
        ';

        return Database::getInstance()->query($query, array(':productor' => $idProductor));
    }

    public static function eliminaSistemesRecollidaProductor($idProductor) {
        if(!is_int($idProductor) || $idProductor < 1) {
            throw new Exception('El productor no és vàlid');
        }

        $query = 'DELETE FROM `sistemesrecollida` WHERE `productor` = :productor';

        return Database::getInstance()->exec($query, array(':productor' => $idProductor));
    }

    //Necessitat de contenidors
    public static function desaNecessitatContenidorsProductor($idProductor, array $necessitat) {
        if(!is_int($idProductor) || $idProductor < 1) {
            throw new Exception('El productor no és vàlid');
        }
        
        $query = '
        INSERT INTO `necessitatscontenidors` (
            `productor`,
            `material`,
            `capacitat`,
            `demanda`
        ) VALUES (
            :productor,
            :material,
            :capacitat,
            :demanda
        )';

        $db = Database::getInstance();
        $exec = true;
        
        foreach($necessitat as $nc) {
            $exec&= $db->exec($query, array(
                ':productor'=> $idProductor,
                ':material' => $nc[0],
                ':capacitat'=> $nc[1],
                ':demanda'  => $nc[2]
            ));
        }

        return $exec;
    }

    public static function obteNecessitatContenidors($idProductor) {
        if(!is_int($idProductor) || $idProductor < 1) {
            throw new Exception('El productor no és vàlid');
        }
        
        $query = '
        SELECT
            `material`,
            `capacitat`,
            `demanda`
        FROM `necessitatscontenidors`
        WHERE `productor` = :productor';

        return Database::getInstance()->query($query, array(':productor' => $idProductor));
    }

    public static function eliminaNecessitatContenidorsProductor($idProductor) {
        if(!is_int($idProductor) || $idProductor < 0) {
            throw new Exception('El productor no és vàlid');
        }
        
        $query = 'DELETE FROM `necessitatscontenidors` WHERE `productor` = :productor';

        return Database::getInstance()->exec($query, array(':productor' => $idProductor));
    }

    //Entregues de material
    public static function llistaEntreguesMaterial() {
        $db = Database::getInstance();

        $query = '
        SELECT
            `p`.`nom`,
            `em`.`id`,
            `em`.`alta`,
            `m`.`nom` AS municipi
        FROM `formularientregamaterial` `em`
            LEFT JOIN `productors` `p` ON `p`.`id` = `em`.`productor`
            LEFT JOIN `municipis` `m` ON `m`.`id` = `p`.`municipi`
        ORDER BY `p`.`nom` ASC
        ';

        return $db->query($query);
    }

    public static function creaFormulariEntregaMaterial($productor, $entregaContenidors, $entregaMaterialGrafic, $suggerimentsComentaris) {
        if(!is_int($productor) || $productor < 1) {
            throw new Exception('El productor no és vàlid');
        }

        $db = Database::getInstance();

        $query = '
        INSERT INTO `formularientregamaterial` (
            `productor`,
            `alta`,
            `usuari`,
            `entregaMaterialGrafic`,
            `suggerimentsComentaris`
        ) VALUES (
            :productor,
            NOW(),
            :usuari,
            :entregaMaterialGrafic,
            :suggerimentsComentaris
        )';

        return $db->exec($query, array(
            ':productor' => $productor,
            ':usuari' => $_SESSION['userID'],
            ':entregaMaterialGrafic' => $entregaMaterialGrafic,
            ':suggerimentsComentaris' => $suggerimentsComentaris
        ));
    }

    public static function modificaFormulariEntregaMaterial($id, $productor, $entregaContenidors, $entregaMaterialGrafic, $suggerimentsComentaris) {
        $db = Database::getInstance();

        $query = '
        UPDATE `formularientregamaterial` SET
            `productor` = :productor,
            `entregaMaterialGrafic` = :entregaMaterialGrafic,
            `suggerimentsComentaris` = :suggerimentsComentaris
        WHERE `id` = :id';

        return $db->exec($query, array(
            ':productor' => $productor,
            ':entregaMaterialGrafic' => $entregaMaterialGrafic,
            ':suggerimentsComentaris' => $suggerimentsComentaris
        ));
    }
    //Fi entregues de material

    //Seguiments
    public static function llistaSeguiments() {
        $db = Database::getInstance();

        $query = '
        SELECT
            `p`.`nom`,
            `s`.`id`,
            `s`.`alta`,
            `m`.`nom` AS municipi
        FROM `formulariseguiment` `s`
            LEFT JOIN `productors` `p` ON `p`.`id` = `s`.`productor`
            LEFT JOIN `municipis` `m` ON `m`.`id` = `p`.`municipi`
        ORDER BY `nom` ASC
        ';

        return $db->query($query);
    }

    public static function creaFormulariSeguiment($productor, $participa, $grauSatisfaccio, $queixes, $dubtes, $comentaris, $suggeriments) {
        if(!is_int($productor) || $productor < 1) {
            throw new Exception('La ID del productor no és vàlida');
        }

        if(!is_numeric($grauSatisfaccio) || !inRange(0, $grauSatisfaccio, 10)) {
            throw new Exception('El grau de satisfacció ha de ser un número entre 0 i 10');
        }

        $db = Database::getInstance();

        $query = '
        INSERT INTO `formulariseguiment` (
            `productor`,
            `alta`,
            `usuari`,
            `participaRecollidaSelectiva`,
            `grauSatisfaccio`,
            `queixes`,
            `dubtes`,
            `comentaris`,
            `suggeriments`
        ) VALUES (
            :productor,
            NOW(),
            :usuari,
            :participa,
            :grauSatisfaccio,
            :queixes,
            :dubtes,
            :comentaris,
            :suggeriments
        )';

        return $db->exec($query, array(
            ':productor' => $productor,
            ':usuari' => $_SESSION['userID'],
            ':participa' => $participa,
            ':grauSatisfaccio' => $grauSatisfaccio,
            ':queixes' => $queixes,
            ':dubtes' => $dubtes,
            ':comentaris' => $comentaris,
            ':suggeriments' => $suggeriments
        ));
    }

    public static function modificaFormulariSeguiment($id, $productor, $participa, $grauSatisfaccio, $queixes, $dubtes, $comentaris, $suggeriments) {
        if(!is_int($id) || $id < 1) {
            throw new Exception('La ID del formulari no és vàlida');
        }

        if(!is_int($productor) || $productor < 1) {
            throw new Exception('La ID del productor no és vàlida');
        }

        $db = Database::getInstance();

        $query = '
        UPDATE `formulariseguiment` SET
            `productor` = :productor,
            `participaRecollidaSelectiva` = :participa,
            `grauSatisfaccio` = :grauSatisfaccio,
            `queixes` = :queixes,
            `dubtes` = :dubtes,
            `comentaris` = :comentaris,
            `suggeriments` = :suggeriments
        WHERE `id` = :id';

        return $db->exec($query, array(
            ':id' => $id,
            ':productor' => $productor,
            ':participa' => $participa,
            ':grauSatisfaccio' => $grauSatisfaccio,
            ':queixes' => $queixes,
            ':dubtes' => $dubtes,
            ':comentaris' => $comentaris,
            ':suggeriments' => $suggeriments
        ));
    }
    //Fi seguiments
}

?>