<?php
    if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = intval($_REQUEST['id']);
    } else {
        Sessions::redirect('incidencies_particulars');
    }

    $incidencia = Incidencies::obteIncidencia($id);
?>

<div class="field">
    <div class="fieldLabel">
        <label for="usuari"><?php echo i18n::t('Introduïda per'); ?></label>
    </div>

    <div class="fieldInput">
        <label id="usuari"><?php echo (empty($incidencia['nomUsuari'])? $incidencia['username'] : $incidencia['nomUsuari']); ?></label>
    </div>
</div>

<div class="field">
    <div class="fieldLabel">
        <label for="data"><?php echo i18n::t('Data'); ?></label>
    </div>

    <div class="fieldInput">
        <label id="data"><?php echo $incidencia['data']; ?></label>
    </div>
</div>

<div class="field">
    <div class="fieldLabel">
        <label for="text"><?php echo i18n::t('Text'); ?></label>
    </div>

    <div class="fieldInput">
        <label id="text"><?php echo nl2br(stripslashes($incidencia['text'])); ?></label>
    </div>
</div>

<div class="field">
    <div class="fieldLabel"></div>
    
    <div class="fieldInput">
        <a href="?page=incidencies_particulars" class="button"><?php echo i18n::t('Torna enrere'); ?></a>
    </div>
</div>