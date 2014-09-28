<?php require INCLUDE_DIR . '/header.php'; ?>

<div data-role="page">
    <div data-role="header" data-nobackbtn="true">
        <h1>SPORA Login</h1>
    </div><!-- /header -->

    <div data-role="content">
        <form action="index.php?action=doLogin" method="post">
            <div data-role="fieldcontain">
                <label for="username">Usuari:</label>
                <input type="text" name="username" id="username" value=""  />
            </div>

            <div data-role="fieldcontain">
                <label for="password">Contrasenya:</label>
                <input type="password" name="password" id="password" value=""  />
            </div>

            <p>
                <input type="submit" name="send" value="Entra" />
            </p>
        </form>
    </div><!-- /content -->
</div><!-- /Login page -->

<?php require INCLUDE_DIR . '/footer.php'; ?>