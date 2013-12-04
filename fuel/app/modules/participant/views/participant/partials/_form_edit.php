<script>
    $.datepicker.setDefaults({
        dateFormat: 'dd/mm/yy',
        selectOtherMonths: true,
        changeMonth: true,
        changeYear: true,
        dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
        firstDay: 1
    });
    // Fonction datepicker pour la date de naissance
    $(function(){
        $('#form_d_date_naissance').datepicker({
            dateFormat: 'dd/mm/yy',
            yearRange: '-100:+10'
        });
    });
    
    // Fonction datepicker pour la date de naissance
    $(function(){
        $('.dp,#form_d_date_permis_theorique,#form_d_fin_etude,#form_d_date_inscription_onem,#form_d_date_fin_stage_onem,#form_d_date_inscription_forem,#form_d_date_expiration_carte_sejour,#form_d_date_examen_medical').datepicker({
            yearRange: '-100:+10'
        }).attr("readonly","readonly");
    });
    

    

    // Fonction tabs
    $(function() {
        $( "#tabs" ).tabs();
    });
    
    // Dialog "nouvelle adresse"
    $(function() {
        $("#dialog-adresse").dialog({
            autoOpen: false,
            height: 500,
            width: 400,
            modal: true
        });

        $('#create-adresse')
//        .button()
        .click(function() {
            $('#dialog-adresse').dialog('open');
            return false;
        });
        $("#dialog-contact").dialog({
            autoOpen: false,
            height: 500,
            width: 450,
            modal: true
        });

        $('#create-contact')
//        .button()
        .click(function() {
            $('#dialog-contact').dialog('open');
            return false;
        });



    });
    
    // Fonction maskd input pour les dates
//    jQuery(function($){
//       $("#form_t_registre_national").mask("999999-999.99");
//       $("#form_t_compte_bancaire").mask("999-9999999-99");
//    });

</script>
<legend>
    <?php
    $nom = $participant->t_nom." ".$participant->t_prenom;
    $id = $participant->id_participant;
    $annee = date('Y');
    $mois = date('m');

    ?>

    <?php echo Form::open('prestation/') ?>


    Modifier le participant <?php echo $participant->t_nom." ".$participant->t_prenom; ?> -

    <?php echo Form::hidden('idparticipant',$id) ?>
    <?php echo Form::hidden('nom',$nom) ?>
    <?php echo Form::hidden('annee',$annee) ?>
    <?php echo Form::hidden('mois',$mois) ?>
    <?php echo Form::button('Prestation',Null,array('class'=> 'btn btn-link')); ?>
    <?php echo Form::close(); ?>



</legend>

<?php echo Form::open(array('class' => 'form-horizontal')); ?>
    <fieldset>


        <ul id="gestaTab" class="nav nav-pills">
            <li class="active"><a href="#signaletique" data-toggle="tab" onclick="fill_hidden_input('signaletique')">Signalétique</a></li>
            <li><a href="#adresse" data-toggle="tab" onclick="fill_hidden_input('adresse')">Adresse</a></li>
            <li><a href="#situation" data-toggle="tab" onclick="fill_hidden_input('situation')">Situation</a></li>
            <li><a href="#contact" data-toggle="tab" onclick="fill_hidden_input('contact')">Personne contact</a></li>
            <li><a href="#diplome" data-toggle="tab" onclick="fill_hidden_input('diplome')">Diplôme</a></li>
        <!--    <li><a href="#fin_formation" data-toggle="tab" onclick="fill_hidden_input('fin_formation')">Fin de Formation</a></li>-->


            <li><a href="#employabilite" data-toggle="tab" onclick="fill_hidden_input('employabilite')">Employabilité</a></li>

            <li><a href="#checklist" data-toggle="tab" onclick="fill_hidden_input('checklist')">Checklist</a></li>

            <li><?php echo Html::anchor('contrat/ajouter/' . $participant->id_participant, 'Contrat') ?></li>
        </ul>

        <div id="gestaTabContent" class="tab-content">
            <div class="tab-pane fade in active" id="signaletique">
                <?php echo render('participant/partials/_form_edit_signaletique'); ?>
            </div>
            <div class="tab-pane fade" id="adresse">
                <?php echo render('participant/partials/_form_list_adresse'); ?>
            </div>
            <div class="tab-pane fade" id="situation">
                <?php echo render('participant/partials/_form_edit_situation'); ?>
            </div>
            <div class="tab-pane fade" id="contact">
                <?php echo render('participant/partials/_form_list_contact'); ?>
            </div>
            <div class="tab-pane fade" id="diplome">
                <?php echo render('participant/partials/_form_edit_diplome'); ?>
            </div>
        <!--    </div> <div class="tab-pane fade" id="fin_formation">-->
        <!--        --><?php //echo render('participant/_form_edit_fin_formation'); ?>
        <!--    </div>-->
            <div class="tab-pane fade" id="employabilite"></div>


            <div class="tab-pane fade" id="checklist">
                <?php echo render('participant/partials/_form_checklist'); ?>
            </div>
        </div>
    </fieldset>

    <input type="hidden" name="tab" value="" />
    <div class="form-actions">
        <button type="submit" class="btn btn-success">Modifier le participant</button>
    </div>

<?php echo Form::close(); ?>



<div id="dialog-adresse">
    <?php echo Form::open('participant/ajouter_adresse/' . $participant->id_participant, array('class' => 'form-horizontal')); ?>
    <div class="control-group">
        <?php echo Form::label('Rue', 't_nom_rue', array('class' => 'control-label')); ?>
        <div class="controls">
            <?php echo Form::input('t_nom_rue', Input::post('t_nom_rue', isset($adresse) ? $adresse->t_nom_rue : '')); ?>
        </div>
    </div>
    <div class="control-group">
        <?php echo Form::label('Bte', 't_bte', array('class' => 'control-label')); ?>
        <div class="controls">
            <?php echo Form::input('t_bte', Input::post('t_bte', isset($adresse) ? $adresse->t_bte : '')); ?>
        </div>
    </div>
    <div class="control-group">
        <?php echo Form::label('CP', 't_code_postal', array('class' => 'control-label')); ?>
        <div class="controls">
            <?php echo Form::input('t_code_postal', Input::post('t_code_postal', isset($adresse) ? $adresse->t_code_postal : '')); ?>
        </div>
    </div>
    <div class="control-group">
        <?php echo Form::label('Commune', 't_commune', array('class' => 'control-label')); ?>
        <div class="controls">
            <?php echo Form::input('t_commune', Input::post('t_commune', isset($adresse) ? $adresse->t_commune : '')); ?>
        </div>
    </div>
    <div class="control-group">
        <?php echo Form::label('Téléphone', 't_telephone', array('class' => 'control-label')); ?>
        <div class="controls">
            <?php echo Form::input('t_telephone', Input::post('t_telephone', isset($adresse) ? $adresse->t_telephone : '')); ?>
        </div>
    </div>
    <div class="control-group">
        <?php echo Form::label('Type', 't_type', array('class' => 'control-label')); ?>
        <div class="controls">
            <?php echo Form::input('t_type', Input::post('t_type', isset($adresse) ? $adresse->t_type : '')); ?>
        </div>
    </div>
    <?php if (empty($alreadyDefault)): ?>
        <div class="control-group">
            <?php echo Form::label('Défaut : Adresse domicile officiel.', 't_courrier', array('class' => 'control-label')); ?>
            <div class="controls">
                <?php echo Form::checkbox('t_courrier', '1', array('checked' => 'checked')); ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="form-actions">
        <button type="submit" class="btn btn-success">Créer l'adresse</button>
    </div>
    <?php echo Form::close(); ?>
</div>

<div id="dialog-contact">
    <?php echo render('participant/partials/_form_ajouter_contact'); ?>
</div>

<script type="text/javascript">

function fill_hidden_input($value)
{
    $('input[name="tab"]').val($value);
}


</script>