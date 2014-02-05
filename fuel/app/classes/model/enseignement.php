<?php

use Orm\Model;

class Model_Enseignement extends Model
{

    protected static $_primary_key = array('id_enseignement');
    protected static $_table_name = 'enseignement';
    protected static $_properties = array(
        'id_enseignement',
        't_nom',
        't_valeur',
        'i_position',
        'type_enseignement_id',
    );
    
    protected static $_belongs_to = array(
        'type_enseignement' => array(
            'key_from' => 'type_enseignement_id',
            'model_to' => 'Model_Type_Enseignement',
            'key_to' => 'id_type_enseignement',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
    );

    public static function validate($factory)
    {
        $val = Validation::forge($factory);
        $val->add_field('t_nom', 'Nom', 'required|max_length[255]');
        $val->add_field('t_valeur', 'Valeur', 'required|max_length[10]');



        $val->set_message('valid_string', 'Le champ :label ne doit contenir que des chiffres.');
        $val->set_message('required', 'Veuillez remplir le champ :label.');
        $val->set_message('max_length', 'Le champ :label doit faire au plus :param:1 caractères.');

        return $val;
    }

}
