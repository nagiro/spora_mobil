<h2><?php echo i18n::t('Comentaris d\'educadors'); ?></h2>

<table>
    <thead>
        <th><?php echo i18n::t('Municipi'); ?></th>
        <th><?php echo i18n::t('Adreça'); ?></th>
        <th><?php echo i18n::t('Educador'); ?></th>
        <th><?php echo i18n::t('Data'); ?></th>        
        <th><?php echo i18n::t('Text'); ?></th>        
    </thead>
    <tbody>
        <?php
            $comentaris = Poblacions::llistaComentaris();
			
            if(is_array($comentaris)) {
                foreach($comentaris as $c) {
                    echo '<tr>
                        <td>' . $c['nom_municipi'] . '</td>
                        <td>' . $c['dir_adreca'] . '</td>
                        <td>' . $c['nom_educador'] . '</td>            			
            			<td>' . $c['data'] . '</td>
            			<td>' . $c['comentari'] . '</td>                        
                    </tr>';
                }
            }
        ?>
    </tbody>
</table>