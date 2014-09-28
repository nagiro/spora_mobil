    <div data-role="fieldcontain">
        <fieldset>
            <legend>Necessitat de contenidors</legend>
            <?php
                $tipusMaterial = Sessions::getAuxiliarInfo('TipusMaterial');
                $capacitatsContenidor = Sessions::getAuxiliarInfo('CapacitatContenidor');

                if(is_array($tipusMaterial) && is_array($capacitatsContenidor)) {
                    echo '<div data-role="collapsible-set">';

                    foreach($tipusMaterial as $t) {
                        echo '<div data-role="collapsible"><h4>' . $t . '</h4>';

                        foreach($capacitatsContenidor as $c) {
                            echo '<input type="number" min="0" stepage="1" value="0" name="' . $c . '" /> ' . $c . PHP_EOL;
                        }

                        echo '</div>';
                    }

                    echo '</div>';
                }
            ?>
        </fieldset>
    </div>

    <div data-role="fieldcontain">
        <label for="entregaMaterialGrafic">Entrega de material gràfic</label>
        <textarea cols="40" rows="8" name="entregaMaterialGrafic" id="entregaMaterialGrafic"></textarea>
    </div>

    <div data-role="fieldcontain">
        <label for="suggerimentsComentaris">Suggeriments i comentaris</label>
        <textarea cols="40" rows="8" name="suggerimentsComentaris" id="suggerimentsComentaris"></textarea>
    </div>