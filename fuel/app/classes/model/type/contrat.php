<?php

use Orm\Model;


class Model_Type_Contrat extends Model {

    protected static $_primary_key = array('id_type_contrat');
    protected static $_table_name = 'type_contrat';
    protected static $_properties = array(
        'id_type_contrat',
        't_type_contrat',
        'b_type_contrat_actif',
        'i_heures',
        'i_paye',
        'subside_id',
        'i_forem'
    );

    public static function deleteContrat($id){

        $pdo = \Maitrepylos\Db::getPdo();
        $sql = "DELETE FROM type_contrat WHERE id_type_contrat = ? ";
        $r = $pdo->prepare($sql);
        return $r->execute(array($id));

    }

    public static function getListeContrat($id){

        $pdo = \Maitrepylos\Db::getPdo();
        $sql = "SELECT p.id_participant,p.t_nom,p.t_prenom,c.d_date_debut_contrat,c.d_date_fin_contrat_prevu,g.t_nom as groupe_nom
                FROM contrat c
                INNER JOIN participant p
                ON c.participant_id = p.id_participant
                INNER JOIN groupe g
                ON g.id_groupe = c.groupe_id
                WHERE c.type_contrat_id = ?
                ORDER BY p.t_nom,c.d_date_debut_contrat ASC";

        $r = $pdo->prepare($sql);
        $r->execute(array($id));
        return $r->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getNames() {
        $result = array();
        $contrat = DB::select('id_type_contrat', 't_type_contrat')->from('type_contrat')->order_by('i_position')->execute();
        foreach ($contrat->as_array() as $value) {
            $result[$value['id_type_contrat']]= $value['t_type_contrat'];
            
        }        
        return $result;
       
    }
    
    public static function validate($factory) 
    {
        $val = Validation::forge($factory);
        $val->add_callable('\Maitrepylos\Validation');
        $val->add_field('i_heures', 'Heures', 'required|bland_hour|more_forty_hours');
        $val->add_field('t_type_contrat', 'Type', 'required');

        return $val;
    }




}
