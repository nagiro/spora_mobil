<?php
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);
    } else {
        $id = 0;
    }

    $usuari = new DBTable('usuaris', $id);

    if($id) {
        $title = i18n::t('Usuari: %s', array( empty($usuari->nom)? $usuari->username : $usuari->nom ));
    } else {
        $title = i18n::t('Crea un usuari');
    }
?>

<script type="text/javascript" language="javascript">
    $(function(){
        $('#userForm').submit(function(){            
            $.ajax({
                url: '?action=saveUser',
                type: 'post',
                data: {
                    id: $('#id').val(),
                    username: $('#username').val(),
                    nom: $('#nom').val(),
                    password1: $('#password1').val(),
                    password2: $('#password2').val(),
                    profile: $('#profile').val(),
                    municipis: $('#municipis').val(),
                    language: $('#language').val()
                },
                dataType: 'json',
                success: function(data) {
                    if(data.error) {
                        $('#error').show();
                        $('#errorText').text(data.error);
                    } else if(data.redirect) {
                        window.location = data.redirect;
                    }
                }
            });
            return false;
        });
    });
</script>

<h2><?php echo $title; ?></h2>

<form action="#" method="post" id="userForm">
    <div class="ui-widget" id="error" style="display: none;">
        <div class="fieldLabel"></div>

        <div class="fieldInput ui-state-error ui-corner-all" style="margin-left: 10px;">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
            <strong>Error:</strong> <span id="errorText"></span></p>
        </div>
    </div>
    
    <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />

    <div class="field">
        <div class="fieldLabel">
            <label for="username"><?php echo i18n::t('Nom d\'usuari'); ?></label>
        </div>

        <div class="fieldInput">
            <?php
                if($id !== 0) {
                    echo '<input type="text" disabled="disabled" name="username" id="username" value="' . $usuari->username . '" />';
                } else {
                    echo '<input type="text" name="username" id="username" />';
                }
            ?>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="nom"><?php echo i18n::t('Nom complet'); ?></label>
        </div>

        <div class="fieldInput">
            <input type="text" name="nom" id="nom" value="<?php echo $usuari->nom; ?>" />
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="password1"><?php echo i18n::t('Contrasenya'); ?></label>
        </div>

        <div class="fieldInput">
            <input type="password" name="password1" id="password1" />
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="password2"><?php echo i18n::t('Repeteix contrasenya'); ?></label>
        </div>

        <div class="fieldInput">
            <input type="password" name="password2" id="password2" />
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="profile"><?php echo i18n::t('Perfil'); ?></label>
        </div>

        <div class="fieldInput">
            <select name="profile" id="profile">
            <?php
                $tipusUsuari = $usuari->getEnumDomain('profile');

                foreach($tipusUsuari as $perfil) {
                    if($perfil == $usuari->profile) {
                        echo '<option value="' . $perfil . '" selected="selected">' . $perfil . '</option>';
                    } else {
                        echo '<option value="' . $perfil . '">' . $perfil . '</option>';
                    }
                }
            ?>
            </select>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="municipis"><?php echo i18n::t('Bloqueja l\'usuari als municipis'); ?></label>
        </div>

        <div class="fieldInput">
            <select name="municipis" id="municipis" size="10" multiple="multiple">
                <?php
                    $municipis = new DBTable('municipis');
                    $municipis->actiu = 1;
                    $municipis = $municipis->readAll();

                    $municipisUsuari = Users::obteMunicipisUsuari($id);

                    foreach($municipis as $m) {
                        if(in_array($m['id'], $municipisUsuari)) {
                            echo '<option value="' . $m['id'] . '" selected="selected">'. $m['nom'] . '</option>';
                        } else {
                            echo '<option value="' . $m['id'] . '">'. $m['nom'] . '</option>';
                        }
                    }
                ?>
            </select>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="language"><?php echo i18n::t('Idioma'); ?></label>
        </div>

        <div class="fieldInput">
            <select name="language" id="language">
            <?php
                $languages = Sessions::listLanguages();
                
                foreach($languages as $l) {
                    if($l['id'] == $usuari->language) {
                        echo '<option selected="selected" value="'.$l['id'].'">' . $l['nom'] . '</option>';
                    } else {
                        echo '<option value="'.$l['id'].'">' . $l['nom'] . '</option>';
                    }
                }
            ?>
            </select>
        </div>
    </div>
    
    <div class="field">
        <div class="fieldLabel">
            <label for="save">&nbsp;</label>
        </div>

        <div class="fieldInput">
            <input type="submit" name="save" id="save" value="<?php echo i18n::t('Desa els canvis'); ?>" />
            <input type="reset" value="<?php echo i18n::t('Esborra'); ?>" />
        </div>
    </div>
</form>