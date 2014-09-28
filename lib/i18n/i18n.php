<?php

abstract class i18n {
    public static function load($page) {
        if(Sessions::isVar('language')) {
            $language = Sessions::getVar('language');
        } else {
            $language = 'es';
        }

        $page = basename($page);
        $lang = basename($language);
        $path = INCLUDE_DIR . '/' . I18N_DIR . '/' . $page . '/' . $lang . '.php';

        if(is_readable($path)) {
            global $translation;

            include $path;
        }
    }
    
    public static function t($string, $tokens = null) {
        global $translation;
        
        $string = isset($translation[$string])? $translation[$string] : $string;

        if(is_array($tokens) && count($tokens)) {
            $parts = explode('%s', $string);
            $string = '';

            foreach($parts as $i => $str) {
                if($str) {
                    $string.= $str . $tokens[$i];
                }
            }
            
            return $string;
        } else {
            return $string;
        }
    }
}

?>