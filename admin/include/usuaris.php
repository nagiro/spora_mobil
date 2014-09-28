<h2><?php echo i18n::t('Usuaris'); ?></h2>

<div class="actions">
    <a href="?page=usuari" class="button"><?php echo i18n::t('Afegeix un usuari'); ?></a>
</div>

<table>
    <thead>
        <th><?php echo i18n::t('Usuari'); ?></th>
        <th><?php echo i18n::t('Nom complet'); ?></th>
        <th><?php echo i18n::t('Tipus d\'usuari'); ?></th>
        <th><?php echo i18n::t('Data de creació'); ?></th>
        <th><?php echo i18n::t('Municipis'); ?></th>
        <th><?php echo i18n::t('Accions'); ?></th>
    </thead>
    <tbody>
        <?php
            $usuaris = new DBTable('usuaris');
            $llista = $usuaris->readAll();

            if(is_array($llista)) {
                $accions = '
                    <a href="?page=usuari&amp;id=%d" class="button">'. i18n::t('Edita') . '</a>
                    <a href="?action=deleteUser&amp;id=%d" class="button">' . i18n::t('Elimina') . '</a>';

                foreach($llista as $u) {
                    $municipis = Users::obteNomsMunicipisUsuari($u['id']);
                    
                    if(count($municipis)) {
                        $strMunicipis = array();
                        
                        foreach($municipis as $m) {
                            $strMunicipis[]= $m['nom'];
                        }
                        
                        $strMunicipis = join(', ', $strMunicipis);
                    } else {
                        $strMunicipis = '';
                    }
                    
                    echo '<tr>
                        <td>' . $u['username'] . '</td>
                        <td>' . $u['nom'] . '</td>
                        <td>' . $u['profile'] . '</td>
                        <td>' . $u['creacio'] . '</td>
                        <td>' . $strMunicipis . '</td>
                        <td>';

                    if($u['id'] == $_SESSION['userID'] || !in_array($u['profile'], array(Users::PERFIL_ADMINISTRADOR, Users::PERFIL_MANTENIDOR))) {
                        echo sprintf($accions, $u['id'], $u['id']);
                    }

                    echo '
                        </td>
                    </tr>';
                }
            }
        ?>
    </tbody>
</table>