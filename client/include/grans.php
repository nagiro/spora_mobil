<?php
//    if(isNumericParam('id')) {
//        $id = intval($_REQUEST['id']);
//    } else {
//        Sessions::redirect('menu');
//    }
?>
<div data-role="page">
    <div data-role="header">
        <a href="?page=menu" data-icon="back">Menu</a>

        <h1>Grans productors</h1>

        <a href="?page=productor" data-icon="add" data-theme="b">Alta</a>
    </div><!-- /header -->

    <div data-role="content">
        <ul data-role="listview" data-filter="true" role="listbox">
        <?php
            $productors = Productors::llistaProductors();

            if(is_array($productors)) {

                $letter = '';

                foreach($productors as $p) {

                    $inicial = $p['nom'];
                    $inicial = $inicial{0};

                    if($inicial != $letter) {
                        $letter = $inicial;
                        echo '<li data-role="list-divider">' . $letter . '</li>';
                    }

                    echo '<li><a href="?page=productor&m=' . $p['id'] . '">' . $p['nom'] . '</a></li>';
                }
            }
        ?>
        </ul>
    </div><!-- /content -->
</div><!-- /Grans Productors -->