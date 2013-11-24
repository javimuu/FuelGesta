<table class="form">
    <tr>
        <td>
            <table class="form_left">
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Mutuelle', 't_mutuelle', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('t_mutuelle', Input::post('t_mutuelle', isset($participant) ? $participant->t_mutuelle : '')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Org. de paiement', 't_organisme_paiement', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::select('t_organisme_paiement', Input::post('t_organisme_paiement', isset($participant) ? $participant->t_organisme_paiement : ''), array('' => '', 'CAPAC' => 'CAPAC', 'FGTB' => 'FGTB', 'CSC' => 'CSC', 'CGLSB' => 'CGLSB')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Téléphone de l\'organisme', 't_organisme_paiement_phone', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('t_organisme_paiement_phone', Input::post('t_organisme_paiement_phone', isset($participant) ? $participant->t_organisme_paiement_phone : '')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Enfants à charge', 't_enfants_charge', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::select('t_enfants_charge', Input::post('t_enfants_charge', isset($participant) ? $participant->t_enfants_charge : ''), array('' => '', 'Oui' => 'Oui', 'Non' => 'Non')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="children">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Date de naissance</th>
                                    <th>Age</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $children = explode(";", $participant->t_children) ?>
                                <?php if (count($children) > 1): ?>
                                    <?php foreach (array_chunk($children, 3) as $child): ?>
                                        <tr>
                                            <?php foreach ($child as $value): ?>
                                                <?php if (count(explode("-", $value)) == 3): ?>
                                                    <td><input type="text" value="<?php echo $value; ?>"
                                                               name="t_children[]"
                                                               class="dp"/></td>
                                                <?php else: ?>
                                                    <td><input type="text" value="<?php echo $value; ?>"
                                                               name="t_children[]"/></td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <td class="child_age"></td>
                                            <td><a class="btn btn-mini btn-danger remove_child" href="#"><i
                                                        class="icon-remove-sign icon-white"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                            <button class="btn btn-success pull-right" id="add_child"><i
                                    class="icon-user icon-white"></i> Ajouter
                                un enfant
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Permis de conduire', 't_permis', array('class' => 'control-label')); ?>
                            <div class="controls">
                                A <?php
                                if (in_array('A', $participant->t_permis)) {
                                    echo Form::checkbox('t_permis[]', 'A', array('checked' => 'checked'));
                                } else {
                                    echo Form::checkbox('t_permis[]', 'A');
                                }
                                ?>
                                B <?php
                                if (in_array('B', $participant->t_permis)) {
                                    echo Form::checkbox('t_permis[]', 'B', array('checked' => 'checked'));
                                } else {
                                    echo Form::checkbox('t_permis[]', 'B');
                                }
                                ?>
                                C <?php
                                if (in_array('C', $participant->t_permis)) {
                                    echo Form::checkbox('t_permis[]', 'C', array('checked' => 'checked'));
                                } else {
                                    echo Form::checkbox('t_permis[]', 'C');
                                }
                                ?>
                                D <?php
                                if (in_array('D', $participant->t_permis)) {
                                    echo Form::checkbox('t_permis[]', 'D', array('checked' => 'checked'));
                                } else {
                                    echo Form::checkbox('t_permis[]', 'D');
                                }
                                ?>
                                E <?php
                                if (in_array('E', $participant->t_permis)) {
                                    echo Form::checkbox('t_permis[]', 'E', array('checked' => 'checked'));
                                } else {
                                    echo Form::checkbox('t_permis[]', 'E');
                                }
                                ?>
                                Théorique <?php
                                if (in_array('thq', $participant->t_permis)) {
                                    echo Form::checkbox('t_permis[]', 'thq', array('checked' => 'checked'));
                                } else {
                                    echo Form::checkbox('t_permis[]', 'thq');
                                }
                                ?>
                                Licence <?php
                                if (in_array('lic', $participant->t_permis)) {
                                    echo Form::checkbox('t_permis[]', 'lic', array('checked' => 'checked'));
                                } else {
                                    echo Form::checkbox('t_permis[]', 'lic');
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Date du théorique', 'd_date_permis_theorique', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('d_date_permis_theorique', Input::post('d_date_permis_theorique', isset($participant) ? \Maitrepylos\Date::db_to_date($participant->d_date_permis_theorique) : '')); ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Frais stagiaire', 'i_frais_stagiaire', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('i_frais_stagiaire', Input::post('i_frais_stagiaire', isset($participant) ? $participant->i_frais_stagiaire : '0')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Identification Bob', 'i_identification_bob', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('i_identification_bob', Input::post('i_identification_bob', isset($participant) ? $participant->i_identification_bob : '0')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </td>

        <td>
            <table class="form_right">

                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Date Inscription Onem', 'd_date_inscription_onem', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('d_date_inscription_onem', Input::post('d_date_inscription_onem', isset($participant) ? \Maitrepylos\Date::db_to_date($participant->d_date_inscription_onem) :'')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Date fin stage Onem', 'd_date_fin_stage_onem', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('d_date_fin_stage_onem', Input::post('d_date_fin_stage_onem', isset($participant) ? \Maitrepylos\Date::db_to_date($participant->d_date_fin_stage_onem) :'')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Numero inscription Onem', 't_numero_inscription_onem', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('t_numero_inscription_onem', Input::post('t_numero_inscription_onem', isset($participant) ? $participant->t_numero_inscription_onem : '0')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Date inscription forem', 'd_date_inscription_forem', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('d_date_inscription_forem', Input::post('d_date_inscription_forem', isset($participant) ? \Maitrepylos\Date::db_to_date($participant->d_date_inscription_forem) : '')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Numero inscription Forem', 't_numero_inscription_forem', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('t_numero_inscription_forem', Input::post('t_numero_inscription_forem', isset($participant) ? $participant->t_numero_inscription_forem : '0')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Date expi. carte de séj.', 'd_date_expiration_carte_sejour', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('d_date_expiration_carte_sejour', Input::post('d_date_expiration_carte_sejour', isset($participant) ? \Maitrepylos\Date::db_to_date($participant->d_date_expiration_carte_sejour) : '')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Date examen médical.', 'd_date_examen_medical', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('d_date_examen_medical', Input::post('d_date_examen_medical', isset($participant) ? \Maitrepylos\Date::db_to_date($participant->d_date_examen_medical) : '')); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <?php echo Form::label('Lieu Examen Médical', 't_lieu_examen_medical', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php echo Form::input('t_lieu_examen_medical', Input::post('t_lieu_examen_medical', isset($participant) ? $participant->t_lieu_examen_medical : '0')); ?>
                            </div>
                        </div>
                    </td>
                </tr>




            </table>
        </td>
    </tr>
</table>


<script type="text/javascript">

    $('#form_t_organisme_paiement').change(function () {
        disabledPhoneInput();
    });

    $(document).ready(function () {
        disabledPhoneInput();
        $("#add_child").click(function () {
            $('#children table > tbody').append('<tr><td><input type="text" name="t_children[]" /></td><td><input type="text" name="t_children[]" /></td><td><input type="text" name="t_children[]" class="dp" /></td><td></td><td><a class="btn btn-mini btn-danger remove_child" href="#"><i class="icon-remove-sign icon-white"></i></a></td></tr>');
            $('.dp').datepicker({
                yearRange: '-100:+10'
            }).attr("readonly", "readonly");
            remove_children();
            return false;
        });

        remove_children();
        hide_children();
        $('#form_t_enfants_charge').change(function () {
            hide_children();
        });
        get_children_age();
    });

    function remove_children() {
        $(".remove_child").click(function () {
            $(this).closest('tr').remove();
            return false;
        });
    }

    function disabledPhoneInput() {
        if ($('#form_t_organisme_paiement').val() == '') {
            $('#form_t_organisme_paiement_phone').attr('disabled', true);
        }
        else {
            $('#form_t_organisme_paiement_phone').removeAttr('disabled');
        }
    }

    function hide_children() {
        if ($('#form_t_enfants_charge').val() == '' || $('#form_t_enfants_charge').val() == 'Non') {
            $('#children').hide();
        }
        if ($('#form_t_enfants_charge').val() == 'Oui') {
            $('#children').show();
        }
    }

    function get_children_age() {
        $.each($('#children input.dp'), function (index, value) {
            var age = getAge($(this).val());
            $(this).parent().parent().find(".child_age").text(age);
        });
    }
</script>