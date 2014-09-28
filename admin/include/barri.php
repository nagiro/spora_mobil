<?php
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);
    } else {
        $id = 0;
    }

    $barri = new DBTable('barris', $id);
    
    if(empty($id)) {
        $titol = i18n::t('Afegeix barri');
    } else {
        $titol = i18n::t('Edita: %s', array($barri->via . ' ' . $barri->nom));
    }
?>

<h2><?php echo $titol; ?></h2>

<script type="text/javascript" language="javascript">
    $(function(){
        $('#areaForm').submit(function(){
            request('?action=saveArea', {
                id: $('#id').val(),
                nom: $('#nom').val(),
                municipi: $('#municipi').val()
            });
            return false;
        });
    });
</script>

<form action="#" method="post" id="areaForm">
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
            <label for="nom"><?php echo i18n::t('Nom'); ?></label>
        </div>

        <div class="fieldInput">
            <input type="text" name="nom" id="nom" value="<?php echo $barri->nom; ?>" />
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="municipi"><?php echo i18n::t('Municipi'); ?></label>
        </div>

        <div class="fieldInput">
            <select name="municipi" id="municipi">
                <?php
                    $municipis = Poblacions::llistaMunicipis();

                    foreach($municipis as $m) {
                        if($m['id'] == $barri->municipi) {
                            echo '<option value="' . $m['id'] . '" selected="selected">' . $m['nom'] . '</option>';
                        } else {
                            echo '<option value="' . $m['id'] . '">' . $m['nom'] . '</option>';
                        }
                    }
                ?>
            </select>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="desa">&nbsp;</label>
        </div>

        <div class="fieldInput">
            <input type="submit" name="desa" value="<?php echo i18n::t('Desa el barri'); ?>" />
            <input type="reset" name="reset" value="<?php echo i18n::t('Esborra camps'); ?>" />
        </div>
    </div>
</form>