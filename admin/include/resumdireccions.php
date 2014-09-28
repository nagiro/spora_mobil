<?php
    $resum = Sessions::getVar('ResumImportacio');

    if(!is_array($resum)) {
        Sessions::redirect('direccions');
        exit;
    }
?>

<h2><?php echo i18n::t('Resum de la importació'); ?></h2>

<div class="field">
    <div class="fieldLabel"></div>

    <div class="fieldInput">
        <table>
            <thead>
                <tr>
                    <th><?php echo i18n::t('Concepte'); ?></th>
                    <th><?php echo i18n::t('Número'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th><?php echo i18n::t('Barris nous afegits'); ?></th>
                    <td id="newAreaCount"><?php echo intval($resum['newAreaCount']); ?></td>
                </tr>
                <tr>
                    <th><?php echo i18n::t('Carrers nous afegits'); ?></th>
                    <td id="newStreetCount"><?php echo intval($resum['newStreetCount']); ?></td>
                </tr>
                <tr>
                    <th><?php echo i18n::t('Adreces que no s\'han pogut importar'); ?></th>
                    <td id="errorCount"><?php echo intval($resum['errorCount']); ?></td>
                </tr>
                <tr>
                    <th><?php echo i18n::t('Adreces importades amb èxit'); ?></th>
                    <td id="importCount"><?php echo intval($resum['importCount']); ?></td>
                </tr>
                <tr>
                    <th><?php echo i18n::t('Adreces totals'); ?></th>
                    <td id="count"><?php echo intval($resum['count']); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="field">
    <div class="fieldLabel"></div>

    <div class="fieldInput">
        <a href="?page=direccions" class="button" title="<?php echo i18n::t('Enrere'); ?>"><?php echo i18n::t('Torna enrere'); ?></a>
    </div>
</div>