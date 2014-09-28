<?php
    if(!isNumericParam('c')) {
        exit;
    }

    if(Sessions::isVar('GrupBarri')) {
        $a = Sessions::getVar('GrupBarri');
    }

    $id = intval($_REQUEST['c']);
    $start = intval($_REQUEST['start']);
    $nomCarrer = Poblacions::obteNomCarrer($id);
?>
<div data-role="page">
    <div data-role="header" data-nobackbtn="true">
        <a href="?page=numero&amp;c=<?php echo $id; ?>&amp;m=<?php echo $a; ?>&amp;start=<?php echo $start; ?>" id="urlback" data-icon="arrow-l" data-theme="b" data-iconpos="notext">Volver</a>
        <h1 id="carrer" data-id="<?php echo $id; ?>"><?php echo $nomCarrer; ?></h1>
        <a href="#?page=incidencia" id="novaIncidencia" data-icon="alert" data-theme="b" data-rel="dialog" data-transition="pop" data-iconpos="notext">Incidencia</a>
    </div><!-- /header -->

    <div data-role="content">
        <div data-role="fieldcontain">
            <label for="numero">Numero</label>
            <input type="text" name="numero" id="numero" />
        </div>
        <div data-role="fieldcontain">
            <label for="planta">Planta</label>
            <input type="text" name="planta" id="planta" />
        </div>
        <div data-role="fieldcontain">
            <label for="porta">Puerta</label>
            <input type="text" name="porta" id="porta" />
        </div>
        <input type="hidden" name="barri" id="barri" value="<?php echo $a; ?>" />
        <div data-role="fieldcontain">
            <a href="#" id="desaDireccio" name="desaDireccio" data-role="button" onclick="desaDireccio();return false;">Guarda direcci&oacute;n</a>
        </div>
    </div><!-- /content -->
</div><!-- /Addnumero -->