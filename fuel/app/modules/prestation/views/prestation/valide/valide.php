<h2>Situation des heures de <b><?php echo $nom ?> (<?php echo $date->format('m') . '-' . $date->format('Y') ?>)
</h2></b>


<table class="tablesorter table table-striped">
    <thead>
    <tr>
        <th>Motif</th>
        <th>Heures prest&eacute;es</th>
        <th>Pourcentage</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($tableau AS $valeur) {
        echo "<tr>";
        echo "<td>";
        echo $valeur['t_motif'];
        echo "</td>";
        echo "<td>";
        echo $valeur['i_secondes'];
        echo "</td>";
        echo "<td>";
        echo round($valeur['pourcentage']) . ' %';
        echo "</td>";
        echo "</tr>";
    }

    ?>
    <?php ?>
    <tr>
        <td>Total</td>
        <td><?php echo $total_heures_prester . '  Hors absences' ?> </td>
        <td><?php echo round($total_pourcentage) . ' % Absences comprises' ?></td>
    </tr>
    </tbody>
</table>




<?php
$message = '';
if ($situation === 1):?>

    <?php
    if($recup){

        $resume['resultat'] = 'Il existe des heures de récupération ( '.$recup.') qui n\'aurait pas du être prise';
    }else{
        $resume['resultat'] =  'Il reste encore à introduire :' . $resume['resultat'];
    }
    ?>

    <p>R&eacute;sum&eacute; du mois : <?php echo 'Prest&eacute; : ' . $total_heures_prester . ' - A Prester '
        . $resume['heure_a_prester'] .', '.$resume['resultat'] ?>
    <?php echo render('prestation/valide/_incorecte'); ?>



<?php elseif ($situation === 2): ?>
    <p>R&eacute;sum&eacute; du mois : <?php echo 'Prest&eacute; : ' . $total_heures_prester . ' - A Prester '
            . $resume['heure_a_prester'] . ' = ' . $resume['resultat'] ?></p>
    <?php echo render('prestation/valide/_incoherente') ?>


<?php elseif ($situation === 3): ?>

    <?php
    $messageRecup = null;
    if (((int)$totalHeuresPresterMoinsRecup) > ((int)$resume['heure_a_prester'])) {
        $message = 'heures supplémentaires : ';
    }

    if($recup){
        $messageRecup = '(moins les heures de récup '.$recup.')';
    }
    ?>

    <p>R&eacute;sum&eacute; du mois : <?php echo 'Prest&eacute; : ' . $totalHeurePresterNoRecup .$messageRecup. '  - A Prester '
            . $resume['heure_a_prester'] . ' = ' . $message . $resume['resultat'] ?></p>
    <?php echo render('prestation/valide/_corecte') ?>
<?php endif; ?>




