<script type="text/javascript" language="javascript">
    $(function() {

		$("#b_exporta").click(function(){
			$("#loading").show();
			$.get('?action=exportXLS', 
					{
						municipi:   $('#municipi_select').val(),
		                barri:      $('#barri').val(),
		                carrer:     $('#carrer').val(),
		                educador:   $('#educador').val(),
		                tipusAccio: $('#tipusAccio').val(),
		                inici:      $('#dataInici').val(),
		                fi:         $('#dataFi').val()
					}, function(retData){				  
				  var binUrl = JSON.parse(retData);
				  var iframe = document.createElement("iframe");
				  	iframe.setAttribute("src", binUrl);
				  	iframe.setAttribute("style", "display: none");
				  	document.body.appendChild(iframe);
				  //document.body.innerHTML += "<iframe src='" + binUrl + "' style='display: none;' ></iframe>"
				  $("#loading").hide();
				}); 			
		});
        
        $('#dataInici').datepicker({
            changeYear: true,
            dateFormat: 'dd/mm/yy'
        });
        $('#dataFi').datepicker({
            changeYear: true,
            dateFormat: 'dd/mm/yy'
        });
        $.datepicker.setDefaults($.datepicker.regional['ca']);
        $('#dataInici').datepicker('setDate', '-1d');
        $('#dataFi').datepicker('setDate', '+0d');

        $('#municipi_select').change(function() {
        	$("#loading").show();
            $.getJSON('?action=get&table=carrers', {
                municipi: $(this).val(),
                sort: 'nom'
            }, function(data) {
            	$("#loading").hide();
                $('#carrer').html('<option value="">- Tots -</option>');

                if(data) {
                    $.each(data, function(i,carrer){
                        $('#carrer').append('<option value="' + carrer.id + '">' + carrer.via + ' ' + carrer.nom + '</option>');
                    });
                }
            });

            $.getJSON('?action=obteGrupsBarris', {
                municipi: $(this).val()
            },
            function(data) {
                $('#barri').html('<option value="">- Tots -</option>');

                if(data) {
                    $.each(data, function(i,barri){
                        $('#barri').append('<option value="' + barri.grup + '">' + barri.nom + '</option>');
                    });
                }
            });
        });

        $('#barri').change(function(){
            $.getJSON('?action=obteCarrersBarrisAgrupats', {
                municipi: $('#municipi_select').val(),
                barri: $(this).val()
            },
            function(data) {
                $('#carrer').html('<option value="">- Tots -</option>');

                if(data) {
                    $.each(data, function(i,carrer){
                        $('#carrer').append('<option value="' + carrer.id + '">' + carrer.via + ' ' + carrer.nom + '</option>');
                    });
                }
            });
        });

        $('#filter').submit(function() {
            $('#llista').html('');
            $("#loading").show();
            $.getJSON('?action=stats_particular', {
                municipi:   $('#municipi_select').val(),
                barri:      $('#barri').val(),
                carrer:     $('#carrer').val(),
                educador:   $('#educador').val(),
                tipusAccio: $('#tipusAccio').val(),
                inici:      $('#dataInici').val(),
                fi:         $('#dataFi').val()
            }, function(data) {
                var total = 0;
                $("#loading").hide();
                
                $.each(data, function(ciutat, barris) {
                    if(typeof(barris) != 'object') {
                        return true;
                    }

                    $('#llista').append('\n\
                        <tr class="stat1">\n\
                            <td>' + ciutat +  '</td>\n\
                            <td>' + barris[0] + '</td>\n\
                            <td>' + barris[1] + '</td>\n\
                            <td>&nbsp;</td>\n\
                        </tr>');

                    $.each(barris, function(barri, carrers) {
                        if(typeof(carrers) != 'object') {
                            return true;
                        }

                        total = parseFloat(carrers[0] / carrers[1]) * 100;
                        total = total.toFixed(2);
                        
                        if(isNaN(total)) {
                            total = 0;
                        }

                        $('#llista').append('\n\
                           <tr class="stat2">\n\
                                <td>' + barri +  '</td>\n\
                                <td>' + carrers[0] + '</td>\n\
                                <td>' + carrers[1] + '</td>\n\
                                <td>' + total + '%</td>\n\
                            </tr>');

                        $.each(carrers, function(carrer, educadors) {
                            if(typeof(educadors) != 'object') {
                                return true;
                            }

                            total = parseFloat(educadors[0] / educadors[1]) * 100;
                            total = total.toFixed(2);

                            if(isNaN(total)) {
                                total = 0;
                            }

                            $('#llista').append('\n\
                                <tr class="stat3">\n\
                                    <td>' + carrer +  '</td>\n\
                                    <td>' + educadors[0] + '</td>\n\
                                    <td>' + educadors[1] + '</td>\n\
                                    <td>' + total + '%</td>\n\
                                </tr>');
                        });
                    });
                });

//                 total = parseFloat(data[0] / data[1]) * 100;
//                 total = total.toFixed(2);
// 
//                 if(isNaN(total)) {
//                     total = 0;
//                 }
// 
//                 $('#llista').append('\n\
//                     <tr class="stattotal">\n\
//                         <td>TOTAL</td>\n\
//                         <td>' + data[0] + '</td>\n\
//                         <td>' + data[1] + '</td>\n\
//                         <td>' + total + '%</td>\n\
//                     </tr>');
            });

            return false;
        });
    });
</script>

<h3 id="loading" style="display:none; background-color:#CCCCCC; text-align:center; width:100%; padding:10px; margin-bottom:10px;">Loading...</h3>
<h2><?php echo i18n::t('Estadístiques'); ?></h2>
<form method="post" action="" id="filter">
    <fieldset>
        <div class="field">
            <div class="fieldLabel"></div>
            
            <div class="fieldInput"><?php echo i18n::t('Filtra per'); ?></div>

        <div class="field">
            <div class="fieldLabel">
                <label for="municipi_select"><?php echo i18n::t('Municipi'); ?></label>
            </div>

            <div class="fieldInput">
                <?php
                
                    if(count($_SESSION['municipi'])) {
                        $municipis = array();
                        
                        foreach($_SESSION['municipi'] as $m) {
                            $municipis[] = Poblacions::obteMunicipi($m);
                        }
                    } else {
                        $municipis = Poblacions::llistaPoblacions();
                    }
                
                    echo '<select name="municipi_select" id="municipi_select" style="width: 200px;">
                        <option value="">- ' . i18n::t('Tots') . ' -</option>';

                    foreach($municipis as $m) {
                        echo '<option value="' . $m['id'] . '">' . $m['nom'] . '</option>';
                    }
                    
                    echo '</select>';
                ?>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="barri"><?php echo i18n::t('Barris'); ?></label>
            </div>

            <div class="fieldInput">
                <select name="barri" id="barri" style="width: 200px;">
                    <?php
                    if(empty($_SESSION['municipi'])) {
                        echo '<option value="">- ' . i18n::t('Tria un municipi') . ' -</option>';
                    } else {
                        echo '<option value="">- ' . i18n::t('Tots') . ' -</option>';

                        $grups = Poblacions::llistaAgrupacionsBarris($_SESSION['municipi']);

                        foreach($grups as $g) {
                            echo '<option value="' . $g['grup'] . '">' . $g['nom'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="carrer"><?php echo i18n::t('Carrer'); ?></label>
            </div>

            <div class="fieldInput">
                <select name="carrer" id="carrer" style="width: 200px;">
                    <option value="">- <?php echo i18n::t('Tria un municipi'); ?> -</option>
                </select>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="educador"><?php echo i18n::t('Educador/a'); ?></label>
            </div>

            <div class="fieldInput">
                <select name="educador" id="educador" style="width: 200px;">
                    <option value="">- <?php echo i18n::t('Tots'); ?> -</option>
                    <?php
                        $users = Users::llistaUsuaris();

                        foreach ($users as $u) {
                            $nom = empty($u['nom'])? $u['username'] : $u['nom'];

                            echo '<option value="' . $u['id'] . '">' . $nom . '</option>';
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="tipusAccio"><?php echo i18n::t('Acció'); ?></label>
            </div>

            <div class="fieldInput">
                <select name="tipusAccio" id="tipusAccio" style="width: 200px;">
                    <?php
                        $tipus = Poblacions::mostraOpcions();

                        if(is_array($tipus)) {
                            echo '<option value="Informats" selected="selected">' . i18n::t('Informats') . '</option>';

                            foreach($tipus as $t) {
                                echo '<option value="' . $t['id'] . '">' . $t['nom'] . '</option>';
                            }

                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel">
                <label for="dates"><?php echo i18n::t('Dates'); ?></label>
            </div>

            <div class="fieldInput">
                <label id="dates"><?php echo i18n::t('Del'); ?>: </label>
                <input id="dataInici" type="text" class="data" />
                <label id="datesFi"> <?php echo i18n::t('al'); ?>: </label>
                <input id="dataFi" type="text" class="data" />
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel"></div>

            <div class="fieldInput">
                <input name="send" value="<?php echo i18n::t('Filtra'); ?>" type="submit" class="button" />
                <input id="b_exporta" name="send" value="<?php echo i18n::t('Exporta'); ?>" type="button" class="button" />
            </div>
        </div>

        <div class="field">
            <div class="fieldLabel"></div>

            <div class="fieldInput">
                <div id="resultats">
                    <table id="taularesultats" class="tablesorter">
                        <thead>
                            <tr>
                                <th><?php echo i18n::t('Carrer'); ?></th>
                                <th><?php echo i18n::t('Acció'); ?></th>
                                <th><?php echo i18n::t('Llars totals'); ?></th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody id="llista">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </fieldset>
</form>