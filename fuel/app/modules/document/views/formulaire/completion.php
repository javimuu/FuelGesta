<?php echo render('partials/_titre_document.php'); ?>

<script type="text/javascript">
    $(function () {
        var projects = [
        <?php foreach ($participants as $participant): ?>
            {
                value:"<?php echo $participant->id_participant; ?>",
                label:"<?php echo $participant->t_nom . ' ' . $participant->t_prenom; ?>"
            },
            <?php endforeach; ?>
        ];

        $('#form_nom').autocomplete({
            minLength:0,
            source:projects,
            select:function (event, ui) {
                $('#form_nom').val(ui.item.label);
                $('#form_idparticipant').val(ui.item.value);
                return false;
            }
        })

    });
</script>

<?php echo Form::open(array('action' => 'document/formulaire/formulaire/' . $formulaire), array('class' => 'form-horizontal')); ?>

<fieldset>
    <legend>Impression du document <?php echo $titre_document; ?></legend>
    
    <div class="control-group">
        <?php echo Form::label('Nom', 'nom', array('class' => 'control-label')); ?>
        <div class="controls">
        <?php echo Form::input('nom', ''); ?>Facultatif
        </div>
        <?php echo Form::hidden('idparticipant', ''); ?>
    </div>
    <?php if ($formulaire == 2): ?>
        Généartion du/des document(s) C98 en fonction d'un <b>seul</b> choix facultatif.
    <div class="control-group">
        <?php echo Form::label('Localisation', 'centre', array('class' => 'control-label')); ?>
        <div class="controls">
        <?php echo Form::select('centre','', array('','Localisation'=>$centre)) ?>Facultatif
        </div>
    </div>
    <div class="control-group">
        <?php echo Form::label('Groupe', 'groupe', array('class' => 'control-label')); ?>
        <div class="controls">
            <?php echo Form::select('groupe', '', array('','Groupe'=>$groupe)) ?>Facultatif
        </div>
    </div>
    <?php endif; ?>

    <div class="control-group">
        <?php echo Form::label('Mois', 'mois', array('class' => 'control-label')); ?>
        <div class="controls">
            <?php echo Form::select('mois',date('M'), $mois) ?>Obligatoire
        </div>
    </div>

    <div class="control-group">
        <?php echo Form::label('Année', 'annee', array('class' => 'control-label')); ?>
        <div class="controls">
        <?php echo Form::select('annee', date('Y'), $annees) ?>Obligatoire
        </div>
    </div>

    
</fieldset>

<div class="form-actions">
    <button type="submit" class="btn btn-success">Suivant</button>

</div>
<?php echo Form::close(); ?>

<?php if(!empty($verif)): ?>
<table class="table table-top table-striped">
    <tr>
        <th colspan="3">Participant(s) dont l'information est incomplète </th>
    </tr>
    <tr>
    <th>Nom </th>
    <th>Registre nationale</th>
    <th>Organisme de payement</th>
    <th>Adresse</th>
    </tr>

    <?php foreach($verif as $value): ?>
        <tr>
        <?php if($value['t_registre_national'] == null || $value['t_organisme_paiement'] == null || $value['t_nom_rue'] == null ) : ?>
            <td><?php echo Html::anchor('participant/modifier/'.$value['id_participant'],$value['t_nom'].' '.$value['t_prenom']) ?></td>
            <td><?php echo ($value['t_registre_national'] == null)?'X':'' ?></td>
            <td><?php echo ($value['t_organisme_paiement'] == null)?'X':'' ?></td>
            <td><?php echo ($value['t_nom_rue'] == null)?'X':'' ?></td>

        <?php endif; ?>
        </tr>
    <?php endforeach; ?>


</table>
<?php endif; ?>



<p><?php echo Html::anchor('document', '<i class="icon-step-backward"></i> Retour', array('class' => 'btn pull-right')); ?></p>
<div class="clear"></div>