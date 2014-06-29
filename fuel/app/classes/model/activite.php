<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of heures
 *
 * @author gg
 */
use Orm\Model;

class Model_Activite extends \Orm\Model {

    protected static $_primary_key = array('id_activite');
    protected static $_table_name = 'activite';
    protected static $_properties = array(
        'id_activite',
        't_nom',
        't_schema',
        'i_position',
        'i_active',

    );

    public static function validate($factory) 
    {
        $val = Validation::forge($factory);

        $val->add_callable('\Cranberry\MyValidation');
        $val->add_callable('\Maitrepylos\Validation');

        $val->add_field('t_nom', 'Nom', 'required|uniqueActivityName');
        $val->add_field('t_schema', 'Valeur', 'required|activite');

        $val->set_message('required', 'Veuillez remplir le champ :label.');
        $val->set_message('exact_length', 'Le champ :label doit compter exactement :param:1 caractères.');
        $val->set_message('valid_string', 'Le champ :label ne doit contenir que des chiffres.');
        $val->set_message('activite', 'Le schéma ne peux être que un des élèments suivant + @ = / % * - $ #');

        return $val;
    }

    public static function validate_modify($factory)
    {
        $val = Validation::forge($factory);
        $val->add_callable('\Maitrepylos\Validation');

        $val->add_field('t_nom', 'Nom', 'required');
        $val->add_field('t_schema', 'Valeur', 'required|activite');

        $val->set_message('required', 'Veuillez remplir le champ :label.');
        $val->set_message('exact_length', 'Le champ :label doit compter exactement :param:1 caractères.');
        $val->set_message('valid_string', 'Le champ :label ne doit contenir que des chiffres.');
        $val->set_message('activite', 'Le schéma ne peux être que un des élèments suivant + @ = / % * - $ #');

        return $val;
    }

}

?>
