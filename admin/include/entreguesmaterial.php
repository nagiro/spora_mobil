<h2><?php echo i18n::t('Entregues de material'); ?></h2>

<p><a href="?page=entregamaterial" class="button"><?php echo i18n::t('Afegeix productor'); ?></a></p>

<table>
    <thead>
        <tr>
            <th><?php echo i18n::t('Nom de l\'establiment'); ?></th>
            <th><?php echo i18n::t('Data d\'alta'); ?></th>
            <th><?php echo i18n::t('Municipi'); ?></th>
            <th><?php echo i18n::t('Accions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $productors = Productors::llistaEntreguesMaterial();

            if(is_array($productors)) {
                foreach($productors as $p) {
                    echo '<tr>
                        <td>' . $p['nom'] . '</td>
                        <td>' . $p['alta'] . '</td>
                        <td>' . $p['municipi'] . '</td>
                        <td>
                            <a href="?page=entregamaterial&amp;id=' . $p['id'] . '" class="button">' . i18n::t('Edita') . '</a>
                            <a href="?action=deleteEntregaMaterial&amp;id=' . $p['id'] . '" class="button">' . i18n::t('Elimina') . '</a>
                        </td>
                    </tr>';
                }
            }
        ?>
    </tbody>
</table>