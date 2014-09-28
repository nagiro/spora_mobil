<h2><?php echo i18n::t('Incidències de particulars'); ?></h2>

<table>
    <thead>
        <th><?php echo i18n::t('Número'); ?></th>
        <th><?php echo i18n::t('Afegida per'); ?></th>
        <th><?php echo i18n::t('Data'); ?></th>
        <th><?php echo i18n::t('Accions'); ?></th>
    </thead>
    <tbody>
        <?php
            $incidencies = Incidencies::llistaIncidenciesParticulars();

            if(is_array($incidencies)) {
                foreach($incidencies as $i) {
                    echo '<tr>
                        <td>' . $i['id'] . '</td>
                        <td>' . (empty($i['usuari'])? $i['username'] : $i['usuari']) . '</td>
                        <td>' . $i['data'] . '</td>
                        <td>
                            <a href="?page=incidencia&amp;id=' . $i['id'] . '" class="button">' . i18n::t('Visualitza') . '</a>
                            <a href="?action=deleteIncidence&amp;id=' . $i['id'] . '" class="button">' . i18n::t('Elimina') . '</a>
                        </td>
                    </tr>';
                }
            }
        ?>
    </tbody>
</table>