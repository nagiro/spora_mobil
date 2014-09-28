<?php
    if(Sessions::isVar('GrupBarri')) {
        $a = Sessions::getVar('GrupBarri');
    }
?>
<div data-role="page">
    <div data-role="header">
        <h1>Buscar n.</h1>
    </div><!-- /header -->
    <div data-role="content">
        <div data-role="fieldcontain">
            <label for="text">N&uacute;mero</label>
            <input type="hidden" name="carrer" id="carrer" value="<?php echo $_REQUEST['c']; ?>" />
            <input type="hidden" name="barri" id="barri" value="<?php echo $a; ?>" />
            <input type="text" name="text" id="numero" placeholder="N&uacute;mero a buscar" autofocus="autofocus" />
        </div>

        <a href="#" id="cercaNumero" onclick="cercaNumero();return false;" data-role="button">Busca</a>
    </div><!-- /content -->
</div>