<?php
    $capacitatsContenidor = Sessions::getAuxiliarInfo('CapacitatContenidor');
    $sistemesRecollida = Sessions::getAuxiliarInfo('SistemaRecollida');
    $tipusMaterial = Sessions::getAuxiliarInfo('TipusMaterial');

    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);

        $necessitatContenidors = Productors::obteNecessitatContenidors($id);

        foreach($necessitatContenidors as $i => $nc) {
            if(!in_array($nc['material'], $tipusMaterial)) {
                $tipusMaterial[] = $nc['material'];
            }

            if(!in_array($nc['capacitat'], $capacitatsContenidor)) {
                $capacitatsContenidor[] = $nc['capacitat'];
            }

            $necessitatContenidors[$i]['material'] = array_search($nc['material'], $tipusMaterial);
            $necessitatContenidors[$i]['capacitat'] = array_search($nc['capacitat'], $capacitatsContenidor);
        }

        $sistemesRecollidaTriats = Productors::obteSistemesRecollidaProductor($id);

        foreach($sistemesRecollidaTriats as $i => $sr) {
            $sistemesRecollidaTriats[$i]['material'] = array_search($sr['material'], $tipusMaterial);
        }
    } else {
        $id = 0;
        $necessitatContenidors = array();
        $sistemesRecollidaTriats = array();
    }

    $productor = new DBTable('productors', $id);
?>

<script type="text/javascript" language="javascript">
    $(function() {
        var capacitatsContenidor = <?php echo json_encode($capacitatsContenidor); ?>;
        var sistemesRecollida = <?php echo json_encode($sistemesRecollida); ?>;
        var tipusMaterial = <?php echo json_encode($tipusMaterial); ?>;

        var necessitatContenidors = <?php echo json_encode($necessitatContenidors); ?>;
        var sistemesRecollidaTriats = <?php echo json_encode($sistemesRecollidaTriats); ?>;

        $.each(necessitatContenidors, function(index, entry){
            var material = entry.material, capacitat = entry.capacitat;
            $('#contenidor_'+material+'_'+capacitat).val(entry.demanda);
        });

        $.each(sistemesRecollidaTriats, function(index, entry){
            var material = entry.material;
            $('#sistemaRecollida_'+material).val(entry.sistema);
        });

        $('#addMaterial').click(function() {
            var nouMaterial = $('#addMaterialText').val();
            var i, index;

            if(!nouMaterial) {
                formError('Has d\'escriure el nom d\'un material vàlid');
                return false;
            }

            for(var i in tipusMaterial) {
                if(tipusMaterial[i] == nouMaterial) {
                    formError('Aquest material ja hi és');
                    return false;
                }
            }

            index = parseInt(i)+1;
            tipusMaterial.push(nouMaterial);

            var content = '<div id="tipusMaterial_'+index+ '"><p>Sistema de recollida <select name="sistemaRecollida_'+index+'" id="sistemaRecollida_'+index+'"><option value="">&nbsp;</option>';

            for(i in sistemesRecollida) {
                content+= '<option value="' + sistemesRecollida[i] + '">' + sistemesRecollida[i] + '</option>';
            }

            content+= '</select></p>';

            for(i in capacitatsContenidor) {
                content+= '<input type="number" step="1" value="0" name="contenidor_'+index+'_'+i+'" id="contenidor_'+index+'_'+i+'" /> ' + capacitatsContenidor[i] + '<br />';
            }

            content+= '</div>';
            
            $('#newTabs').append(content);
            $('#materialTabs').tabs("add", "#tipusMaterial_"+index, nouMaterial);
            $('#addMaterialText').val('');
            return false;
        });

        $('#addCapacity').click(function(){
            var capacitat = parseInt($('#addCapacityText').val()), index;

            if(!capacitat || capacitat < 1) {
                formError('Aquesta capacitat no és vàlida!');
                return;
            }

            capacitat = capacitat + 'L';

            for(var i in capacitatsContenidor) {
                if(capacitatsContenidor[i] == capacitat) {
                    formError('Aquesta capacitat ja hi és');
                    return;
                }
            }

            index = parseInt(i)+1;
            capacitatsContenidor.push(capacitat);

            $.each(tipusMaterial, function(indexMaterial, material) {
                $('#tipusMaterial_' + indexMaterial).append('<input type="number" step="1" value="0" name="contenidor_'+indexMaterial+'_'+index+'" id="contenidor_'+indexMaterial+'_'+index+'" /> ' + capacitat + '<br />');
            });

            $('#addCapacityText').val('');
        });

        $("#materialTabs").tabs({collapsible: false});

        $('#desaProductor').submit(function(){
            var necessitatContenidors = new Array(), sistemesRecollidaTriats = new Array(), entry;
            var itemID = new String();

            $.each(tipusMaterial, function(indexMaterial, nomMaterial){
                itemID = '#sistemaRecollida_' + indexMaterial;

                if($(itemID).val() != '') {
                    entry = [nomMaterial, $(itemID).val()];
                    sistemesRecollidaTriats.push(entry);
                }

                $.each(capacitatsContenidor, function(indexCapacitat, nomCapacitat){
                    itemID = '#contenidor_'+indexMaterial+'_'+indexCapacitat;
                    if($(itemID).val() > 0) {
                        entry = [
                            nomMaterial,
                            nomCapacitat,
                            $(itemID).val()
                        ];

                        necessitatContenidors.push(entry);
                    }
                });
            });

            $('#sistemesRecollidaTriats').val(sistemesRecollidaTriats.toString());
            $('#materialsTriats').val(necessitatContenidors.toString());

            request('?action=saveProductor', {
                id: $('#id').val(),
                nom: $('#nom').val(),
                municipi: $('#municipi').val(),
                establiment: $('#establiment').val(),
                telefon: $('#telefon').val(),
                contacte: $('#contacte').val(),
                horaInici: $('#horaInici').val(),
                minutInici: $('#minutInici').val(),
                horaFi: $('#horaFi').val(),
                minutFi: $('#minutFi').val(),
                dilluns: $('#dilluns').is(':checked'),
                dimarts: $('#dimarts').is(':checked'),
                dimecres: $('#dimecres').is(':checked'),
                dijous: $('#dijous').is(':checked'),
                divendres: $('#divendres').is(':checked'),
                dissabte: $('#dissabte').is(':checked'),
                diumenge: $('#diumenge').is(':checked'),
                materialsTriats: $('#materialsTriats').val(),
                sistemesRecollidaTriats: $('#sistemesRecollidaTriats').val(),
                suggerimentsComentaris: $('#suggerimentsComentaris').val()
            });
            return false;
        });
    });

</script>
<h2><?php echo i18n::t('Alta de productor'); ?></h2>

<form action="#" method="post" id="desaProductor">
    <div class="ui-widget" id="error" style="display: none;">
        <div class="fieldLabel"></div>

        <div class="fieldInput ui-state-error ui-corner-all" style="margin-left: 10px;">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
            <strong>Error:</strong> <span id="errorText"></span></p>
        </div>
    </div>
    
    <input type="hidden" name="id" id="id" value="<?php echo $productor->id; ?>" />

    <div class="field">
        <div class="fieldLabel">
            <label for="nom"><?php echo i18n::t('Nom de l\'establiment'); ?></label>
        </div>

        <div class="fieldInput">
            <input type="text" name="nom" id="nom" value="<?php echo $productor->nom; ?>"  />
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="municipi"><?php echo i18n::t('Municipi'); ?></label>
        </div>

        <div class="fieldInput">
            <select name="municipi" id="municipi">
                <?php
                    echo '<option value="">- ' . i18n::t('Tria un municipi') . ' -</option>';

                    $municipis = Poblacions::llistaMunicipis();
                    
                    foreach($municipis as $municipi) {
                        if($municipi['id'] == $productor->municipi) {
                            echo '<option value="'.$municipi['id'].'" selected="selected">' . $municipi['nom'] . '</option>';
                        } else {
                            echo '<option value="'.$municipi['id'].'">' . $municipi['nom'] . '</option>';
                        }
                    }
                ?>
            </select>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="establiment" class="select"><?php echo i18n::t('Tipus d\'establiment'); ?></label>
        </div>

        <div class="fieldInput">
            <select name="establiment" id="establiment">
                <?php
                    $tipusEstabliment = Sessions::getAuxiliarInfo('TipusEstabliment');
                    
                    foreach($tipusEstabliment as $t) {
                        if($t == $productor->tipusEstabliment) {
                            echo '<option value="'.$t.'" selected="selected">' . $t . '</option>';
                        } else {
                            echo '<option value="'.$t.'">' . $t . '</option>';
                        }
                    }
                ?>
            </select>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="telefon"><?php echo i18n::t('Telèfon'); ?></label>
        </div>

        <div class="fieldInput">
            <input type="tel" name="telefon" id="telefon" value="<?php echo $productor->telefon; ?>"  />
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="contacte"><?php echo i18n::t('Persona de contacte'); ?></label>
        </div>

        <div class="fieldInput">
            <input type="text" name="contacte" id="contacte" value="<?php echo $productor->personaContacte; ?>"  />
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="horari"><?php echo i18n::t('Horari de contacte'); ?></label>
        </div>

        <div class="fieldInput">
            <span class="horaInici"><input type="text" name="horaInici" id="horaInici" value="" style="max-width: 50px;" />:<input type="text" name="minutInici" id="minutInici" value="" style="max-width: 50px;" /></span> -
            <span class="horaFi"><input type="text" name="horaFi" id="horaFi" value="" style="max-width: 50px;" />:<input type="text" name="minutFi" id="minutFi" value="" style="max-width: 50px;" /></span>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label><?php echo i18n::t('Dies de descans setmanal'); ?></label>
        </div>

        <div class="fieldInput">
            <input type="hidden" name="dies" id="dies" value="" />
            <?php
                $dies = array(
                    'dilluns' => i18n::t('Dilluns'),
                    'dimarts' => i18n::t('Dimarts'),
                    'dimecres'=> i18n::t('Dimecres'),
                    'dijous'  => i18n::t('Dijous'),
                    'divendres'=>i18n::t('Divendres'),
                    'dissabte'=> i18n::t('Dissabte'),
                    'diumenge'=> i18n::t('Diumenge')
                );

                foreach($dies as $codiDia => $nomDia) {
                    if(strpos($productor->diesDescans, $codiDia) !== false) {
                        echo '
                        <input type="checkbox" name="'.$codiDia.'" id="'.$codiDia.'" class="custom" checked="checked" />
                        <label for="'.$codiDia.'">'.$nomDia.'</label><br />
                        ';
                    } else {
                        echo '
                        <input type="checkbox" name="'.$codiDia.'" id="'.$codiDia.'" class="custom" />
                        <label for="'.$codiDia.'">'.$nomDia.'</label><br />
                        ';
                    }
                }
            ?>
        </div>
    </div>

    <div class="field" id="materials">
        <div class="fieldLabel">
            <label for="materialTabls"><?php echo i18n::t('Materials'); ?></label>
        </div>

        <div class="fieldInput">
            <p>
                <input type="text" id="addCapacityText" placeholder="<?php echo i18n::t('Capacitat'); ?>" />
                <input type="button" id="addCapacity" class="button" href="#" value="+ <?php echo i18n::t('Capacitat'); ?>" style="width: 110px" /><br />

                <input type="text" id="addMaterialText" placeholder="<?php echo i18n::t('Material'); ?>" />
                <input type="button" id="addMaterial" class="button" href="#" value="+ <?php echo i18n::t('Material'); ?>" style="width: 110px" /><br />
            </p>

            <div id="materialTabs">
                <input type="hidden" name="materialsTriats" id="materialsTriats" value="" />
                <input type="hidden" name="sistemesRecollidaTriats" id="sistemesRecollidaTriats" value="" />
                <ul>
                    <?php
                        $llista = '';
                        $capacitats = '';

                        if(is_array($tipusMaterial)) {
                            foreach($tipusMaterial as $indexMaterial => $tm) {
                                $llista.= '<li><a href="#tipusMaterial_' . $indexMaterial . '">' . $tm . '</a></li>';

                                $capacitats.= '<div id="tipusMaterial_' . $indexMaterial . '">';

                                $capacitats.= '<p>' . i18n::t('Sistema de recollida') . ' <select name="sistemaRecollida_'.$indexMaterial.'" id="sistemaRecollida_'.$indexMaterial.'"><option value="">&nbsp;</option>';

                                foreach($sistemesRecollida as $s) {
                                    $capacitats.= '<option value="' . $s . '">' . $s . '</option>';
                                }

                                $capacitats.= '</select></p>';

                                if(is_array($capacitatsContenidor)) {
                                    foreach($capacitatsContenidor as $indexCapacitat => $c) {
                                        $capacitats.= '<input type="number" step="1" value="0" name="contenidor_'.$indexMaterial.'_'.$indexCapacitat.'" id="contenidor_'.$indexMaterial.'_'.$indexCapacitat.'" /> ' . $c . '<br />';
                                    }
                                }

                                $capacitats.= '</div>';
                            }
                        }

                        echo $llista;
                    ?>
                </ul>

                <?php echo $capacitats; ?>
                <div id="newTabs" style="display: none;"></div>
            </div>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="suggerimentsComentaris"><?php echo i18n::t('Suggeriments i comentaris'); ?></label>
        </div>

        <div class="fieldInput">
            <textarea cols="40" rows="8" name="suggerimentsComentaris" id="suggerimentsComentaris"><?php echo $productor->suggerimentsComentaris; ?></textarea>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel"></div>

        <div class="fieldInput">
            <input type="submit" class="button" value="<?php echo i18n::t('Desa'); ?>" />
        </div>
    </div>
</form>