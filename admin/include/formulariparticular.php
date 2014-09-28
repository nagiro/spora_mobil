<h2><?php echo i18n::t('Direccions'); ?></h2>

<div class="actions">
    <a href="?page=carrer" class="button"><?php echo i18n::t('Afegeix un carrer'); ?></a>
    <a href="?page=direccio" class="button"><?php echo i18n::t('Afegeix una direcció'); ?></a>
</div>

<table>
    <thead>
        <th><?php echo i18n::t('Via'); ?></th>
        <th><?php echo i18n::t('Nom'); ?></th>
        <th><?php echo i18n::t('Municipi'); ?></th>
        <th><?php echo i18n::t('Accions'); ?></th>
    </thead>
    <tbody>
        <?php
            $direccions = Poblacions::llistaCarrers();

            if(is_array($direccions)) {
                foreach($direccions as $d) {
                    echo '<tr>
                        <td>' . $d['via'] . '</td>
                        <td>' . $d['nom'] . '</td>
                        <td>' . $d['nomMunicipi'] . '</td>
                        <td>
                            <a href="?page=direccio&amp;id=' . $d['id'] . '" class="button">' . i18n::t('Formularis') . '</a>
                            <a href="?page=carrer&amp;id=' . $d['id'] . '" class="button">' . i18n::t('Edita') . '</a>
                            <a href="?action=deleteDirection&amp;id=' . $d['id'] . '" class="button">' . i18n::t('Elimina') . '</a>
                        </td>
                    </tr>';
                }
            }
        ?>
    </tbody>
</table>