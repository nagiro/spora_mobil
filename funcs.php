<?php

function isParam($key) {
    return (isset($_REQUEST[$key]) && !empty($_REQUEST[$key]));
}

function isNumericParam($key) {
    return (isset($_REQUEST[$key]) && !empty($_REQUEST[$key]) && is_numeric($_REQUEST[$key]));
}

function isArrayParam($key) {
    return (isset($_REQUEST[$key]) && !empty($_REQUEST[$key]) && is_array($_REQUEST[$key]));
}

function incrementCharCounter($char) {
    $ord = ord($char);

    return ($ord > 65 && $ord < 90)? chr($ord+1) : ($char . 'A');
}

function IsLatin1($str) {
    return (preg_match("/^[\\x00-\\xFF]*$/u", $str) === 1);
}

function cleanString($orig) {
    $orig = trim($orig);
        
    if(mb_detect_encoding($orig . 'a','UTF-8, ISO-8859-1') == "ISO-8859-1") {
       $orig = utf8_encode($orig);
    }

    $orig = mb_convert_case($orig, MB_CASE_TITLE, "UTF-8");
    $orig = eliminateMBChars($orig);

    return $orig;
}

//No elimina caracters multibyte (UTF8)
function cleanString2($orig) {
    $orig = trim($orig);

    if(mb_detect_encoding($orig . 'a','UTF-8, ISO-8859-1') == "ISO-8859-1") {
        $orig = utf8_encode($orig);
    }

    $orig = ucwords(strtolower($orig));

    return $orig;
}

function eliminateMBChars($str) {
    $t = array(
        'à' => 'a',     'á' => 'a',     'è' => 'e',     'é' => 'e',     'ì' => 'i',
        'í' => 'i',     'ò' => 'o',     'ó' => 'o',     'ù' => 'u',     'ú' => 'u',
        'ñ' => 'n',     'ç' => 'c',     '·' => '.',     'À' => 'A',     'Á' => 'A',
        'È' => 'E',     'É' => 'E',     'Í' => 'I',     'Ì' => 'I',     'Ò' => 'O',
        'Ó' => 'O',     'Ù' => 'U',     'Ú' => 'U',     'Ñ' => 'N',     'Ç' => 'C'
    );

    return str_replace(array_keys($t), array_values($t), $str);
}

function word_shift_left(&$str) {
    return shift_left($str, ' ');
}

function shift_left(&$str, $separator) {
    $pos = strpos($str, $separator);

    if($pos !== false) {
        $word = substr($str, 0, $pos);

        $str = substr($str, $pos+1);

        return $word;
    } else {
        $word = $str;
        $str = '';

        return $word;
    }
}

function word_shift_right(&$str) {
    return shift_right($str, ' ');
}

function shift_right(&$str, $separator) {
    $pos = strrpos($str, $separator);

    if($pos !== false) {
        $word = substr($str, $pos+1);

        $str = substr($str, 0, $pos);

        return $word;
    } else {
        $word = $str;
        $str = '';

        return $word;
    }
}

function validDateRange($start, $end) {
    list($di,$mi,$ai) = explode("/", $start);
    list($df,$mf,$af) = explode("/", $end);

    if(!checkdate($mi, $di, $ai)||!checkdate($mf, $df, $af)) {
        return false;
    }

    $tsFrom = mktime(0, 0, 0, $mi, $di, $ai);
    $tsEnd  = mktime(23,59,59,$mf, $df, $af);

    return ($tsFrom <= $tsEnd);
}

function date2Timestamp($date, $startDay = true) {
    list($d,$m,$a) = explode("/", $date);

    if($startDay) {
        $ts = mktime(0, 0, 0, $m, $d, $a);
    } else {
        $ts = mktime(23,59,59,$m, $d, $a);
    }

    return date('Y-m-d H:i:s', $ts);
}

function inRange($min, $val, $max) {
    return ($val >= $min) && ($val <= $max);
}

function valrange($min, $val, $max) {
    if($val <= $min) {
        return $min;
    } elseif($val >= $max) {
        return $max;
    } else {
        return $val;
    }
}

?>