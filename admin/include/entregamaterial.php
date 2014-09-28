<?php
    $capacitatsContenidor = Sessions::getAuxiliarInfo('CapacitatContenidor');
    $sistemesRecollida = Sessions::getAuxiliarInfo('SistemaRecollida');
    $tipusMaterial = Sessions::getAuxiliarInfo('TipusMaterial');

    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);

        $entregaContenidors = Productors::obteEntreguesContenidors($id);

        foreach($entregaContenidors as $i => $nc) {
            if(!in_array($nc['material'], $tipusMaterial)) {
                $tipusMaterial[] = $nc['material'];
            }

            if(!in_array($nc['capacitat'], $capacitatsContenidor)) {
                $capacitatsContenidor[] = $nc['capacitat'];
            }

            $entregaContenidors[$i]['material'] = array_search($nc['material'], $tipusMaterial);
            $entregaContenidors[$i]['capacitat'] = array_search($nc['capacitat'], $capacitatsContenidor);
        }

        $sistemesRecollidaEntregaTriats = Productors::obteSistemesRecollidaEntregaContenidors($id);

        foreach($sistemesRecollidaEntregaTriats as $i => $sr) {
            $sistemesRecollidaEntregaTriats[$i]['material'] = array_search($sr['material'], $tipusMaterial);
        }
    } else {
        $id = 0;
        $entregaContenidors = array();
        $sistemesRecollidaEntregaTriats = array();
    }

    $formularientregamaterial = new DBTable('formularientregamaterial', $id);
?>

<script type="text/javascript" language="javascript">
    $(function() {
        var capacitatsContenidor = <?php echo json_encode($capacitatsContenidor); ?>;
        var sistemesRecollida = <?php echo json_encode($sistemesRecollida); ?>;
        var tipusMaterial = <?php echo json_encode($tipusMaterial); ?>;

        var entregaContenidors = <?php echo json_encode($entregaContenidors); ?>;
        var sistemesRecollidaEntregaTriats = <?php echo json_encode($sistemesRecollidaEntregaTriats); ?>;

        $.each(entregaContenidors, function(index, entry){
            var material = entry.material, capacitat = entry.capacitat;
            $('#contenidor_'+material+'_'+capacitat).val(entry.demanda);
        });

        $.each(sistemesRecollidaEntregaTriats, function(index, entry){
            var material = entry.material;
            $('#sistemaRecollida_'+material).val(entry.sistema);
        });


        $('#addMaterial').click(function() {
            var nouMaterial = $('#addMaterialText').val();
            var i, index;

            if(!nouMaterial) {
                return false;
            }

            for(var i in tipusMaterial) {
                if(tipusMaterial[i] == nouMaterial) {
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
                content+= '<input type="number" step="1" value="0" /> ' + capacitatsContenidor[i] + '<br />';
            }

            content+= '</div>';

            $('#newTabs').append(content);
            $('#materialTabs').tabs("add", "#tipusMaterial_"+index, nouMaterial);
            $('#addMaterialText').val('');
            return false;
        });

        $('#addCapacity').click(function(){
            var capacitat = parseInt($('#addCapacityText').val());

            if(!capacitat || capacitat < 1) {
                alert('Aquesta capacitat no és vàlida!');
                return;
            }

            capacitat = capacitat + 'L';

            for(var i in capacitatsContenidor) {
                if(capacitatsContenidor[i] == capacitat) {
                    alert('Aquesta capacitat ja hi és');
                    return;
                }
            }

            capacitatsContenidor.push(capacitat);

            $.each(tipusMaterial, function(index, material) {
                $('#tipusMaterial_' + index).append('<input type="number" step="1" value="0" /> ' + capacitat + '<br />');
            });

            $('#addCapacityText').val('');
        });

        $("#materialTabs").tabs({collapsible: true});

        $('#entregaForm').submit(function(){
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

            $('#sistemesRecollidaEntregaTriats').val(sistemesRecollidaTriats.toString());
            $('#materialsTriats').val(necessitatContenidors.toString());

            request('?action=saveEntregaMaterial', {
                id: $('#id').val(),
                productor: $('#productor').val(),
                materialsTriats: $('#materialsTriats').val(),
                sistemesRecollidaTriats: $('#sistemesRecollidaEntregaTriats').val(),
                entregaMaterialGrafic: $('#entregaMaterialGrafic').val(),
                suggerimentsComentaris: $('#suggerimentsComentaris').val()
            });
            return false;
        });
    });

</script>
<h2><?php echo i18n::t('Necessitat de contenidors'); ?></h2>

<form action="#" method="post" id="entregaForm">
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
                            if($p['id'] == $$formularientregamaterial->productor) {
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
                <input type="hidden" name="sistemesRecollidaEntregaTriats" id="sistemesRecollidaEntregaTriats" value="" />
                <ul>
                    <?php
                        $llista = '';
                        $capacitats = '';

                        if(is_array($tipusMaterial)) {
                            foreach($tipusMaterial as $indexMaterial => $tm) {
                                $llista.= '<li><a href="#tipusMaterial_' . $indexMaterial . '">' . $tm . '</a></li>';

                                $capacitats.= '<div id="tipusMaterial_' . $indexMaterial . '">';

                                $capacitats.= '<p>' . i18n::t('Sistema de recollida') . ' <select name="sistemaRecollida_'.$indexMaterial.'" id="sistemaRecollida_'.$indexMaterial.'"><option value="">&nbsp;</option>';

                                if(is_array($sistemesRecollida)) {
                                    foreach($sistemesRecollida as $s) {
                                        $capacitats.= '<option value="' . $s . '">' . $s . '</option>';
                                    }
                                }

                                $capacitats.= '</select></p>';

                                if(is_array($capacitatsContenidor)) {
                                    foreach($capacitatsContenidor as $c) {
                                        $capacitats.= '<input type="number" step="1" value="0" /> ' . $c . '<br />';
                                    }
                                }

                                $capacitats.= '</div>';
                            }
                        }

                        echo $llista;
                    ?>
                </ul>

                <?php echo $capacitats; ?>
                <div id="newTabs" style="display:none;">&nbsp;</div>
            </div>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="entregaMaterialGrafic"><?php echo i18n::t('Entrega de material gràfic'); ?></label>
        </div>

        <div class="fieldInput">
            <textarea cols="40" rows="8" name="entregaMaterialGrafic" id="entregaMaterialGrafic"><?php echo $formularientregamaterial->entregaMaterialGrafic; ?></textarea>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel">
            <label for="suggerimentsComentaris"><?php echo i18n::t('Suggeriments i comentaris'); ?></label>
        </div>

        <div class="fieldInput">
            <textarea cols="40" rows="8" name="suggerimentsComentaris" id="suggerimentsComentaris"><?php echo $formularientregamaterial->suggerimentsComentaris; ?></textarea>
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel"></div>

        <div class="fieldInput">
            <input type="submit" class="button" value="<?php echo i18n::t('Desa'); ?>" />
        </div>
    </div>
</form>