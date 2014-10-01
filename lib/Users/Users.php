<?php

abstract class Users {
    const PERFIL_ADMINISTRADOR  = 'Administrador';
    const PERFIL_EDUCADOR       = 'Educador';
    const PERFIL_CLIENT         = 'Client';
    const PERFIL_INFORMADOR     = 'Informador';
    const PERFIL_MANTENIDOR     = 'Mantenidor';
    
    public static function checkCredentials($username, $password) {
        if(empty($username) || empty($password)) {
            return false;
        }

        $db = Database::getInstance();

        $info = $db->query('
            SELECT
                `id`,
                `password`,
                `profile`,
                `nom`,
                `language`
            FROM `usuaris` WHERE `username` = :username', array(
            ':username' => $username
        ));

        if(!is_array($info) || !isset($info[0])) {
            return false;
        }

        $info = $info[0];
        $password = md5($password . md5($password));

        if(strcmp($info['password'], $password) == 0) {
            //Login successful
            return $info;
        } else {
            return false;
        }
    }

    public static function usernameExists($username) {
        if(empty($username) || !is_string($username)) {
            throw new Exception('Nom d\'usuari no vàlid');
        }

        $query = 'SELECT `id` FROM `usuaris` WHERE `username` = :username';
        $user = Database::getInstance()->query($query, array(':username' => $username));

        return isset($user[0]) && !empty($user[0]['id']);
    }

    public static function register($username, $password, $name, $profile, $language) {
        if(empty($username) || !is_string($username)) {
            throw new Exception('Nom d\'usuari no vàlid');
        }

        if(empty($password)) {
            throw new Exception('La contrasenya no és vàlida');
        }

        if(empty($profile)) {
            throw new Exception('El perfil no és vàlid');
        }

        if(Users::usernameExists($username)) {
            throw new Exception('El nom d\'usuari ja està sent utilitzat per un altre usuari');
        }

        $password = md5($password . md5($password));

        $user = new DBTable('usuaris');
        $user->username = $username;
        $user->password = $password;
        $user->profile  = $profile;
        $user->nom	= $name;
        $user->creacio	= date('Y-m-d H:i:s');
        $user->language = $language;
        //$user->municipi = 0;
        $user->store();
        
        return true;
    }

    public static function modifyUser($id, $password, $name, $profile, $language) {
        if(empty($id) || !is_numeric($id)) {
            throw new Exception('La ID de l\'usuari no és vàlida');
        }

        if(empty($profile)) {
            throw new Exception('El perfil no és vàlid');
        }

        $user = new DBTable('usuaris', $id);
        $user->profile  = $profile;
        $user->nom	= $name;
        $user->language = $language;

        if(!empty($password)) {
            $user->password = md5($password . md5($password));
        }

        $user->store();

        return true;
    }

    public static function llistaUsuaris($perfil = '') {
        $db = Database::getInstance();

        $query = '
        SELECT
            `id`,
            `username`,
            `creacio`,
            `profile`,
            `nom`
        FROM `usuaris`
        ';
        $params = array();

        if(!empty($perfil)) {
            $query.= ' WHERE `profile` = :profile';
            $params = array(':profile' => $perfil);
        }

        $query.= ' ORDER BY `nom`, `username` ASC';

        return $db->query($query, $params);
    }

    public static function llistaOpcionsMenu() {
        if(!Sessions::isLogged()) {
            return false;
        }

        $db = Database::getInstance();

        $query = '
        SELECT
            `m`.`pagina`,
            `mg`.`grup`,
            `ml`.`text`
        FROM `accesmenu` `a`
            LEFT JOIN `menu` `m` ON `a`.`menu` = `m`.`id`
            LEFT JOIN `menu_group_labels` `mg` ON `mg`.`language` = :language AND `mg`.`id` = `m`.`grup`
            LEFT JOIN `menu_entry_labels` `ml` ON `ml`.`language` = :language AND `ml`.`menu` = `m`.`id`
        WHERE `a`.`perfil` = :perfil
          AND `m`.`grup` > 0';

        $opcions = $db->query($query, array(
            ':perfil' => Sessions::getProfile(),
            ':language' => Sessions::getVar('language')
        ));

        $llista = array();

        foreach($opcions as $o) {
            $grup = $o['grup'];

            if(!isset($llista[$grup])) {
                $llista[$grup] = array();
            }

            $llista[$grup][] = array(
                'href' => $o['pagina'],
                'text' => $o['text']
            );
        }

        return $llista;
    }
    
    public static function desaMunicipisUsuari($id, array $municipis) {
        $query = '
        INSERT INTO `usuarismunicipis` VALUES (
            :usuari,
            :municipi
        )';
        $municipi = 0;
        
        $statement = Database::getInstance()->getStatement($query);
        $statement->bindValue(':usuari', $id, PDO::PARAM_INT);
        $statement->bindParam(':municipi', $municipi, PDO::PARAM_INT);
        
        foreach($municipis as $municipi) {
            $statement->execute();
        }
        
        return true;
    }

    public static function eliminaMunicipisUsuari($id) {
        $query = 'DELETE FROM `usuarismunicipis` WHERE `usuari` = :usuari';

        return Database::getInstance()->exec($query, array(':usuari' => $id));
    }

    public static function obteMunicipisUsuari($id) {
        $query = 'SELECT `municipi` FROM `usuarismunicipis` WHERE `usuari` = :usuari';
        $result = Database::getInstance()->query($query, array(':usuari' => $id));
        
        $municipis = array();
        
        foreach($result as $r) {
            $municipis[] = $r['municipi'];
        }        
        
        return $municipis;
    }
    
    public static function obteNomsMunicipisUsuari($id) {
        $query = '
        SELECT
            `m`.`id`,
            `m`.`nom`
        FROM `usuarismunicipis` `um`
            LEFT JOIN `municipis` `m` ON `m`.`id` = `um`.`municipi`
        WHERE `um`.`usuari` = :usuari';
        
        return Database::getInstance()->query($query, array(':usuari' => $id));
    }
}

?>