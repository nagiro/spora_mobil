<?php
    if(isNumericParam('id')) {
        $id = intval($_REQUEST['id']);
    } else {
        $id = 0;
    }

    $productor = new DBTable('formulariproductor', $id);
?>

<script type="text/javascript" language="javascript">
    $(function(){
        $('#nom').focus(function(){
            alert('we');
        });
    });
</script>

<div data-role="page">
    <div data-role="header">
        <h1>Alta de productor</h1>
    </div><!-- /header -->

    <div data-role="content">
        <div data-role="fieldcontain">
            <label for="nom">Nom de l'establiment</label>
            <input type="text" name="nom" id="nom" value="<?php echo $productor->nom; ?>" />
        </div>

        <div data-role="fieldcontain">
            <label for="tipusEstabliment">Tipus</label>
            <select name="tipusEstabliment" id="tipusEstabliment">
                <?php
                    $tipusEstabliment = Sessions::getAuxiliarInfo('TipusEstabliment');

                    if(is_array($tipusEstabliment)) {
                        foreach($tipusEstabliment as $t) {
                            if($t == $productor->tipusEstabliment) {
                                echo '<option value="' . $t . '" selected="selected">' . $t .  '</option>';
                            } else {
                                echo '<option value="' . $t . '">' . $t .  '</option>';
                            }
                        }
                    }

                ?>
            </select>
        </div>

        <div data-role="fieldcontain">
            <label for="telefon">Tel&egrave;fon</label>
            <input type="tel" name="telefon" id="telefon" />
        </div>

        <div data-role="fieldcontain">
            <label for="personaContacte">Persona de contacte</label>
            <input type="text" name="personaContacte" id="personaContacte" />
        </div>

        <div data-role="fieldcontain">
            <label for="horari">Horari de contacte</label>
            <input type="text" name="horari" id="horari" />
        </div>

        <div data-role="fieldcontain">
            <fieldset data-role="controlgroup" data-type="horizontal">
                <legend>Dies de descans setmanal:</legend>

                <input type="checkbox" name="diesDescans" id="dilluns" class="custom" />
                <label for="dilluns">Dl</label>

                <input type="checkbox" name="diesDescans" id="dimarts" class="custom" />
                <label for="dimarts">Dm</label>

                <input type="checkbox" name="diesDescans" id="dimecres" class="custom" />
                <label for="dimecres">Dc</label>

                <input type="checkbox" name="diesDescans" id="dijous" class="custom" />
                <label for="dijous">Dj</label>

                <input type="checkbox" name="diesDescans" id="divendres" class="custom" />
                <label for="divendres">Dv</label>


                <input type="checkbox" name="diesDescans" id="dissabte" class="custom" />
                <label for="dissabte">Ds</label>

                <input type="checkbox" name="diesDescans" id="diumenge" class="custom" />
                <label for="diumenge">Du</label>
            </fieldset>
        </div>

        <div data-role="fieldcontain">
            <label for="materials">Materials</label>

            <div data-role="collapsible-set" id="materials">
                    <?php
                        $materials = Sessions::getAuxiliarInfo('TipusMaterial');
                        $sistemesRecollida = Sessions::getAuxiliarInfo('SistemaRecollida');
                        $capacitats = array(60,90,120,240,360);
                        $sistemesOpcions = '';

                        if(is_array($sistemesRecollida)) {
                            foreach($sistemesRecollida as $s) {
                                $sistemesOpcions.= '<option value="' . $s . '">' . $s . '</option>';
                            }
                        }

                        if(is_array($materials)) {
                            foreach($materials as $material) {
                                echo '<div data-role="collapsible">
                                    <h4>' . $material . '</h4>';

                                echo '
                                <fieldset data-role="controlgroup">
                                    <p>
                                        <label for="sistemaRecollida_">Sistema de recollida</label>
                                        <select id="sistemaRecollida_" name="sistemaRecollida_">' . $sistemesOpcions . '</select>
                                    </p>';

                                foreach($capacitats as $c) {
                                    echo '
                                        <label for="capacitat_' . $c .'">' . $c . ' L</label>
                                        <input type="number" name="capacitat_' . $c .'" id="capacitat_' . $c .'" value="0" />
                                    ';
                                }

                                echo '</fieldset>
                                </div>';
                            }
                        }
                    ?>
            </div>

            <div class="ui-grid-a">
                <div class="ui-block-a"><button type="submit" data-theme="c" data-icon="add">Material</button></div>
                <div class="ui-block-b"><button type="submit" data-theme="c" data-icon="add">Capacitat</button></div>
            </div>
        </div>

        <div data-role="fieldcontain">
            <label for="suggerimentsComentaris">Suggeriments i comentaris</label>
            <textarea name="suggerimentsComentaris" id="suggerimentsComentaris" rows="8" cols="40"></textarea>
        </div>
    </div><!-- /content -->
</div><!-- /Activitat -->