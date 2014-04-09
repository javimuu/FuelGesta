<?php

use Orm\Model;

class Model_Contrat extends Orm\Model
{


    protected static $_primary_key = array('id_contrat');
    protected static $_table_name = 'contrat';
    protected static $_properties = array(
        'id_contrat',
        'i_temps_travail',
        'd_date_debut_contrat',
        'd_date_fin_contrat',
        'd_date_fin_contrat_prevu',
        't_remarque',
        'f_frais_deplacement',
        't_duree_innoccupation',
        'b_derogation_rw',
        't_abonnement',
        'f_tarif_horaire',
        't_situation_sociale',
        'd_avertissement1',
        'd_avertissement2',
        'd_avertissement3',
        't_motif_avertissement1',
        't_motif_avertissement2',
        't_motif_avertissement3',
        'd_date_demande_derogation_rw',
        't_connaissance_eft',
        't_ressource',
        't_passe_professionnel',
        'd_date_reponse_onem',
        'd_date_demande_onem',
        'b_dispense_onem',
        'b_reponse_rw',
        'd_date_demande_forem',
        'b_reponse_forem',
        'b_necessaire',
        't_moyen_transport',
        'groupe_id',
        'participant_id',
        'type_contrat_id'
    );

    public static function validate($factory)
    {
        $val = Validation::forge($factory);
        $val->add_callable('MaitrePylos\validation');
        $val->set_message('max_length', 'Le champ :label doit faire maximum :param:1 chiffres');
        //$val->set_message('exceeds_onehundred', 'Le :label ne peut dépasser 100');
        //J'ajoute ici la vérification des dates, car il n'est pas possible de le faire dans \model_contat
        $val->add_field('d_date_fin_contrat_prevu', 'Date Fin de contrat prévu', 'required|date_less[' . \Input::post('d_date_debut_contrat') . ']|valid_date[d/m/Y]');
        $val->add_field('d_date_debut_contrat', 'Formation ', 'eighteen_months_more[' . \Input::post('d_date_fin_contrat_prevu') . ']|valid_date[d/m/Y]');
        $val->add_field('f_frais_deplacement', 'Frais de déplacement', 'numeric');
        $val->add_field('t_moyen_transport', 'Moyen Transport', 'required');
        $val->add_field('t_duree_innoccupation', 'Durée innoccupation', 'numeric');
        $val->add_field('d_avertissement1', 'Date avertissement', 'date_is_range['. \Input::post('d_date_debut_contrat') .',' . \Input::post('d_date_fin_contrat_prevu') . ']');
        $val->add_field('d_avertissement2', 'Date avertissement', 'date_is_range['. \Input::post('d_date_debut_contrat') .',' . \Input::post('d_date_fin_contrat_prevu') . ']');
        $val->add_field('d_avertissement3', 'Date avertissement', 'date_is_range['. \Input::post('d_date_debut_contrat') .',' . \Input::post('d_date_fin_contrat_prevu') . ']');
        $val->set_message('date_less', 'La :label ne peut être inférieure à la date de début de contrat');
        $val->set_message('date_is_range', 'La :label doit-être comprise entre la date d\'entrée et de sortie');
        $val->set_message('eighteen_months_more', 'La :label ne peut être supérieure à 18 mois');
        $val->set_message('valid_string', 'Le :label doit-etre numérique');
        $val->set_message('numeric', 'Le :label doit-etre numérique');
        $val->set_message('valid_date', 'La :label est incorecte');
        $val->set_message('required', 'Veuillez remplir le champ :label.');

        //$val->add_field('i_temps_travail', 'Temps de Travail', 'required|max_length[3]|exceeds_onehundred');

        return $val;
    }

    public static function getTempTravail($id)
    {

        $heures = DB::select('i_heures')
            ->from('type_contrat')
            ->where('id_type_contrat', $id)
            ->execute();
        return $heures->as_array();

    }

    public static function getContrat()
    {

        //$contrat =  DB::select('SELECT * FROM type_contrat tc INNER JOIN subside s ON s.id_subside = tc.subside',DB::SELECT)->execute();
        //return $contrat->as_array();

        return DB::select()->from('type_contrat')->join('subside', 'INNER')
            ->on('subside.id_subside', '=', 'type_contrat.subside_id')->order_by('i_position', 'asc')->execute();

    }




}