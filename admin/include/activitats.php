<h2><?php echo i18n::t('Activitats'); ?></h2>

<div class="actions">
    <a href="?page=activitat" class="button"><?php echo i18n::t('Nova activitat'); ?></a>
</div>

<table>
    <thead>
        <th><?php echo i18n::t('Títol'); ?></th>
        <th><?php echo i18n::t('Model de formulari'); ?></th>
        <th><?php echo i18n::t('Accions'); ?></th>
    </thead>
    <tbody>
        <?php
            $activitats = new DBTable('activitats');
            $activitats = $activitats->readAll();

            foreach($activitats as $a) {
                echo '
                <tr>
                    <td>' . $a['titol'] . '</td>
                    <td>' . Formularis::obteNomFormulari($a['formulari']) . '</td>
                    <td>
                        <a href="?page=activitat&amp;id=' . $a['id'] . '" class="button">' . i18n::t('Edita') . '</a>
                        <a href="?action=deleteActivitat&amp;id=' . $a['id'] . '" class="button">' . i18n::t('Elimina') . '</a>
                    </td>
                </tr>';
            }
        ?>
    </tbody>
</table>