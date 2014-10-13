<?php 
$mode = ""; $id = "";

if(isset($_REQUEST['mode'])) $mode = $_REQUEST['mode'];
if(isset($_REQUEST['id'])) $id = $_REQUEST['id'];

if($mode == "EDITA"):

if(!empty($id)) $poblacio = Poblacions::obteMunicipi($id);
else $poblacio = Poblacions::obteMunicipi(null);

?>
	<script type="text/javascript" language="javascript">
	    $(function() {
	    	$('#desaPoblacio').submit(function(){		    	
	            request('?action=savePoblacio', {
	                id: $('#id').val(),
	                nom: $('#nom').val(),
	                actiu: $('#actiu').val()	            
	            });
	            return false;
	        });
	    });
	
	</script>
	<h2><?php echo i18n::t('Alta de població'); ?></h2>
	
	<form action="#" method="post" id="desaPoblacio">
	    <div class="ui-widget" id="error" style="display: none;">
	        <div class="fieldLabel"></div>
	
	        <div class="fieldInput ui-state-error ui-corner-all" style="margin-left: 10px;">
	            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
	            <strong>Error:</strong> <span id="errorText"></span></p>
	        </div>
	    </div>
	    
	    <input type="hidden" name="id" id="id" value="<?php echo $poblacio['id']; ?>" />
	
	    <div class="field">
	        <div class="fieldLabel">
	            <label for="nom"><?php echo i18n::t('Nom de la població'); ?></label>
	        </div>
	
	        <div class="fieldInput">
	            <input type="text" name="nom" id="nom" value="<?php echo $poblacio['nom']; ?>"  />
	        </div>
	    </div>
	
	    <div class="field">
	        <div class="fieldLabel">
	            <label for="municipi"><?php echo i18n::t('Actiu'); ?></label>
	        </div>
	
	        <div class="fieldInput">
	            <select name="actiu" id="actiu">
	            <?php if($poblacio['actiu'] == 1): ?>
	            	<option value="1" selected>Sí</option>
	            	<option value="0">No</option>
	            <?php else: ?>
	            	<option value="1">Sí</option>
	            	<option value="0" selected>No</option>
	            <?php endif; ?>
	            </select>
	        </div>
	    </div>
	
	    <div class="field">
	        <div class="fieldLabel"></div>
	
	        <div class="fieldInput">
	            <input type="submit" class="button" value="<?php echo i18n::t('Desa'); ?>" />
	        </div>
	    </div>
	</form>

<?php  else: ?>
	
	<h2><?php echo i18n::t('Llistat de municipis'); ?></h2>

	<p><a href="?page=poblacions&amp;mode=EDITA" class="button"><?php echo i18n::t('Afegeix un municipi'); ?></a></p>
	
	<table>
	    <thead>
	        <tr>
	            <th><?php echo i18n::t('ID Municipi'); ?></th>
	            <th><?php echo i18n::t('Nom'); ?></th>
	            <th><?php echo i18n::t('Actiu?'); ?></th>
	            <th><?php echo i18n::t('Accions'); ?></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php
	            $municipis = Poblacions::llistaMunicipis(false);
	
	            if(is_array($municipis)) {
	                foreach($municipis as $p) {
	                    echo '<tr>
	                        <td>' . $p['id'] . '</td>
	                        <td>' . $p['nom'] . '</td>
	                        <td>' . $p['actiu'] . '</td>
	                        <td>
	                            <a href="?page=poblacions&amp;mode=EDITA&amp;id=' . $p['id'] . '" class="button">' . i18n::t('Edita') . '</a>
	                            <a href="?action=deletePoblacions&amp;id=' . $p['id'] . '" class="button">' . i18n::t('Elimina') . '</a>
	                        </td>
	                    </tr>';
	                }
	            }
	        ?>
	    </tbody>
	</table>
	
<?php endif; ?>	