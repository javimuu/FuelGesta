
<?php

use Orm\Model;

class Model_Statut_Entree extends Model
{

    protected static $_primary_key = array('id_statut_entree');
    protected static $_table_name = 'statut_entree';
    protected static $_properties = array(
        'id_statut_entree',
        't_nom',
        't_valeur',
        'i_position',
        'type_statut_id',
    );
    
    protected static $_belongs_to = array(
        'type_statut' => array(
            'key_from' => 'type_statut_id',
            'model_to' => 'Model_Type_Statut',
            'key_to' => 'id_type_statut',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
    );

    public static function validate($factory)
    {
        $val = Validation::forge($factory);
        $val->add_field('t_nom', 'Nom', 'required|max_length[255]');
        $val->add_field('t_valeur', 'Valeur', 'required');

        $val->set_message('required', 'Veuillez remplir le champ :label.');
        $val->set_message('max_length', 'Le champ :label doit faire au plus :param:1 caractères.');

        return $val;
    }

}
