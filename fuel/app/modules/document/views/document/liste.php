<fieldset  id="catalogue">
        <legend><a href="<?php echo Uri::create('document/document/liste_excel/'.$groupe->id_groupe) ?>"><button class="btn btn-small "><i class="icon-print"></i></button></a>    <?php //echo render('/back', array('url' => Uri::create('liste_diplomes_xml'))); ?></legend>
    <table class="table table-hover table-striped" border="1">
        <thead>
        <tr>
            <th colspan="12" class="label" style="text-align: center"><?php echo $groupe->t_nom ?></th>
        </tr>
        <tr>
            <th>#</th>
            <th>NOM</th>
            <th>PRENOM</th>
            <th>ADRESSE</th>
            <th>TELEPHONE</th>
            <th>GSM</th>
            <th>AGE</th>
            <th>N&deg; NATIONAL</th>
            <th>CHOMAGE</th>
            <th>CONTRAT</th>
            <th>DATE ENTREE</th>
            <th>DATE SORTIE PREVUE</th>
        </tr>
        </thead>
        <tbody>
        <?php  $num = 0; ?>

        <?php foreach ($liste as $item): ?>
            <?php

            $chomage = null;
//            if($item->adr_bte == "") {
//                $bte = null;
//            }else {
//                $bte = " bte : ".$item->adr_bte.' ';
//            }

            if($item['t_situation_sociale'] == 'B10') {
                $chomage = 'X';
            }
            ?>
        <tr>
            <td><?php echo ++$num  ?></td>
            <td><?php echo $item['t_nom'] ?></td>
            <td><?php echo $item['t_prenom'] ?></td>
            <td><?php echo $item['adresse'] ?></td>
            <td><?php echo $item['t_telephone'] ?></td>
            <td><?php echo $item['t_gsm'] ?></td>
            <td><?php echo $item['age'] ?></td>
            <td><?php echo $item['t_registre_national'] ?></td>
            <td><?php echo $chomage ?></td>
            <td><?php echo $item['t_type_contrat'] ?></td>
            <td><?php echo Maitrepylos\Date::db_to_date($item['d_date_debut_contrat'])  ?></td>
            <td><?php echo Maitrepylos\Date::db_to_date($item['d_date_fin_contrat_prevu'])  ?></td>

        </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</fieldset>
<p><?php echo Html::anchor('/document/formulaire/liste', '<i class="icon-step-backward"></i> Retour', array('class' => 'btn pull-right')); ?></p>
<div class="clear"></div>