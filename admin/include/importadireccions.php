<script type="text/javascript" language="javascript">
    $(function(){
        if($("#drop-div").length > 0) {
            $("#drop-div").dropzone({
                url : "?action=upload"
            });
        }

        $.fn.dropzone.newFilesDropped = function() {
            $('#drop-div').html('Carregant el fitxer...');
            $('#drop-div').css('background', 'none');
        };

        $.fn.dropzone.uploadFinished = function() {
            $('#drop-div').html('Llegint el fitxer...');

            $.getJSON('?action=getXLSCols', function(data) {
                $('#drop-div').html('Fitxer llegit');

                var optionsStr = '', index;
                for(var i in data) {
                    index = parseInt(i) + 1;
                    optionsStr+= '<option value="' + index + '">' + data[i] + '</option>';
                }

                $('#barri').html(optionsStr);
                $('#text').html(optionsStr);
                
                $('#via').html(optionsStr);
                $('#nom').html(optionsStr);

                $('#numero').html(optionsStr);
                $('#bis').html(optionsStr);
                $('#bloc').html(optionsStr);
                $('#escala').html(optionsStr);
                $('#repla').html(optionsStr);
                $('#porta').html(optionsStr);
            });

            $.getJSON('?action=getXLSSheets', function(data) {
                var numSheets = parseInt(data);

                $('#fulles').val();
                
                for(var i = 1; i <= numSheets; i++) {
                    $('#fulles').append('<option value="' + i + '">Fulla ' + i + '</option>');
                }
            });
        };

        //Selector de municipis
        $.getJSON('?action=get&table=municipis', {actiu: 1}, function(data){
            $.each(data, function(index, entry){
                $('<option></option>')
                    .attr('value', entry.id)
                    .html(entry.nom)
                    .appendTo('#municipi');
            });
        });

        $('#formImporta').submit(function(){
            var indexMunicipi = $('#municipi').val(),
                indexBarri = $('#barri').val(),
                indexText = $('#text').val(),
                fullesTriades = $('#fulles').val();

            if(!indexMunicipi || !indexBarri) {
                return false;
            }

            $.ajax({
                url: '?action=parseXLSText',
                type: 'POST',
                dataType: 'json',
                data: {
                    municipi: indexMunicipi,
                    barri: indexBarri,
                    text: indexText,
                    fulles: fullesTriades
                }
            });

            //window.location = '?page=resumdireccions';
            return false;
        });
    });
</script>

<h2><?php echo i18n::t('Entrada de dades'); ?></h2>

<div id="fileuploader">    
    <form action="?action=upload" method="post" enctype="multipart/form-data">
        <div class="field">
            <div class="fieldLabel">
                <label for="drop-div"><?php echo i18n::t('Fitxer XLS'); ?></label>
            </div>
            
            <div class="fieldInput">
                <div id="drop-div">
                    <span id="drop-div-text"><?php echo i18n::t('Arrossega\'l aquí'); ?></span>
                </div>
            </div>
        </div>
    </form>

    <form id="formImporta">
        <div class="field">
            <div class="fieldLabel">
                <label for="municipi"><?php echo i18n::t('Municipi'); ?></label>
            </div>

            <div class="fieldInput">
                <select name="municipi" id="municipi">
                    <option><?php echo i18n::t('Tria una columna'); ?></option>
                </select>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="barri"><?php echo i18n::t('Barri'); ?></label>
            </div>

            <div class="fieldInput">
                <select name="barri" id="barri">
                    <option><?php echo i18n::t('Tria una columna'); ?></option>
                </select>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="text"><?php echo i18n::t('Camp genèric'); ?></label>
            </div>

            <div class="fieldInput">
                <select name="text" id="text">
                    <option><?php echo i18n::t('Tria una columna'); ?></option>
                </select>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="fulles"><?php echo i18n::t('Fulles'); ?></label>
            </div>

            <div class="fieldInput">
                <select name="fulles" id="fulles" multiple="multiple" size="5">
                    <option><?php echo i18n::t('Llegeix totes les fulles'); ?></option>
                </select>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel"></div>

            <div class="fieldInput">
                <input type="submit" name="send" value="<?php echo i18n::t('Importa'); ?>" />
            </div>
        </div>
    </form>
</div>
