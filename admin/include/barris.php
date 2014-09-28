<h2><?php echo i18n::t('Barris'); ?></h2>

<div class="actions">
    <a href="?page=barri" class="button"><?php echo i18n::t('Afegeix un barri'); ?></a>
</div>

<table>
    <thead>
        <tr>
            <th><?php echo i18n::t('Nom'); ?></th>
            <th><?php echo i18n::t('Municipi'); ?></th>
            <th><?php echo i18n::t('Accions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $direccions = Poblacions::llistaBarris();

            if(is_array($direccions)) {
                foreach($direccions as $d) {
                    echo '<tr>
                        <td>' . $d['nom'] . '</td>
                        <td>' . $d['nomMunicipi'] . '</td>
                        <td>
                            <a href="?page=barri&amp;id=' . $d['id'] . '" class="button">' . i18n::t('Edita') . '</a>
                            <a href="?action=deleteArea&amp;id=' . $d['id'] . '" class="button">' . i18n::t('Elimina') . '</a>
                        </td>
                    </tr>';
                }
            }
        ?>
    </tbody>
</table>