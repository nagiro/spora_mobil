<?php

abstract class Incidencies {
    public static function llistaIncidenciesParticulars() {
        $db = Database::getInstance();

        $query = '
        SELECT
            `i`.`id`,
            `u`.`username`,
            `u`.`nom` AS `usuari`,
            `i`.`data`
        FROM `incidencies` `i`
        LEFT JOIN `usuaris` `u` ON `u`.`id` = `i`.`usuari`
        WHERE `tipus` = "Particular"
        ORDER BY `i`.`data`, `usuari` ASC
        ';

        return $db->query($query);
    }

    public static function llistaIncidenciesProductors() {
        $db = Database::getInstance();

        $query = '
        SELECT
            `i`.`id`,
            `u`.`username`,
            `u`.`nom` AS `usuari`,
            `i`.`data`
        FROM `incidencies` `i`
        LEFT JOIN `usuaris` `u` ON `u`.`id` = `i`.`usuari`
        WHERE `tipus` = "Productor"
        ORDER BY `i`.`data`, `usuari` ASC
        ';

        return $db->query($query);
    }

    public static function afegeixIncidencia($tipus, $usuari, $text) {
        if(empty($tipus) || empty($usuari) || empty($text)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        INSERT INTO `incidencies` (
            `tipus`,
            `usuari`,
            `data`,
            `text`
        ) VALUES (
            :tipus,
            :usuari,
            NOW(),
            :text
        )
        ';
        $params = array(
            ':tipus' => $tipus,
            ':usuari' => $usuari,
            ':text' => $text
        );

        return $db->exec($query, $params);
    }
    
    public static function obteIncidencia($id) {
        $db = Database::getInstance();

        $query = '
        SELECT
            `i`.`id`,
            `i`.`data`,
            `i`.`usuari`,
            `i`.`text`,
            `u`.`nom` AS nomUsuari,
            `u`.`username`
        FROM `incidencies` `i`
        LEFT JOIN `usuaris` `u` ON `u`.`id` = `i`.`usuari`
        WHERE `i`.`id` = :id
        ';
        $params = array(
            ':id' => $id
        );

        $result = $db->query($query, $params);
        
        if(!is_array($result) || !isset($result[0])) {
            return false;
        }

        return $result[0];
    }
}

?>