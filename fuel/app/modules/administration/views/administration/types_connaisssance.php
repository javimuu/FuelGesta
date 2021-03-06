<?php echo render($partial_dir.'_titre_types_connaissance.php'); ?>

<fieldset>
    
    <a href="ajouter_type_connaissance" class="btn btn-success pull-right"><i class="icon-white icon-plus"></i> Ajouter un type</a>
        
    <table class="table table-striped table-top">
        <tr>
            <th>Nom</th>
            <th class="actions"></th>

        </tr>
        <?php
        foreach ($types as $type) {
        ?>
        <tr>
            <td><?php echo $type->t_nom; ?></td>
            <td class="text-right">
                <a class="btn btn-mini btn-warning" href="<?php echo Uri::create('administration/modifier_type_connaissance/'.$type->id_type_connaissance); ?>"><i class="icon-edit icon-white"></i></a>
                <a class="btn btn-mini btn-danger" href="<?php echo Uri::create('administration/supprimer_type_connaissance/'.$type->id_type_connaissance); ?>" onclick="return confirm('Etes-vous sûr de vouloir supprimer ce type de statut ?')"><i class="icon-remove-sign icon-white"></i></a>
            </td>
        </tr>    
        <?php
        }
        ?>
    </table>
</fieldset>

<?php echo render($partial_dir.'_back.php', array('url' => Uri::create($view_dir))); ?>