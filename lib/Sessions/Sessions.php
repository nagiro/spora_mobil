<?php

abstract class Sessions {
    private static $page = '';
    
    public static function login($username, $password) {

        self::endSession();
        self::startSession();

        if(empty($username) || empty($password)) {
            return false;
        }

        $userInfo = Users::checkCredentials($username, $password);

        if(!is_array($userInfo)) {
            return false;
        }
         
        $_SESSION['userID']     = $userInfo['id'];
        $_SESSION['username']   = $username;
        $_SESSION['name']       = $userInfo['nom'];
        $_SESSION['profile']    = $userInfo['profile'];
        $_SESSION['municipi']   = Users::obteMunicipisUsuari($userInfo['id']);
        
        Sessions::setVar('language', $userInfo['language']);
        
        //$_SESSION['userID']     = 1;
        //$_SESSION['username']   = 'lluis';
        //$_SESSION['name']       = 'Lluís';
        //$_SESSION['profile']    = 'Administrador';
        //$_SESSION['municipi']   = Users::obteMunicipisUsuari(8);
        
        //Sessions::setVar('language', 'ca');        

        return true;
    }

    public static function logout() {
        self::endSession();
        self::startSession();
    }

    public static function startSession() {
        session_start();
	ob_start();
    }

    public static function endSession() {
        session_destroy();
    }

    public static function isLogged() {
        return ( isset($_SESSION['userID']) && !empty($_SESSION['userID']) );
    }

    public static function setPage($page) {
        if(!self::isLogged()) {
            self::$page = 'login';
        } elseif(self::pageExists($page)) {
            self::$page = $page;
        } else {
            self::$page = 'menu';
        }
        
        i18n::load(self::$page);
    }

    public static function getPage() {
	if(empty(self::$page)) {
	    self::$page = self::isLogged()? 'menu' : 'login';
	}

        return self::$page;
    }

    public static function setVar($key, $value) {
        if(!isset($_SESSION['vars'])) {
            $_SESSION['vars'] = array();
        }

        $_SESSION['vars'][$key] = $value;
    }

    public static function getVar($key) {
        if(!isset($_SESSION['vars'])) {
            $_SESSION['vars'] = array();
        }

        return isset($_SESSION['vars'][$key])? $_SESSION['vars'][$key] : null;
    }

    public static function isVar($key) {
        return (isset($_SESSION['vars']) && isset($_SESSION['vars'][$key]));
    }

    private static function pageExists($page) {
        $path = INCLUDE_DIR . '/' . basename($page) . '.php';

        return (file_exists($path) && is_readable($path));
    }

    public static function getAuxiliarInfo($group) {
        $query = 'SELECT `valor` FROM `infoauxiliar` WHERE `grup` = :grup AND `language` = :language';

        $result = Database::getInstance()->query($query, array(
            ':grup' => $group,
            ':language' => Sessions::getVar('language')
        ));
        $values = array();

        if(is_array($result)) {
            foreach($result as $r) {
                $values[] = $r['valor'];
            }
        }

        return $values;
    }

    public static function listLanguages() {
        $query = 'SELECT `id`, `nom` FROM `idiomes`';
        
        return Database::getInstance()->query($query);
    }

    public static function redirect($to) {
        $to = basename(trim($to));
        header('Location: index.php?page=' . $to);
        exit;
    }

    public static function ajaxRedirect($to) {
        $redirect = array('redirect' => '?page=' . $to);
        echo json_encode($redirect);
        exit;
    }

    public static function isAdmin() {
        return (isset($_SESSION['profile']) && $_SESSION['profile'] == Users::PERFIL_ADMINISTRADOR);
    }

    public static function isMaintainer() {
        return (isset($_SESSION['profile']) && $_SESSION['profile'] == Users::PERFIL_MANTENIDOR);
    }

    public static function getProfile() {
	if(self::isLogged() && isset($_SESSION['profile'])) {
	    return $_SESSION['profile'];
	} else {
	    return '';
	}
    }

    public static function drawPage() {
        $page = Sessions::getPage();

        if($page == 'login') {
            i18n::load($page);
            
            require INCLUDE_DIR . '/login.php';
        } else {
            i18n::load('_system');
            
            require INCLUDE_DIR . '/header.php';

            require INCLUDE_DIR . '/' . $page . '.php';

            require INCLUDE_DIR . '/footer.php';
        }
    }

    public static function drawPageBody($page) {
        i18n::load($page);
        i18n::load('_system');
        
        require INCLUDE_DIR . '/' . $page . '.php';
    }
}

?>