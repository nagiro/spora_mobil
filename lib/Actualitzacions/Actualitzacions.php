<?php

abstract class Actualitzacions {
    const PREFIX_BD = 'spora';

    //Totes les direccions
    public static function creaBackupDireccions($inputDBname = null, $outputDBname = null) {
        $previous_memory_limit = ini_get('memory_limit');
        ini_set('memory_limit', '128M');

        $result = false;
        $outputDB = Database::getInstance($outputDBname);

        if(!$outputDB->tableExists('backupdireccions')) {
            //És necessària la versió més nova de la base de dades per fer un backup
            return false;
        }

        //Agafa direccions per text (esquiva problemes de PK)
        $inputDB = Database::getInstance($inputDBname);

        if($inputDB->tableExists('barriscarrer')) {
            $direccions = $inputDB->query('
            SELECT
                `b`.`nom` AS barri,
                `c`.`via` AS via,
                `c`.`nom` AS carrer,
                `d`.`numero`,
                `d`.`ordrePis`,
                `d`.`text`
            FROM `direccions` `d`
                LEFT JOIN `barris` `b` ON `d`.`barri` = `b`.`id`
                LEFT JOIN `carrers` `c` ON `d`.`carrer` = `c`.`id` LIMIT 2000
            ');
        } else {
            $direccions = $inputDB->query('
            SELECT
                `b`.`nom` AS barri,
                `c`.`via` AS via,
                `c`.`nom` AS carrer,
                `d`.`numero`,
                `d`.`ordrePis`,
                `d`.`text`
            FROM `direccions` `d`
                LEFT JOIN `carrers` `c` ON `d`.`carrer` = `c`.`id`
                LEFT JOIN `barris` `b` ON `c`.`barri` = `b`.`id`
            ');
        }

        //Reinsereix amb un prepared statement (mes rapid)
        $barri = "";
        $via = "";
        $carrer = "";
        $numero = 0;
        $ordrePis = 0;
        $text = "";

        //Query per no duplicar entrades al backup
        $discardStatement = $outputDB->getStatement('
            SELECT
                `id`
            FROM `backupdireccions`
            WHERE
                `barri` LIKE :barri AND
                `via` LIKE :via AND
                `carrer` LIKE :carrer AND
                `text` LIKE :text
            LIMIT 1
        ');
        $discardStatement->bindParam(':barri', $barri, PDO::PARAM_STR);
        $discardStatement->bindParam(':via', $via, PDO::PARAM_STR);
        $discardStatement->bindParam(':carrer', $carrer, PDO::PARAM_STR);
        $discardStatement->bindParam(':text', $text, PDO::PARAM_STR);

        $insertStatement = $outputDB->getStatement('
            INSERT INTO `backupdireccions` (
                `barri`,
                `via`,
                `carrer`,
                `numero`,
                `ordrePis`,
                `text`
            ) VALUES (
                :barri,
                :via,
                :carrer,
                :numero,
                :ordrePis,
                :text
            )
        ');
        $insertStatement->bindParam(':barri', $barri, PDO::PARAM_STR);
        $insertStatement->bindParam(':via', $via, PDO::PARAM_STR);
        $insertStatement->bindParam(':carrer', $carrer, PDO::PARAM_STR);
        $insertStatement->bindParam(':numero', $numero, PDO::PARAM_INT);
        $insertStatement->bindParam(':ordrePis', $ordrePis, PDO::PARAM_INT);
        $insertStatement->bindParam(':text', $text, PDO::PARAM_STR);

        if(is_array($direccions)) {
            foreach($direccions as $d) {
                $barri = cleanString($d['barri']);
                $via = cleanString($d['via']);
                $carrer = cleanString($d['carrer']);
                $numero = $d['numero'];
                $ordrePis = $d['ordrePis'];
                $text = $d['text'];

                $entry = $discardStatement->fetch(PDO::FETCH_ASSOC);

                if(empty($entry)) {
                    $insertStatement->execute();
                }
            }

            $result = true;
        }

        ini_set('memory_limit', $previous_memory_limit);
        return $result;
    }

    public static function restauraBackupDireccions() {
        $previous_memory_limit = ini_get('memory_limit');
        ini_set('memory_limit', '128M');

        $db = Database::getInstance();

        //Cleanup
        $db->exec('TRUNCATE TABLE `direccions`');

        $selectQuery = '
        SELECT
            `barri`,
            `via`,
            `carrer`,
            `numero`,
            `ordrePis`,
            `text`
        FROM `backupdireccions`';
        $backups = $db->query($selectQuery);

        $insertQueryDireccio = '';

        if(is_array($backups)) {
            //"Caches" locals per estalviar consultes
            $barris = array();
            $carrers = array();

            foreach($backups as $b) {
                //Comprova barris i carrers
                $barri = $b['barri'];

                if(!isset($barris[$barri])) {
                    $barris[$barri] = Poblacions::obteIDBarriPerNom($barri);
                }

                $carrer = $b['via'] . ' ' . $b['carrer'];

                if(!isset($carrers[$carrer])) {
                    $carrers[$carrer] = Poblacions::obteIDCarrerPerNomComplet($b['via'], $b['carrer']);
                }

                //Insereix direcció si no hi és
                $novaID = Poblacions::afegeixDireccioPerText(
                    $barris[$barri],
                    $carrers[$carrer],
                    $b['numero'],
                    $b['ordrePis'],
                    $b['text']
                );
            }
        }

        ini_set('memory_limit', $previous_memory_limit);
        return true;
    }

    //Direccions que han tingut alguna actuació
    public static function creaBackupActuacions($inputDB = null, $outputDB = null) {
        $previous_memory_limit = ini_get('memory_limit');
        ini_set('memory_limit', '128M');

        $result = false;
        $db = Database::getInstance($outputDB);

        //Cleanup
        $db->exec('TRUNCATE TABLE `backupactuacions`');

        //Agafa direccions per text (esquiva problemes de PK)
        $direccions = Database::getInstance($inputDB)->query('
        SELECT
            `b`.`nom` AS barri,
            `c`.`via` AS via,
            `c`.`nom` AS carrer,
            `d`.`text`,
            `fp`.`actuacio`,
            `fp`.`educador`,
            `fp`.`data`,
            `fp`.`ocult`
        FROM `formularipoblacio` `fp`
            LEFT JOIN `direccions` `d` ON `d`.`id` = `fp`.`direccio`
            LEFT JOIN `barris` `b` ON `d`.`barri` = `b`.`id`
            LEFT JOIN `carrers` `c` ON `d`.`carrer` = `c`.`id`
        ');

        //Reinsereix amb un prepared statement (mes rapid)
        $barri = '';
        $via = '';
        $carrer = '';
        $text = '';
        $actuacio = 0;
        $educador = 0;
        $data = '';
        $ocult = 0;

        $insertQuery = '
        INSERT INTO `backupactuacions` (
            `barri`,
            `via`,
            `carrer`,
            `text`,
            `actuacio`,
            `educador`,
            `data`,
            `ocult`
        ) VALUES (
            :barri,
            :via,
            :carrer,
            :text,
            :actuacio,
            :educador,
            :data,
            :ocult
        )';
        $insertStatement = $db->getStatement($insertQuery);
        $insertStatement->bindParam(':barri', $barri, PDO::PARAM_STR);
        $insertStatement->bindParam(':via', $via, PDO::PARAM_STR);
        $insertStatement->bindParam(':carrer', $carrer, PDO::PARAM_STR);
        $insertStatement->bindParam(':text', $text, PDO::PARAM_STR);
        $insertStatement->bindParam(':actuacio', $actuacio, PDO::PARAM_INT);
        $insertStatement->bindParam(':educador', $educador, PDO::PARAM_INT);
        $insertStatement->bindParam(':data', $data, PDO::PARAM_STR);
        $insertStatement->bindParam(':ocult', $ocult, PDO::PARAM_INT);

        if(is_array($direccions)) {
            foreach($direccions as $d) {
                $barri = $d['barri'];
                $via = $d['via'];
                $carrer = $d['carrer'];
                $text = $d['text'];
                $actuacio = $d['actuacio'];
                $educador = $d['educador'];
                $data = $d['data'];
                $ocult = $d['ocult'];

                $insertStatement->execute();
            }

            $result = true;
        }

        ini_set('memory_limit', $previous_memory_limit);
        return $result;
    }

    public static function restauraBackupActuacions() {
        $previous_memory_limit = ini_get('memory_limit');
        ini_set('memory_limit', '128M');

        $db = Database::getInstance();

        //Cleanup
        $db->exec('TRUNCATE TABLE `formularipoblacio`');

        $selectQuery = '
        SELECT
            `id`,
            `barri`,
            `via`,
            `carrer`,
            `text`,
            `actuacio`,
            `educador`,
            `data`,
            `ocult`
        FROM `backupactuacions`';
        $backups = $db->query($selectQuery);

        $insertQueryDireccio = '';

        if(is_array($backups)) {
            //"Cache" locals per estalviar consultes
            $direccions = array();

            foreach($backups as $b) {
                $direccio = $b['text'];

                if(!isset($direccions[$direccio])) {
                    $novaID = Poblacions::obteIdDireccio(
                        $b['barri'],
                        $b['via'],
                        $b['carrer'],
                        $direccio
                    );

                    if(empty($novaID)) {
                        error_log('Error: entrada de direcció no trobada a backupactuacions, ID=' . $b['id']);
                        continue;
                    }

                    $direccions[$direccio] = $novaID;
                }

                //Insereix actuació
                Poblacions::afegeixFormulariPoblacio(
                    $direccions[$direccio],
                    $b['actuacio'],
                    $b['educador'],
                    $b['data'],
                    $b['ocult']
                );
            }
        }

        ini_set('memory_limit', $previous_memory_limit);
        return true;
    }

    //Backups entre diverses BD del servidor
    public static function llistaBDs() {
        $dbs = Database::getInstance()->query('SHOW DATABASES');

        if(is_array($dbs)) {
            foreach($dbs as $i => $n) {
                $n = strtolower($n['Database']);

                if(strpos($n, self::PREFIX_BD) === 0) {
                    $dbs[$i] = $n;
                } else {
                    unset($dbs[$i]);
                }
            }

            return $dbs;
        } else {
            return false;
        }
    }

    public static function creaBackupServidor($dbs) {
        if(!is_array($dbs)) {
            throw new Exception('Incorrect params');
        }

        foreach($dbs as $database) {
            $db = Database::getInstance($database);

            if(!$db) {
                error_log('No s\'ha pogut connectar a la BD per realitzar backup: ' . $database);
                continue;
            }

            Actualitzacions::creaBackupDireccions($database);
            //Actualitzacions::creaBackupActuacions($database);
        }
    }
}

?>