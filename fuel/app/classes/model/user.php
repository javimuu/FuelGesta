<?php

class Model_User extends \Orm\Model 
{

    protected static $_properties = array(
        'id',
        'username',
        'password',
        'group',
        'email',
        'last_login',
        'login_hash',
        'profile_fields',
        'is_actif',
        't_nom',
        't_prenom',
        't_acl'
    );
    
    protected static $_has_many = array(
        'groupes' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Groupe',
            'key_to' => 'login',
            'cascade_save' => true,
            'cascade_delete' => true,
        )
    );

    public static function validate($factory, $required_password = true) 
    {
        $val = Validation::forge($factory);

        $val->add_callable('\Cranberry\MyValidation');
        $val->add_field('username', 'Login', 'required');        
        $val->add_field('t_nom', 'Nom', 'required');
        $val->add_field('t_prenom', 'Prénom', 'required');
        if($required_password)
            $val->add_field('password', 'Mot de passe', 'required');
        
        $val->set_message('required', 'Veuillez remplir le champ :label.');

        return $val;
    }

    public static function get_users()
    {

        $query = \DB::select('id', 'username')->from('users')->where('is_actif',1)->execute();
        return $query;

    }

}
