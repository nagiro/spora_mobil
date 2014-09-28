<?php

    $agrupacio = new DBTable('barrisagrupats');

    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);
        $agrupacio->grup = $id;
        $agrupacio->actiu = 1;
    } else {
        $id = 0;
    }

    $barris = $agrupacio->readAll();
    $llistaBarris = array();

    if(is_array($barris) && count($barris) > 0) {
        $municipi = Poblacions::obteMunicipiBarri($barris[0]['barri']);

        foreach($barris as $b) {
            $llistaBarris[] = $b['barri'];
        }
    } else {
        $municipi = 0;
    }
    
    if(empty($id)) {
        $titol = 'Afegeix agrupació';
    } else {
        $titol = 'Edita agrupació';
    }
?>

<script type="text/javascript">
    $(function(){
        $('#municipi').bind('change', function(){
            $('#barris').html('');
            
            $.getJSON('?action=get&table=barris', {municipi: $(this).val(), sort: 'nom'}, function(data){
                $.each(data, function(index, entry){
                    $('<option></option>')
                        .attr('value', entry.id)
                        .html(entry.nom)
                        .appendTo('#barris');
                });
            });
        });
    });
</script>

<h2><?php echo i18n::t($titol); ?></h2>

<form action="?action=saveAreaGrouping" method="POST">
    <?php
        if($id !== 0) {
            echo '<input type="hidden" name="id" value="' . $id . '" />';
        }
    ?>

    <div class="field">
        <div class="fieldLabel">
            <label for="municipi"><?php echo i18n::t('Municipi'); ?></label>
        </div>
        
        <div class="fieldInput">
            <select name="municipi" id="municipi">
                <option value=""><?php echo i18n::t('Tria un municipi'); ?></option>
                <?php
                    $municipis = Poblacions::llistaPoblacions();

                    if(is_array($municipis)) {
                        foreach($municipis as $m) {
                            if($m['id'] == $municipi) {
                                echo '<option value="' . $m['id'] . '" selected="selected">' . $m['nom'] . '</option>';
                            } else {
                                echo '<option value="' . $m['id'] . '">' . $m['nom'] . '</option>';
                            }
                        }
                    }
                ?>
            </select>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="barris"><?php echo i18n::t('Barris a l\'agrupació'); ?></label>
        </div>

        <div class="fieldInput">
            <select name="barris[]" id="barris" multiple="multiple" size="10">
                <?php
                    if(!empty($municipi)) {
                        $barris = Poblacions::llistaBarris($municipi);

                        foreach($barris as $b) {
                            if(in_array($b['id'], $llistaBarris)) {
                                echo '<option value="' . $b['id'] . '" selected="selected">' . $b['nom'] . '</option>';
                            } else {
                                echo '<option value="' . $b['id'] . '">' . $b['nom'] . '</option>';
                            }
                        }
                    } else {
                        echo '<option>' . i18n::t('Tria un municipi') . '</option>';
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
            <input type="submit" name="desa" value="<?php echo i18n::t('Desa agrupació'); ?>" />
            <input type="reset" name="reset" value="<?php echo i18n::t('Esborra camps'); ?>" />
        </div>
    </div>
</form>