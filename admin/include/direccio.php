<?php
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);
    } else {
        Sessions::redirect('direccions');
    }

    $userID = $_SESSION['userID'];
    $carrer = new DBTable('carrers', $id);
?>
<table>
    <thead>
        <th><?php echo $carrer->via . ' ' . $carrer->nom; ?></th>
        <th><?php echo i18n::t('Opcions'); ?></th>
    </thead>
    <tbody>
        <?php
            $llistaOpcions = Poblacions::mostraOpcions($carrer->municipi);
            $opcions = '';

            $formularisDireccio = Poblacions::llistaOpcionsDireccio($id);

            if(is_array($formularisDireccio)) {
                foreach($formularisDireccio as $f) {
                    echo '<tr>
                        <td>' . $f['text'] . '</td>
                        <td>';
		    
                    foreach($llistaOpcions as $l) {
                        $chkboxID = $f['id'] . '_' . $l['id'];

                        $bloquejat = (!empty($f['actuacio']) && $f['educador'] != $userID)? 'disabled="disabled"' : '';
                        $marcat = (!empty($f['actuacio']) && $f['actuacio'] == $l['id'])? 'checked="checked"' : '';

                        echo '
                            <input type="checkbox" id="' .  $chkboxID . '" ' . $bloquejat . ' ' . $marcat . ' />
                            <label for="' .  $chkboxID . '">' . $l['abreviacio'] . '</label> ';
                    }

                    echo '
                        </td>
                    </tr>';
                }
            }
        ?>
    </tbody>
</table>