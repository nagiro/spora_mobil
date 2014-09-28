<?php

require '../settings.php';
require '../bootstrap.php';
require '../funcs.php';

Bootstrap::prepare();

Bootstrap::initBuffer();

if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])) {
    try {
        $action = 'action_' . basename($_REQUEST['action']);

        require_once 'form_callbacks.php';

        if(function_exists($action)) {
            call_user_func($action);
        }
    } catch(Exception $e) {
        $error = array('error' => $e->getMessage());
        echo json_encode($error);
    }
} else {
    if(isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
        Sessions::setPage( $_REQUEST['page'] );
    }

    Sessions::drawPage();
}

Bootstrap::releaseBuffer();

?>