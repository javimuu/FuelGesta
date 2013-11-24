<?php

use Orm\Model;

class Model_Type_Statut extends Model
{

    protected static $_primary_key = array('id_type_statut');
    protected static $_table_name = 'type_statut';
    protected static $_properties = array(
        'id_type_statut',
        't_nom',
    );

    protected static $_has_many = array(
        'statuts_entree' => array(
            'key_from' => 'id_type_statut',
            'model_to' => 'Model_Statut_Entree',
            'key_to' => 'type_statut_id',
            'cascade_save' => true,
            'cascade_delete' => true,
        )
    );
    
    public static function validate($factory)
    {
        $val = Validation::forge($factory);
        $val->add_field('t_nom', 'Tnom', 'required|max_length[255]');
        
        $val->set_message('required', 'Veuillez remplir le champ :label.');

        return $val;
    }
    
    public static function getAsSelect()
    {
        $query = \DB::select()->from('type_statut')->execute();
        $result = $query->as_array('id_type_statut', 't_nom');
        $types = array();
        foreach ($result as $id => $nom)
        {
            $types[$id] = $nom;
        }
        return $types;
    }

}
