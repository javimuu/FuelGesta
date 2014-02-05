<?php
use Orm\Model;

class Model_Type_Cedefop extends Model
{
    protected static $_primary_key = array('id_cedefop');
    protected static $_table_name = 'type_cedefop';
    
    protected static $_properties = array(
            'id_cedefop',
            't_nom',
            'i_code',
            'i_position',
    );
    

    public static function validate($factory)
    {
        $val = Validation::forge($factory);
        $val->add_field('t_nom', 'Nom', 'required|max_length[255]');
        $val->add_field('i_code', 'Code ', 'required|exact_length[3]|valid_string[numeric]');

        $val->set_message('required', 'Veuillez remplir le champ :label.');
        $val->set_message('min_length', 'Le champ :label doit faire au moins :param:1 caractères.');
        $val->set_message('max_length', 'Le champ :label doit faire au plus :param:1 caractères.');
        $val->set_message('exact_length', 'Le champ :label doit compter exactement :param:1 caractères.');
        $val->set_message('valid_string', 'Le champ :label ne doit contenir que des chiffres.');

        
        return $val;
    }
    
    public static function getAsSelect()
    {
        $query = \DB::select()->from('type_cedefop')->execute();
        $result = $query->as_array('id_cedefop', 't_nom');
        $types = array();
        foreach ($result as $id => $nom)
        {
            $types[$id] = $nom;
        }
        return $types;
    }

    public static function getCedefop(){

        $query = \DB::select()->from('type_cedefop')->execute();
        return $query;
    }


}
