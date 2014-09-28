<!DOCTYPE html>
<html>
    <head>
        <title><?php echo i18n::t('Panell d\'administració d\'SPORA'); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="robots" content="noindex,nofollow" />
        <link rel="stylesheet" type="text/css" href="../css/styles.css" />
        <link rel="stylesheet" type="text/css" href="../css/ui-lightness/jquery-ui-1.8.10.custom.css" />
        <script type="text/javascript" src="../scripts/jquery-1.4.4.min.js"></script>
        <script type="text/javascript" src="../scripts/jquery-ui-1.8.10.custom.min.js"></script>
        <script type="text/javascript" src="../scripts/jquery.ui.datepicker-ca.js"></script>
        <script type="text/javascript" src="../scripts/spora.js"></script>
        <script type="text/javascript" src="../scripts/jquery.dnd-file-upload.js" ></script>
        <script type="text/javascript" src="../scripts/jquery.tablesorter.min.js" ></script>
    </head>
    <body>
        <div id="wrapper">
            <div id="header">
                <h1><?php echo i18n::t('Panell d\'administració d\'SPORA'); ?></h1>

                <span class="usuari">
                    <span id="nomUsuari"><?php echo $_SESSION['username'] . ' (' . $_SESSION['profile'] . ')'; ?></span>
                    <span id="desconnecta"><a href="?action=logout" class="button"><?php echo i18n::t('Desconnecta'); ?></a></span>
                </span>
            </div>

            <div id="navbar">
                <ul>
		    <?php
			$opcions = Users::llistaOpcionsMenu();

			if(is_array($opcions)) {
			    $grupActual = '';

			    foreach($opcions as $grup => $g) {
				if(strcmp($grup, $grupActual)) {
				    $grupActual = $grup;
				    
				    echo '<li class="separator">' . $grup . '</li>';
				}

				foreach($g as $o) {
				    echo '<li><a class="changePage" href="?page=' . $o['href'] . '">' . $o['text'] . '</a></li>';
				}
			    }
			}
		    ?>
                </ul>
            </div>

            <div id="contents">