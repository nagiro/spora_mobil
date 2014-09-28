<?php
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);

    } else {
        $id = 0;
    }

    $activitat = new DBTable('activitats', $id);

    if($id) {
        $title = i18n::t('Activitat: %s', array($activitat->titol));
    } else {
        $title = i18n::t('Crea una activitat');
    }
?>

<h2><?php echo $title; ?></h2>

<form action="?action=saveActivity" method="POST">
    <div class="field">
        <div class="fieldLabel">
            <label for="titol"><?php echo i18n::t('Títol'); ?></label>
        </div>

        <div class="fieldInput">
            <input type="text" name="titol" id="titol" />
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="formulari"><?php echo i18n::t('Formulari'); ?></label>
        </div>

        <div class="fieldInput">
            <select name="formulari" id="formulari">
                <?php
                    $forms = new DBTable('formularisinstalats');
                    $forms = $forms->readAll();

                    foreach($forms as $f) {
                        echo '<option value="' . $f['id'] . '">' . $f['titol'] . '</option>' . PHP_EOL;
                    }
                ?>
            </select>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="contactes"><?php echo i18n::t('Contactes que hi participaran'); ?></label>
        </div>

        <div class="fieldInput">
            <select name="filtre-municipis" id="filtre-municipis">
                <option value="">- <?php echo i18n::t('Contactes'); ?> -</option>
                <?php
                    $db = Database::getInstance();

                    $contactes = new DBTable('contactes');
                    $contactes = $contactes->readAll();

                    foreach($contactes as $c) {
                        echo '<option value="' . $c['id'] . '">' . $c['nom'] . '</option>' . PHP_EOL;
                    }
                ?>
            </select>

            <table>
                <thead>
                    <th><?php echo i18n::t('Contacte'); ?></th>
                    <th><?php echo i18n::t('Tipus'); ?></th>
                    <th><?php echo i18n::t('Adreça'); ?></th>
                    <th><?php echo i18n::t('Accions'); ?></th>
                </thead>
                <tbody id="contactes"></tbody>
            </table>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">&nbsp;</div>

        <div class="fieldInput">
            <input type="submit" name="save" value="<?php echo i18n::t('Desa els canvis'); ?>" />
        </div>
    </div>
</form>