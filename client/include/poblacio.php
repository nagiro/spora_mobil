<div data-role="page">
    <div data-role="header" data-nobackbtn="true">
        <h1>Poblacions</h1>
    </div><!-- /header -->

    <div data-role="content">
        <ul data-role="listview" data-filter="true">
            <?php                
                if(count($_SESSION['municipi'])) {
                    $poblacio = array();
                       
                    foreach($_SESSION['municipi'] AS $m) {
                        $poblacio[] = Poblacions::obteMunicipi($m);
                    }
                } else {
                    $poblacio = Poblacions::llistaPoblacions();                    
                }

                if(is_array($poblacio)) {
                    foreach($poblacio as $p) {
                        echo '<li data-role="list-divider">' . $p['nom'] . '</li>';

                        $barris = Poblacions::llistaAgrupacionsBarris($p['id']);

                        $grup = '';

                        foreach($barris as $b) {
                            if($grup == $b['grup']){
                                echo ", " . $b['nom'];
                            } else {
                                if($grup!='') {
                                    echo '</a></li>';
                                }
                                
                                $grup = $b['grup'];
                                echo '<li><a href="?page=carrer&m=' . $b['grup'] . '">' . $b['nom'];
                            }
                        }

                        echo '</a></li>';
                    }
                }
            ?>
        </ul>
    </div><!-- /content -->
</div><!-- /Poblacions -->