<?php

    if(isNumericParam('id')) {
        $id = intval($_REQUEST['id']);
    } else {
        $id = 0;
    }

    $seguiment = new DBTable('formulariseguiment', $id);

?>

<script type="text/javascript" language="javascript">
    $(function(){
        $('#seguimentForm').submit(function(){
            request('?action=saveSeguiment', {
                id: $('#id').val(),
                productor: $('#productor').val(),
                participa: $('#participa').is(':checked'),
                grauSatisfaccio: $('#grauSatisfaccio').val(),
                queixes: $('#queixes').val(),
                dubtes: $('#dubtes').val(),
                comentaris: $('#comentaris').val(),
                suggeriments: $('#suggeriments').val()
            });
            return false;
        });
    });
</script>

<h2><?php echo i18n::t('Necessitat de contenidors'); ?></h2>

<form action="#" method="post" id="seguimentForm">
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
            <label for="productor"><?php echo i18n::t('Productor'); ?></label>
        </div>

        <div class="fieldInput">
            <select name="productor" id="productor">
                <?php
                    $productors = Productors::llistaProductors();

                    if(is_array($productors)) {
                        foreach($productors as $p) {
                            if($p['id'] == $seguiment->productor) {
                                echo '<option value="' . $p['id'] . '" selected="selected">' . $p['nom'] . '</option>';
                            } else {
                                echo '<option value="' . $p['id'] . '">' . $p['nom'] . '</option>';
                            }
                        }
                    }
                ?>
            </select>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="participa"><?php echo i18n::t('Servei de recollida selectiva'); ?></label>
        </div>

        <div class="fieldInput">
            <input type="checkbox" name="participa" id="participa" <?php echo ($seguiment->participaRecollidaSelectiva == 'Sí')? 'checked="checked"' : ''; ?> />
            <label><?php echo i18n::t('Participa en el servei de recollida'); ?></label>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="grauSatisfaccio"><?php echo i18n::t('Satisfacció amb el servei'); ?></label>
        </div>

        <div class="fieldInput">
            <input type="number" name="grauSatisfaccio" id="grauSatisfaccio" value="<?php echo $seguiment->grauSatisfaccio; ?>" min="0" max="10" step="1" />
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="queixes"><?php echo i18n::t('Queixes'); ?></label>
        </div>

        <div class="fieldInput">
            <textarea cols="40" rows="8" name="queixes" id="queixes"><?php echo $seguiment->queixes; ?></textarea>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="dubtes"><?php echo i18n::t('Dubtes'); ?></label>
        </div>

        <div class="fieldInput">
            <textarea cols="40" rows="8" name="dubtes" id="dubtes"><?php echo $seguiment->dubtes; ?></textarea>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="comentaris"><?php echo i18n::t('Comentaris'); ?></label>
        </div>

        <div class="fieldInput">
            <textarea cols="40" rows="8" name="comentaris" id="comentaris"><?php echo $seguiment->comentaris; ?></textarea>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="suggeriments"><?php echo i18n::t('Suggeriments'); ?></label>
        </div>

        <div class="fieldInput">
            <textarea cols="40" rows="8" name="suggeriments" id="suggeriments"><?php echo $seguiment->suggeriments; ?></textarea>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel"></div>

        <div class="fieldInput">
            <input type="submit" class="button" value="<?php echo i18n::t('Desa'); ?>" />
        </div>
    </div>
</form>