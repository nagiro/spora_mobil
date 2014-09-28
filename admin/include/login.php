<?php
    if(Sessions::isLogged()) {
        Sessions::redirect('dashboard');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Panel d'administració d'SPORA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="robots" content="noindex,nofollow" />
        <style type="text/css">
            body {
                font-family: Helvetica, sans-serif;
            }
        </style>
    </head>
    <body>
        <h1>Spora Login</h1>

        <form action="index.php?action=doLogin" method="POST">
            <fieldset>
                <p>
                    <label for="username"><?php echo i18n::t('Usuari'); ?></label>
                    <input type="text" name="username" id="username" />
                </p>

                <p>
                    <label for="password"><?php echo i18n::t('Contrasenya'); ?></label>
                    <input type="password" name="password" id="password" />
                </p>

                <input type="submit" value="<?php echo i18n::t('Entra'); ?>" />
            </fieldset>
        </form>
    </body>
</html>