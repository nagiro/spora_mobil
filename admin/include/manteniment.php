<script type="text/javascript">
    $(function(){
        $('#esborraCarrer').click(function(){
            var idMunicipi = parseInt( $('#municipi').val() );

            if(!idMunicipi) {
                return false;
            }

            $.ajax({
                url: '?action=esborraMunicipi',
                data: {
                    municipi: idMunicipi
                },
                method: 'post'
            });

            return false;
        });
    });
</script>
<h1><?php echo i18n::t('Manteniment del sistema'); ?></h1>

<form action="?action=backupServer" method="post">
    <fieldset>
        <legend><?php echo i18n::t('Còpies de seguretat'); ?></legend>

        <div class="field">
            <div class="fieldLabel">
                <label><?php echo i18n::t('Fes una còpia'); ?></label>
            </div>

            <div class="fieldInput">
                <a href="?action=backupDireccions" class="button"><?php echo i18n::t('Direccions'); ?></a>
                <a href="?action=backupActuacions" class="button"><?php echo i18n::t('Actuacions'); ?></a>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label><?php echo i18n::t('Restaura una còpia'); ?></label>
            </div>

            <div class="fieldInput">
                <a href="?action=restauraDireccions" class="button"><?php echo i18n::t('Direccions'); ?></a>
                <a href="?action=restauraActuacions" class="button"><?php echo i18n::t('Actuacions'); ?></a>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="dbs"><?php echo i18n::t('Copia altres bases de dades'); ?></label>
            </div>

            <div class="fieldInput">
                <select name="dbs[]" id="dbs" multiple="multiple">
                    <?php
                        $llista = Actualitzacions::llistaBDs();

                        if(is_array($llista)) {
                            foreach($llista as $l) {
                                if($l != SQL_DBNAME) {
                                    echo '<option value="'. $l . '">' . $l . '</option>';
                                }
                            }
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel"></div>

            <div class="fieldInput">
                <input type="submit" value="<?php echo i18n::t('Fes còpia de seguretat'); ?>" />
            </div>
        </div>
    </fieldset>


    <div class="field">
        <div class="fieldLabel"><?php echo i18n::t('Versió de la BD'); ?></div>

        <div class="fieldInput">
            <?php echo i18n::t('Versió instal·lada'); ?>: <?php echo VERSIO_BD; ?>
            <br/>
            <?php echo i18n::t('Versió necessària'); ?>: <?php $version = Sessions::getAuxiliarInfo('VersioBD'); echo $version[0]; ?>
            <br />
        </div>
    </div>

    <div class="field">
        <div class="fieldLabel"><?php echo i18n::t('Esborra carrers d\'un municipi'); ?></div>

        <div class="fieldInput">
            <select name="municipi" id="municipi">
                <option value=""><?php echo i18n::t('Tria un municipi'); ?></option>
                <?php
                    $municipis = Poblacions::llistaMunicipis();

                    foreach($municipis as $municipi) {
                        echo '<option value="'.$municipi['id'].'">' . $municipi['nom'] . '</option>';
                    }
                ?>
            </select>
            <a id="esborraCarrer" href="?action=esborraMunicipi" class="button"><?php echo i18n::t('Esborra'); ?></a>
        </div>
    </div>
</form>