<h2>Listes des contrats de type : <?php echo $type_contrat->t_type_contrat ?></h2>


<fieldset>

    <table class="table table-striped table-top" id="table_activite">
        <tr>

            <th>Nom</th>
            <th>Prénom</th>
            <th>Date début Contrat</th>
            <th>Date Fin contrat prévu</th>
            <th>Nom du groupe</th>

        </tr>

        <?php
        foreach ($contrat as $contrats):

            ?>
            <tr>

                <td><a class="btn btn-link"
                       href="<?php echo Uri::create('contrat/ajouter/' . $contrats['id_participant']); ?>"><?php echo $contrats['t_nom']; ?></a>
                </td>
                <td><?php echo $contrats['t_prenom']; ?></td>
                <td class="denomination"><?php echo \Maitrepylos\Date::db_to_date($contrats['d_date_debut_contrat']) ?></td>
                <td class="denomination"><?php echo \Maitrepylos\Date::db_to_date($contrats['d_date_fin_contrat_prevu']) ?></td>
                <td><?php echo $contrats['groupe_nom'] ?></td>

            </tr>
        <?php
        endforeach;
        ?>
    </table>

</fieldset>

<?php echo render($partial_dir . '_back.php', array('url' => Uri::create($view_dir . 'administration/liste_types_contrat'))); ?>