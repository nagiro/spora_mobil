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
                fullesTriades = $('#fulles').val();

            if(!indexMunicipi) {
                return false;
            }
            $("#loading").show();
            $.get(	'?action=parseXLSText',
            		{
                		municipi: indexMunicipi,
                		fulles: fullesTriades
            		},
            		function(data){ alert(data); $("#loading").hide(); }
            );
                    
            return false;
        });
    });
</script>

<h3 id="loading" style="display:none; background-color:#CCCCCC; text-align:center; width:100%; padding:10px; margin-bottom:10px;">Loading...</h3>
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
                    <option value="-1"><?php echo i18n::t('Municipi genèric'); ?></option>
                    
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
