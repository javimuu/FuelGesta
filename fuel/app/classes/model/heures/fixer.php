<?php

use Orm\Model;

class Model_Heures_Fixer extends Orm\Model {

    protected static $_primary_key = array('id_heures_fixer');
    protected static $_table_name = 'heures_fixer';
    protected static $_properties = array(
        'id_heures_fixer',
        'd_date',
        'i_heures',
        't_motif',
        'participant_id',
    );

    public static function validate_heures($factory) {
        $val = Validation::forge($factory);
        $val->add_callable('\Maitrepylos\Validation');
        $val->add_field('i_heures', 'Heures', 'required|bland_hour|no_hour|min_hour');
        $val->set_message('min_hour', 'Le champ :label ne peux être inférieur à ' .
            \Maitrepylos\Config::getMinTime());
        $val->set_message('no_hour', 'Le champ :label ne peux être égal à 0 . ');
        $val->set_message('required', 'Le champ :label est obligatoire');
        $val->set_message('bland_hour', 'le champ :label doit être de forme 00:00');


        return $val;
    }

}
