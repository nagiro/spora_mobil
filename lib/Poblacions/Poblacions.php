<?php

abstract class Poblacions {
    //Municipis
    public static function llistaPoblacions() {
        $db = Database::getInstance();

        $query = '
        SELECT
            `nom`,
            `id`
        FROM `municipis`
        WHERE `actiu` = 1
        GROUP BY `nom`
        ORDER BY `nom` ASC';

        return $db->query($query);
    }

    public static function obteIDMunicipiPerNom($nom) {
        if(empty($nom)) {
            return 0;
        }

        $db = Database::getInstance();

        $query = 'SELECT `id` FROM `municipis` WHERE `nom` = :nom';
        $params= array(':nom' => $nom);

        $result = $db->query($query, $params);

        if(!is_array($result) || !isset($result[0])) {
            return 0;
        }

        return $result[0]['id'];
    }

    public static function llistaMunicipis() {
        $db = Database::getInstance();

        $query = '
        SELECT DISTINCT
            `id`,
            `nom`
        FROM `municipis` ORDER BY `nom` ASC
        ';

        return $db->query($query);
    }

    public static function obteNomMunicipi($id) {
        if(empty($id)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            `nom`
        FROM `municipis`
        WHERE `id` = :id
        ';
        $params = array(':id' => $id);

        $carrer =  $db->query($query, $params);

        if(is_array($carrer) && isset($carrer[0])) {
            return $carrer[0]['nom'];
        } else {
            return '';
        }
    }

    public static function obteMunicipi($id) {
        if(empty($id)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            `id`,
            `nom`
        FROM `municipis`
        WHERE `id` = :id
        ';
        $params = array(':id' => $id);

        $municipi =  $db->query($query, $params);

        if(is_array($municipi) && isset($municipi[0])) {
            return $municipi[0];
        } else {
            return '';
        }
    }

    public static function eliminaCarrersMunicipi($id) {
        if(!is_int($id) || $id < 1) {
            throw new Exception('El municipi no és vàlid');
        }

        $queries = array();
        $queries[]= 'DELETE FROM `carrers` WHERE `municipi` = :municipi';
        $queries[]= 'DELETE FROM `barriscarrer` WHERE barri IN (
            SELECT id
            FROM barris
            WHERE municipi = :municipi
        )';

        $exec = true;
        $db = Database::getInstance();
        foreach($queries as $query) {
            $exec&= $db->exec($query, array(':municipi' => $id));
        }

        return $exec;
    }

    //Agrupacions de barris
    public static function obteGrupBarri($id) {
        if(empty($id)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            `grup`
        FROM `barrisagrupats`
        WHERE `barri` = :barri AND `actiu` = "1"
        ';
        $params = array(':barri' => $id);

        $carrer =  $db->query($query, $params);

        if(is_array($carrer) && isset($carrer[0])) {
            return $carrer[0]['grup'];
        } else {
            return 0;
        }
    }

    public static function llistaAgrupacionsBarris($municipi = null) {
        $db = Database::getInstance();

        $params = array();
        $query = '
        SELECT
            `ba`.`grup`,
            `m`.`nom` AS nomMunicipi,
            GROUP_CONCAT(`b`.`nom`) AS `nom`
        FROM `barris` `b`
            LEFT JOIN `barrisagrupats` `ba` ON `b`.`id` = `ba`.`barri`
            LEFT JOIN `municipis` `m` ON `m`.`id` = `b`.`municipi`
        WHERE `ba`.`actiu` = 1 ';

        if($municipi) {
            $query.= ' AND `b`.`municipi` = :municipi';
            $params[':municipi'] = $municipi;
        }

        $query.= '
        GROUP BY `ba`.`grup`
        ORDER BY `grup`, `b`.`nom` ASC
        ';

        return $db->query($query, $params);
    }

    public static function afegeixAgrupacioBarris($barris) {
        if(!is_array($barris) ||count($barris) < 1) {
            throw new Exception('La llista de barris a agrupar no pot ser buïda');
        }

         $db = Database::getInstance();

         $query = 'SELECT MAX(`grup`) AS next FROM `barrisagrupats`';
         $nextGID = $db->query($query);

         if(!is_array($nextGID)||!isset($nextGID[0]) || !is_numeric($nextGID[0]['next'])) {
             $nextGID = 0;
         } else {
             $nextGID = $nextGID[0]['next'];
         }
         $nextGID++;

         $query = '
            INSERT INTO `barrisagrupats` (
                `grup`,
                `actiu`,
                `barri`
            ) VALUES (
                :nextID,
                "1",
                :barri
            )
        ';
        $barri = 0;
        $stat = $db->getStatement($query);
        $stat->bindParam(':nextID', $nextGID, PDO::PARAM_INT);
        $stat->bindParam(':barri', $barri, PDO::PARAM_INT);
        $result = true;

        foreach($barris as $barri) {
            $result&= $stat->execute();
        }

        return $result;
    }

    public static function editaAgrupacioBarris($gid, $barris) {
        if(!is_numeric($gid) || empty($gid)) {
            throw new Exception('La ID del grup de barris a editar no és vàlida');
        }

        if(!is_array($barris) || count($barris) < 1) {
            throw new Exception('La llista de barris a editar no pot ser buïda');
        }

        $db = Database::getInstance();

        $query = 'DELETE `barrisagrupats` WHERE `grup` = :grup';
        $params = array(':grup' => $gid);
        $result = $db->exec($query, $params);

        $query = '
            INSERT INTO `barrisagrupats` (
                `grup`,
                `actiu`,
                `barri`
            ) VALUES (
                :nextID,
                "1",
                :barri
            )
        ';
        $barri = 0;
        $stat = $db->getStatement($query);
        $stat->bindParam(':nextID', $gid, PDO::PARAM_INT);
        $stat->bindParam(':barri', $barri, PDO::PARAM_INT);

        foreach($barris as $barri) {
            $result&= $stat->execute();
        }

        return $result;
    }

    public static function eliminaAgrupacioBarris($gid) {
        $db = Database::getInstance();

        $query = 'DELETE FROM `barrisagrupats` WHERE `grup` = :grup';
        $params = array(':grup' => $gid);

        return $db->exec($query, $params);
    }

    //Barris
    public static function llistaBarris($municipi = null) {
        $db = Database::getInstance();

        $query = '
        SELECT
            `b`.`id`,
            `b`.`nom`,
            `m`.`id` AS idMunicipi,
            `m`.`nom` AS nomMunicipi
        FROM `barris` `b`
        LEFT JOIN `municipis` `m` ON `b`.`municipi` = `m`.`id`';
        $params = array();

        if($municipi) {
            $query.= ' WHERE `b`.`municipi` = :municipi';
            $params[':municipi'] = $municipi;
        }

        $query.= ' ORDER BY `nomMunicipi`, `b`.`nom` ASC';

        return $db->query($query, $params);
    }

    public static function afegeixBarri($nom, $municipi) {
        $db = Database::getInstance();

        $query = '
        INSERT INTO `barris` (
            `nom`,
            `municipi`
        ) VALUES (
            :nom,
            :municipi
        )';
        $params = array(
            ':nom' => $nom,
            ':municipi' => $municipi
        );

        return $db->insert($query, $params);
    }

    public static function eliminaBarri($id) {
        if(empty($id)) {
            throw new Exception;
        }

        $db = Database::getInstance();

        $query = 'DELETE FROM `barris` WHERE `id` = :id';
        $params = array(':id' => $id);

        return $db->exec($query, $params);
    }

    public static function obteIDBarriPerNom($nom, $municipi) {
        if(empty($nom)) {
            return 0;
        }

        $db = Database::getInstance();

        $query = 'SELECT `id` FROM `barris` WHERE `nom` = :nom AND `municipi` = :municipi';
        $params= array(':nom' => $nom,
        			   ':municipi' => $municipi);

        $result = $db->query($query, $params);

        if(!is_array($result) || !isset($result[0])) {
            return 0;
        }

        return (int)$result[0]['id'];
    }

    public static function afegeixBarriCarrer($barri, $carrer) {
        if(empty($barri) || empty($carrer)) {
            throw new Exception;
        }

        $db = Database::getInstance();

        $query = '
        INSERT INTO `barriscarrer` (
            `barri`,
            `carrer`
        ) VALUES (
            :barri,
            :carrer
        )
        ';
        $params = array(
            ':barri' => $barri,
            ':carrer'=> $carrer
        );

        return $db->insert($query, $params);
    }

    public static function eliminaBarriCarrer($barri, $carrer) {
        if(empty($barri) || empty($carrer)) {
            throw new Exception;
        }

        $db = Database::getInstance();

        $query = 'DELETE FROM `barriscarrer` WHERE `barri` = :barri AND `carrer` = :carrer';
        $params = array(
            ':barri' => $barri,
            ':carrer' => $carrer
        );

        return $db->exec($query, $params);
    }

    public static function existeixBarriCarrer($barri, $carrer) {
        if(empty($barri) || empty($carrer)) {
            throw new Exception;
        }

        $db = Database::getInstance();

        $query = 'SELECT `id` FROM `barriscarrer` WHERE `barri` = :barri AND `carrer` = :carrer';
        $params = array(
            ':barri' => $barri,
            ':carrer' => $carrer
        );

        $res = $db->query($query, $params);

        return (is_array($res) && count($res) > 0);
    }

    //Carrers
    public static function llistaCarrers($municipi = null, $barri = null) {
        $db = Database::getInstance();

        $query = '
        SELECT
            `c`.`id`,
            `c`.`via`,
            `c`.`nom`,
            `m`.`id` AS idMunicipi,
            `m`.`nom` AS nomMunicipi,
            GROUP_CONCAT(`b`.`nom` SEPARATOR " ,") AS nomBarri
        FROM `carrers` `c`
            LEFT JOIN `barriscarrer` `bc` ON `bc`.`carrer` = `c`.`id`
            LEFT JOIN `barris` `b` ON `b`.`id` = `bc`.`barri`
            LEFT JOIN `municipis` `m` ON `b`.`municipi` = `m`.`id`
        ';
        $params = array();
        $where = array();

        if(!empty($municipi)) {
            $where[]= '`b`.`municipi` = :municipi';
            $params[':municipi'] = $municipi;
        }

        if(!empty($barri)) {
            $where[]= '`c`.`barri` = :barri';
            $params[':barri'] = $barri;
        }

        if(isset($where[0])) {
            $query.= 'WHERE ' . join(' AND ', $where);
        }

        $query.= '
        GROUP BY `c`.`id`
        ORDER BY `nomMunicipi`, `nom`, `via` ASC';

        return $db->query($query, $params);
    }

    public static function llistaCarrersPerBarris($grup, $lletra = null) {
        $db = Database::getInstance();

        $params = array(':grup' => $grup);
        $query = '
        SELECT
            `c`.`id`,
            `c`.`via`,
            `c`.`nom`
        FROM `carrers` `c`
            LEFT JOIN `barriscarrer` `bc` ON `bc`.`carrer` = `c`.`id`
            LEFT JOIN `barrisagrupats` `ba` ON `bc`.`barri` = `ba`.`barri`
        WHERE `ba`.`grup` = :grup';

        if($lletra) {
            $lletra = strtolower(substr($lletra, 0, 1));

            $query.= ' AND `nom` LIKE :lletra';
            $params[':lletra'] = $lletra.'%';
        }

        $query.='
        GROUP BY `c`.`id`
        ORDER BY `nom`, `via` ASC';

        return $db->query($query, $params);
    }
    
    public static function obteNomCarrer($id) {
        if(empty($id)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            `via`,
            `nom`
        FROM `carrers`
        WHERE `id` = :id
        ';
        $params = array(':id' => $id);

        $carrer =  $db->query($query, $params);

        if(is_array($carrer) && isset($carrer[0])) {
            $carrer = $carrer[0];

            return $carrer['via'] . ' ' . $carrer['nom'];
        } else {
            return '';
        }
    }

    public static function obteMunicipiBarri($id) {
        if(empty($id)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            `municipi`
        FROM `barris`
        WHERE `id` = :id
        ';
        $params = array(':id' => $id);

        $carrer =  $db->query($query, $params);

        if(is_array($carrer) && isset($carrer[0])) {
            return $carrer[0]['municipi'];
        } else {
            return 0;
        }
    }

    public static function obteMunicipiCarrer($id) {
        if(empty($id)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            `municipi`
        FROM `carrers`
        WHERE `id` = :id
        ';
        $params = array(':id' => $id);

        $carrer =  $db->query($query, $params);

        if(is_array($carrer) && isset($carrer[0])) {
            return $carrer[0]['municipi'];
        } else {
            return 0;
        }
    }

    public static function afegeixCarrer($via, $nom, $municipi) {
        $db = Database::getInstance();

        $query = '
        INSERT INTO `carrers` (
            `via`,
            `nom`,
            `municipi`
        ) VALUES (
            :via,
            :nom,
            :municipi
        )
        ';
        $params = array(
            ':via' => $via,
            ':nom' => $nom,
            ':municipi' => $municipi
        );

        return $db->insert($query, $params);
    }

    public static function obteIDCarrerPerNom($nom) {
        if(empty($nom)) {
            return 0;
        }

        $db = Database::getInstance();

        $query = 'SELECT `id` FROM `carrers` WHERE `nom` = :nom';
        $params= array(':nom' => $nom);

        $result = $db->query($query, $params);

        if(!is_array($result) || !isset($result[0])) {
            return 0;
        }

        return $result[0]['id'];
    }

    public static function obteIDCarrerPerNomComplet($via, $nom, $municipi) {
        if(empty($via) || empty($nom)) {
            return 0;
        }

        $db = Database::getInstance();

        $query = 'SELECT `id` FROM `carrers` WHERE `via` = :via AND `nom` = :nom AND `municipi` = :municipi';
        $params= array(
            ':nom' => $nom,
            ':via' => $via,
            ':municipi' => $municipi
        );

        $result = $db->query($query, $params);

        if(!is_array($result) || !isset($result[0]) || !is_numeric($result[0]['id'])) {
            return 0;
        }

        return $result[0]['id'];
    }
    
    //Direccions
    public static function mostraCarrer($id, $count, $offset) {
        if(empty($id)) {
            return false;
        }

        $count = intval($count);

        $db = Database::getInstance();

        $query = '
        SELECT
            `id`,
            `text`
        FROM `direccions`
        WHERE `carrer` = :id
        ORDER BY `carrer`, `numero`, `ordrePis` ASC
        ';
        $params = array(':id' => $id);

        $query.= 'LIMIT ' . $offset . ',' . $count;

        return $db->query($query, $params);
    }

    public static function mostraActuacionsCarrer($id, $barri, $count, $offset) {
        if(empty($id)) {
            return false;
        }

        $count = intval($count);

        $db = Database::getInstance();

        $query = '
        SELECT
            `d`.`id`,
            `d`.`text`,
            GROUP_CONCAT(`fp`.`educador` SEPARATOR ",") AS `educadors`,
            GROUP_CONCAT(`fp`.`actuacio` SEPARATOR ",") AS `actuacions`
        FROM `direccions` `d`
            LEFT JOIN `formularipoblacio` `fp` ON `fp`.`direccio` = `d`.`id` AND `fp`.`ocult` = 0
            LEFT JOIN `barrisagrupats` `b` ON `b`.`barri` = `d`.`barri`
        WHERE `d`.`carrer` = :id
        AND `b`.`grup` = :barri
        GROUP BY `d`.`text`
        ORDER BY `d`.`carrer`, `d`.`numero`, `d`.`ordrePis` ASC
        ';
        $params = array(
            ':id' => $id
            ,':barri' => $barri
        );

        $query.= 'LIMIT ' . $offset . ',' . $count;

        return $db->query($query, $params);
    }

    public static function mostraOpcions() {
        $query = '
        SELECT
            `a`.`id`,
            `l`.`abreviacio`,
            `l`.`nom`,
            `a`.`informat`,
            `a`.`perfil`
        FROM `actuacions` `a`
            LEFT JOIN `actuacions_labels` `l` ON `a`.`id` = `l`.`actuacio`
        WHERE `l`.`idioma` = :idioma';

        return Database::getInstance()->query($query, array(':idioma' => Sessions::getVar('language')));
    }

    public static function obteNombreLlarsCarrer($id, $barri) {
        if(empty($id)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            COUNT(`d`.`id`) AS count
        FROM `direccions` `d`
        LEFT JOIN `barrisagrupats` `b` ON `b`.`barri` = `d`.`barri`
        WHERE `d`.`carrer` = :carrer
        AND `b`.`grup` = :barri
        ';
        $params = array(
            ':carrer' => $id
            ,':barri' => $barri
        );

        $llars = $db->query($query, $params);

        if(!is_array($llars) || !isset($llars[0])) {
            return 0;
        } else {
            return $llars[0]['count'];
        }
    }

    public static function cercaPrimeraPosicioNumero($carrer, $barri, $numero) {
        if(empty($carrer)||empty($barri)||empty($numero)) {
            throw new Exception('Invalid params');
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            `d`.`id`
        FROM `direccions` `d`
        LEFT JOIN `barrisagrupats` `b` ON `b`.`barri` = `d`.`barri`
        WHERE `d`.`carrer` = :carrer
        AND `d`.`numero` < :numero
        AND `b`.`grup` = :barri
	GROUP BY `d`.`text`
        ORDER BY `d`.`carrer`, `d`.`numero`, `d`.`ordrePis` ASC
        ';
        $params = array(
            ':carrer' => $carrer,
            ':barri' => $barri,
            ':numero' => $numero
        );
        $llista = $db->query($query, $params);

        if(!is_array($llista) || count($llista) < 1) {
            return 0;
        } else {
            return count($llista);
        }
    }

    public static function afegeixFormulariPoblacio($direccio, $actuacio, $educador, $data, $ocult) {
        $db = Database::getInstance();

        $insertQuery = '
        INSERT INTO `formularipoblacio` (
            `direccio`,
            `actuacio`,
            `educador`,
            `data`,
            `ocult`
        ) VALUES (
            :direccio,
            :actuacio,
            :educador,
            :data,
            :ocult
        )
        ';
        $insertParams = array(
            ':direccio' => $direccio,
            ':actuacio' => $actuacio,
            ':educador' => $educador,
            ':data' => $data,
            ':ocult' => $ocult
        );

        return $db->insert($insertQuery, $insertParams);
    }

    public static function desaResolucioDireccio($direccio, $actuacio) {
        if(empty($direccio) || empty($actuacio)) {
            return false;
        }

        $educador = $_SESSION['userID'];

        if(empty($educador)) {
            return false;
        }

        $db = new DBTable('formularipoblacio');
        $db->direccio = $direccio;
        $db->actuacio = $actuacio;

        $db->primaryKey = $db->select();
        if(empty($db->primaryKey))
            $db->educador = $educador;

        $db->ocult = '0';
        
        $db->store();

        return true;
    }

    public static function eliminaResolucioDireccio($direccio, $actuacio) {
        if(empty($direccio) || empty($actuacio)) {
            return false;
        }

        $educador = $_SESSION['userID'];

        if(empty($educador)) {
            return false;
        }

        $db = new DBTable('formularipoblacio');
        $db->direccio = $direccio;
        $db->actuacio = $actuacio;

        $db->primaryKey = $db->select();
        if(empty($db->primaryKey))
            $db->educador = $educador;

        $db->ocult = '1';

        $db->store();

        return true;
    }

    public static function afegeixDireccioPerText($barri, $carrer, $numero, $ordrePis, $text) {
        $db = Database::getInstance();

        $query = '
        INSERT INTO `direccions` (
            `barri`,
            `carrer`,
            `numero`,
            `ordrePis`,
            `text`
        ) VALUES (
            :barri,
            :carrer,
            :numero,
            :ordrePis,
            :text
        )';
        $params = array(
            ':barri' => $barri,
            ':carrer' => $carrer,
            ':numero' => $numero,
            ':ordrePis' => $ordrePis,
            ':text' => $text
        );

        return $db->insert($query, $params);
    }

    public static function afegeixDireccio($carrerID, $grupbarri, $numero, $planta, $porta) {
        $userID = $_SESSION['userID'];

        if(!isset($userID)) {
            return false;
        }

        $simbols = array(
            'ST' => 0,
            'BJ' => 1,
            'IN' => 2,
            'SS' => 3,
            'EN' => 4,
            'SO' => 5,
            'AT' => 1000,
            'SA' => 1100
        );

        if(isset($simbols[$planta])) {
            $plantavalue = $simbols[$planta];
        } elseif(is_numeric($planta)) {
            $plantavalue = intval($planta) * 10;
        }

        $db = Database::getInstance();

        $queryb = '
            SELECT `d`.`barri`
            FROM `direccions` `d`
            LEFT JOIN `barrisagrupats` `b` ON `b`.`barri` = `d`.`barri`
            WHERE `numero` <= :numero
            AND `carrer` = :carrer
            AND `grup` = :grupbarri
            LIMIT 1
        ';

        $paramsb = array(
            ':numero' => $numero,
            ':carrer' => $carrerID,
            ':grupbarri' => $grupbarri
        );

        $barrib = $db->query($queryb, $paramsb);

        if(!is_array($barrib) || !isset($barrib[0])) {
            $queryb = '
                SELECT `d`.`barri`
                FROM `direccions` `d`
                LEFT JOIN `barrisagrupats` `b` ON `b`.`barri` = `d`.`barri`
                WHERE `numero` >= :numero
                AND `carrer` = :carrer 
                AND `grup` = :grupbarri
                LIMIT 1
            ';
            $paramsb = array(
                ':numero' => $numero,
                ':carrer' => $carrerID,
                ':grupbarri' => $grupbarri
            );

            $barrib = $db->query($queryb, $paramsb);

            if(!is_array($barrib) || !isset($barrib[0])){
                $barri = 0;
            }else{
                $barri = $barrib[0]['barri'];
            }
        }else{
            $barri = $barrib[0]['barri'];
        }

        $query = '
        INSERT INTO `direccions` (
            `carrer`,
            `barri`,
            `numero`,
            `ordrePis`,
            `text`
        ) VALUES (
            :carrer,
            :barri,
            :numero,
            :ordrePis,
            :text
        )';
        $params = array(
            ':carrer' => $carrerID,
            ':barri' => $barri,
            ':numero' => $numero,
            ':ordrePis' => $plantavalue,
            ':text' => $numero .' Pl. '. $planta .' Pta. '. $porta
        );

        return $db->exec($query, $params);
    }

    public static function actualitzaDireccio($id, $carrer, $text) {
        if(empty($id)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        UPDATE TABLE `direccions` SET
            `carrer` = :carrer,
            `text` = :text
        WHERE `id` = :id';
        $params = array(
            ':id' => $id,
            ':carrer' => $carrer,
            ':text' => $text,
        );

        return $db->exec($query, $params);
    }

    public static function llistaOpcionsDireccio($idCarrer) {
        if(empty($idCarrer)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            `d`.`id`,
            `d`.`text`,
            `fp`.`educador`,
            `fp`.`actuacio`
        FROM `direccions` `d`
            LEFT JOIN `formularipoblacio` `fp` ON `fp`.`direccio` = `d`.`id`
        WHERE `d`.`carrer` = :idCarrer
        ORDER BY `carrer`, `numero`, `ordrePis` ASC';
        $params = array(':idCarrer' => $idCarrer);

        return $db->query($query, $params);
    }

    public static function eliminaDireccio($id) {
        if(empty($id)) {
            return false;
        }

        $db = Database::getInstance();

        $query = 'DELETE FROM `direccions` WHERE `id` = :id';
        $params = array(':id' => $id);

        return $db->exec($query, $params);
    }

    public static function obteIdDireccio($barri, $via, $carrer, $text) {
        if(empty($barri)||empty($via)||empty($carrer)||empty($text)) {
            return 0;
        }
        
        $db = Database::getInstance();

        $query = '
        SELECT
            `d`.`id`
        FROM `direccions` `d`
            LEFT JOIN `carrers` `c` ON `c`.`id` = `d`.`carrer`
            LEFT JOIN `barris` `b` ON `b`.`id` = `d`.`barri`
        WHERE
            `b`.`nom` = :barri AND
            `c`.`via` = :via AND
            `c`.`nom` = :carrer AND
            `d`.`text` = :text
        ';
        $params = array(
            ':barri' => $barri,
            ':via' => $via,
            ':carrer' => $carrer,
            ':text' => $text
        );

        $direccio = $db->query($query, $params);

        if(is_array($direccio) && isset($direccio[0])) {
            return $direccio[0]['id'];
        } else {
            return 0;
        }

        $result = $db->query($query, $params);

        if(!is_array($result) || !isset($result[0])) {
            return 0;
        }

        return $result[0]['id'];
    }

    public static function obteIdDireccioPerNom($nom, $carrer) {
        $db = Database::getInstance();

        $query = '
        SELECT
            `id`
        FROM `direccions`
        WHERE
            `text` LIKE %:nom%
            AND `carrer` = :carrer
        LIMIT 1';
        $params = array(
            ':nom' => $nom
            ,':carrer' => $carrer
        );

        $result = $db->query($query, $params);

        if(!is_array($result) || !isset($result[0])) {
            return 0;
        }

        return $result[0]['id'];
    }

    private static function calculaOrdreRelatiuNumero($text) {
        $pos = stripos($text, 'Pl.');

        if($pos) {
            $simbols = array(
                'ST' => 0,
                'BJ' => 1,
                'BX' => 1,
                'E'  => 1,
                'IN' => 2,
                'SS' => 3,
                'EN' => 4,
                'SO' => 5,
                'AT' => 1000,
                'SA' => 1100,
                'SM' => 1100
            );

            $pieces = explode(' ', $text);
            foreach($pieces as $i => $p) {
                if($p == 'Pl.' && isset($pieces[$i+1])) {
                    $planta = $pieces[$i+1];

                    if(isset($simbols[$planta])) {
                        return $simbols[$planta];
                    } elseif(is_numeric($planta)) {
                        return intval($planta) * 10;
                    }
                }
            }
        }

        return 0;
    }

    public static function obtePosicioDireccioPerId($id, $carrer) {
        $db = Database::getInstance();

        $query = '
        SELECT
            COUNT(`id`) AS count
        FROM `direccions`
            WHERE `id` < :id
            AND `carrer` = :carrer
            ORDER BY `carrer`, `numero`, `ordrePis` ASC';
        $params= array(
            ':id' => $id
            ,':carrer' => $carrer
        );

        $result = $db->query($query, $params);

        if(!is_array($result) || !isset($result[0])) {
            return 0;
        }

        return $result[0]['count'];
    }

    public static function obteRangDireccionsCarrer($id) {
        if(empty($id)) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            MAX(`id`) AS maxID,
            MIN(`id`) AS minID
        FROM `direccions`
        WHERE `carrer` = :carrer
        LIMIT 1
        ';
        $params = array(':carrer' => $id);

        $rang = $db->query($query, $params);

        if(!is_array($rang) || !isset($rang[0])) {
            return false;
        }

        $maxID = $rang[0]['maxID'];
        $minID = $rang[0]['minID'];

        return array($minID, $maxID);
    }

    //Altres
    private static function reparaMunicipisBD() {
        $db = Database::getInstance();

        //Repara municipis
        $query = '
        SELECT
            `nom`,
            `id`
        FROM `municipis`
        ORDER BY `nom` ASC
        ';

        $municipis = $db->query($query);

        if(is_array($municipis)) {
            $repeticions = array();
            $municipi = new DBTable('municipis');

            foreach($municipis as $m) {
                $nom = cleanString($m['nom']);

                if(isset($repeticions[$nom])) {
                    $oldID = $repeticions[$nom];
                    $newID = intval($m['id']);

                    if($newID < $oldID) {
                        $municipi->setFieldValue('id', $oldID);
                        $municipi->delete();

                        $repeticions[$nom] = $newID;
                    } else {
                        $municipi->setFieldValue('id', $newID);
                        $municipi->delete();
                    }
                } else {
                    $repeticions[$nom] = $m['id'];

                    $municipi->setFieldValue('id', $m['id']);
                    $municipi->nom = $nom;
                    $municipi->store();
                }
            }
        }
        //Fi de municipis
    }

    private static function reparaBarrisDB() {
        $db = Database::getInstance();

        //Repara municipis
        $query = '
        SELECT
            `nom`,
            `id`,
            `municipi`
        FROM `barris`
        ORDER BY `nom` ASC
        ';

        $carrers = $db->query($query);

        if(is_array($carrers)) {
            $repeticions = array();
            $carrer = new DBTable('barris');

            foreach($carrers as $c) {
                $nom = cleanString($c['nom']);

                if(isset($repeticions[$nom])) {
                    $oldID = $repeticions[$nom];
                    $newID = intval($c['id']);

                    if($newID < $oldID) {
                        $carrer->setFieldValue('id', $oldID);
                        $carrer->delete();

                        $repeticions[$nom] = $newID;
                    } else {
                        $carrer->setFieldValue('id', $newID);
                        $carrer->delete();
                    }
                } else {
                    $repeticions[$nom] = $c['id'];

                    $carrer->setFieldValue('id', $c['id']);
                    $carrer->nom = $nom;
                    $carrer->municipi = $c['municipi'];
                    $carrer->store();
                }
            }
        }
    }

    private static function reparaCarrersBD() {
        $db = Database::getInstance();

        //Repara municipis
        $query = '
        SELECT
            `nom`,
            `id`,
            `municipi`
        FROM `carrers`
        ORDER BY `nom` ASC
        ';

        $carrers = $db->query($query);

        if(is_array($carrers)) {
            $repeticions = array();
            $carrer = new DBTable('carrers');

            foreach($carrers as $c) {
                $nom = cleanString($c['nom']);

                if(isset($repeticions[$nom])) {
                    $oldID = $repeticions[$nom];
                    $newID = intval($c['id']);

                    if($newID < $oldID) {
                        $carrer->setFieldValue('id', $oldID);
                        $carrer->delete();

                        $repeticions[$nom] = $newID;
                    } else {
                        $carrer->setFieldValue('id', $newID);
                        $carrer->delete();
                    }
                } else {
                    $repeticions[$nom] = $c['id'];

                    $carrer->setFieldValue('id', $c['id']);
                    $carrer->nom = $nom;
                    $carrer->municipi = $c['municipi'];
                    $carrer->store();
                }
            }
        }
    }

    public static function importaDireccions(array $numeros) {
        $userID = $_SESSION['userID'];

        if(empty($userID)) {
            throw new Exception('Permission denied');
        }

        $query = '
        INSERT INTO `direccions` (
            `carrer`,
            `barri`,
            `numero`,
            `ordrePis`,
            `text`
        ) VALUES (
            :carrer,
            :barri,
            :numero,
            :ordre,
            :text
        )
        ';

        $carrer = 0;
        $barri = 0;
        $numeroCarrer = 0;
        $ordre = 0;
        $text = '';

        $stat = Database::getInstance()->getStatement($query);
        $stat->bindParam(':barri', $barri, PDO::PARAM_INT);
        $stat->bindParam(':carrer', $carrer, PDO::PARAM_INT);
        $stat->bindParam(':numero', $numeroCarrer, PDO::PARAM_INT);
        $stat->bindParam(':ordre', $ordre, PDO::PARAM_INT);
        $stat->bindParam(':text', $text, PDO::PARAM_STR);

        foreach($numeros as $numero) {
            $carrer = $numero[0];
            $barri = $numero[1];
            $text = $numero[2];
            $ordre = self::calculaOrdreRelatiuNumero($numero[2]);
            $numeroCarrer = $numero[2];

            $stat->execute();
        }

        return true;
    }
}

?>