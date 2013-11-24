<h2>Insertion du nombres d'heures &agrave; effectuer</h2>


<?php echo Form::open(); ?>

<fieldset>
    <legend>Heures à effectuer par <?php echo $nom ?> pour le mois de <?php echo $date->format('m-Y'); ?></legend>
<div class="control-group">
    <?php echo Form::label('Heures', 'i_heures', array('class' => 'control-label')); ?>
    <div class="controls">
    <?php echo Form::input('i_heures', isset($participant) ? $participant : '', array('rel'=>'popover' , 'title' => 'INFOS - Ce formulaire n\'est utile que dans le cas d\'un changement de régime de travail.')); ?>
    </div>
</div>
</fieldset>
<?php 
echo Form::submit('bouton','Suivant');
echo Form::close();
?>
<?php echo Html::anchor('prestation/modifier_participant/', '<i class="icon-arrow-left"></i> Retour', array('class' => "btn btn-sucess pull-right")); ?>
<div class="clear"></div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#form_i_heures').tooltip('toogle');
    });
</script>