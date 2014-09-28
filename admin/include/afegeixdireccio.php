<?php
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);
    } else {
        $id = 0;
    }

    $direccio = new DBTable('direccions', $id);

    if($direccio->isNewEntry()) {
        $titol = 'Nova direcció';
    } else {
        $titol = 'Edita direcció';
    }
?>
<h2><?php echo i18n::t($titol); ?></h2>

<script type="text/javascript" language="javascript">
    $(function(){
        $('#direccioForm').submit(function(){
            request('?action=saveDirection', {
                id: $('#id').val(),
                carrer: $('#carrer').val(),
                llar: $('#llar').val()
            });
            return false;
        });
    });
</script>

<form action="#" method="post" id="direccioForm">
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
            <label for="carrer"><?php echo i18n::t('Carrer'); ?></label>
        </div>

        <div class="fieldInput">
            <select name="carrer" id="carrer">
                <option value=""><?php echo i18n::t('Tria un carrer'); ?></option>
                <?php
                    $carrers = Poblacions::llistaCarrers();

                    if(is_array($carrers)) {
                        foreach($carrers as $c) {
                            if($c['id'] == $direccio->municipi) {
                                echo '<option selected="selected" value="' . $c['id'] . '">' . $c['via'] . ' ' . $c['nom'] . '</option>';
                            } else {
                                echo '<option value="' . $c['id'] . '">' . $c['via'] . ' ' . $c['nom'] . '</option>';
                            }
                        }
                    }
                ?>
            </select>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="llar"><?php echo i18n::t('Llar'); ?></label>
            </div>

            <div class="fieldInput">
                <input type="text" id="llar" name="llar" />
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel"></div>

            <div class="fieldInput">
                <input type="submit" value="<?php echo i18n::t('Desa'); ?>" />
            </div>
        </div>
    </div>
</form>