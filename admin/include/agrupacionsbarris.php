<h2><?php echo i18n::t('Agrupacions de barris'); ?></h2>

<div class="actions">
    <a href="?page=agrupaciobarri" class="button"><?php echo i18n::t('Afegeix una agrupació'); ?></a>
</div>

<table>
    <thead>
        <tr>
            <th><?php echo i18n::t('Agrupació'); ?></th>
            <th><?php echo i18n::t('Municipi'); ?></th>
            <th><?php echo i18n::t('Accions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $agrupacions = Poblacions::llistaAgrupacionsBarris();

            if(is_array($agrupacions)) {
                foreach($agrupacions as $a) {
                    echo '
                    <tr>
                        <td>' . $a['nom'] . '</td>
                        <td>' . $a['nomMunicipi'] . '</td>
                        <td>
                            <a href="?page=agrupaciobarri&amp;id=' . $a['grup'] . '" class="button">' . i18n::t('Edita') . '</a>
                            <a href="?action=deleteAreaGrouping&amp;id=' . $a['grup'] . '" class="button">' . i18n::t('Elimina') . '</a>
                        </td>
                    </tr>';
                }
            }
        ?>
    </tbody>
</table>