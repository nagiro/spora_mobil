<h2><?php echo i18n::t('Direccions'); ?></h2>

<div class="actions">
    <a href="?page=barris" class="button changePage"><?php echo i18n::t('Barris'); ?></a>
    <a href="?page=agrupacionsbarris" class="button changePage"><?php echo i18n::t('Agrupacions de barris'); ?></a>
    <a href="?page=carrer" class="button changePage"><?php echo i18n::t('Afegeix un carrer'); ?></a>
    <a href="?page=afegeixdireccio" class="button changePage"><?php echo i18n::t('Afegeix un número'); ?></a>
</div>

<table>
    <thead>
        <tr>
            <th><?php echo i18n::t('Via'); ?></th>
            <th><?php echo i18n::t('Nom'); ?></th>
            <th><?php echo i18n::t('Barri'); ?></th>
            <th><?php echo i18n::t('Municipi'); ?></th>
            <th><?php echo i18n::t('Accions'); ?></th>
        </tr>
    </thead>
    <tbody id="llistaCarrers">
        <?php
            $carrers = Poblacions::llistaCarrers();

            if(is_array($carrers)) {
                foreach($carrers as $c) {
                    echo '<tr>
                        <td>' . $c['via'] . '</td>
                        <td>' . $c['nom'] . '</td>
                        <td>' . $c['nomBarri'] . '</td>
                        <td>' . $c['nomMunicipi'] . '</td>
                        <td>
                            <a href="?page=direccio&amp;id=' . $c['id'] . '" class="button changePage">' . i18n::t('Formularis') . '</a>
                            <a href="?page=carrer&amp;id=' . $c['id'] . '" class="button changePage">' . i18n::t('Edita') . '</a>
                            <a href="?action=deleteDirection&amp;id=' . $c['id'] . '" class="button">' . i18n::t('Elimina') . '</a>
                        </td>
                    </tr>';
                }
            }
        ?>
    </tbody>
</table>