<h2><?php echo i18n::t('Alta de productors'); ?></h2>

<p><a href="?page=altaproductor" class="button"><?php echo i18n::t('Afegeix productor'); ?></a></p>

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
            $productors = Productors::llistaProductors();

            if(is_array($productors)) {
                foreach($productors as $p) {
                    echo '<tr>
                        <td>' . $p['nom'] . '</td>
                        <td>' . $p['alta'] . '</td>
                        <td>' . $p['municipi'] . '</td>
                        <td>
                            <a href="?page=altaproductor&amp;id=' . $p['id'] . '" class="button">' . i18n::t('Edita') . '</a>
                            <a href="?action=deleteProducer&amp;id=' . $p['id'] . '" class="button">' . i18n::t('Elimina') . '</a>
                        </td>
                    </tr>';
                }
            }
        ?>
    </tbody>
</table>