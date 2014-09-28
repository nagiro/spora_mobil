<?php
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);
    } else {
        $id = 0;
    }
    
    $contacte = new DBTable('contactes', $id);
?>

<form action="?action=saveContact" method="POST">
        <?php
            if($id !== 0) {
                echo '<input type="hidden" name="id" value="' . $id . '" />';
            }
        ?>

        <div class="field">
            <div class="fieldLabel">
                <label for="tipusContacte">Tipus de contacte</label>
            </div>

            <div class="fieldInput">
                <select name="tipusContacte" id="tipusContacte">
                    <?php
                        $tipusContactes = $contacte->getEnumDomain('tipusContacte');

                        foreach($tipusContactes as $tc) {
                            if($tc == $contacte->tipusContacte) {
                                echo '<option value="' . $tc . '" selected="selected">' . $tc . '</option>';
                            } else {
                                echo '<option value="' . $tc . '">' . $tc . '</option>';
                            }
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="nom">Nom</label>
            </div>

            <div class="fieldInput">
                <input type="text" name="nom" id="nom" value="<?php echo $contacte->nom; ?>" />
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="tipusAdresa">Tipus d'adreça</label>
            </div>

            <div class="fieldInput">
                <select name="tipusAdresa" id="tipusAdresa">
                    <?php
                        $tipusAdreces = $contacte->getEnumDomain('tipusAdresa');

                        foreach($tipusAdreces as $ta) {
                            if($tc == $contacte->tipusAdresa) {
                                echo '<option value="' . $ta . '" selected="selected">' . $ta . '</option>';
                            } else {
                                echo '<option value="' . $ta . '">' . $ta . '</option>';
                            }
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="adresa">Nom adreça</label>
            </div>

            <div class="fieldInput">
                <input type="text" name="adresa" id="adresa" value="<?php echo $contacte->adresa; ?>" />
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="numero">Número</label>
            </div>

            <div class="fieldInput">
                <input type="text" name="numero" id="numero" class="numeric" value="<?php echo $contacte->numero; ?>" />
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="pis">Pis</label>
            </div>

            <div class="fieldInput">
                <input type="text" name="pis" id="pis" class="numeric" value="<?php echo $contacte->pis; ?>" />
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="porta">Porta</label>
            </div>

            <div class="fieldInput">
                <input type="text" name="porta" id="porta" class="numeric" value="<?php echo $contacte->porta; ?>" />
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="desa">&nbsp;</label>
            </div>
            
            <div class="fieldInput">
                <input type="submit" name="desa" value="Desa contacte" />
                <input type="reset" name="reset" value="Esborra camps" />
            </div>
        </div>
</form>