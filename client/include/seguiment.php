    <div data-role="fieldcontain">
        <fieldset data-role="controlgroup">
            <legend>Recollida selectiva:</legend>
            <input type="checkbox" name="participaRecollidaSelectiva" id="participaRecollidaSelectiva" class="custom" />
            <label for="participaRecollidaSelectiva">Participa en la recollida</label>
        </fieldset>
    </div>

    <div data-role="fieldcontain">
        <label for="grauSatisfaccio">Satisfacció amb el servei</label>
        <input type="range" name="grauSatisfaccio" id="grauSatisfaccio" value="0" min="0" max="10"  />
    </div>

    <div data-role="collapsible-set">
        <div data-role="collapsible">
            <h4><label for="queixes">Queixes</label></h4>
            <textarea cols="40" rows="8" name="queixes" id="queixes"></textarea>
        </div>

        <div data-role="collapsible">
            <h4><label for="dubtes">Dubtes</label></h4>
            <textarea cols="40" rows="8" name="dubtes" id="dubtes"></textarea>
        </div>

        <div data-role="collapsible">
            <h4><label for="comentaris">Comentaris</label></h4>
            <textarea cols="40" rows="8" name="comentaris" id="comentaris"></textarea>
        </div>

        <div data-role="collapsible">
            <h4><label for="suggeriments">Suggeriments</label></h4>
            <textarea cols="40" rows="8" name="suggeriments" id="suggeriments"></textarea>
        </div>
    </div>