<?php

    use Orm\Model;

class Model_Groupe extends Model
{

    protected static $_primary_key = array('id_groupe');
    protected static $_table_name = 'groupe';
    protected static $_properties = array(
        'id_groupe',
        'login_id',
        't_filiere',
        't_nom',
        'i_lundi',
        'i_mardi',
        'i_mercredi',
        'i_jeudi',
        'i_vendredi',
        'i_samedi',
        'i_dimanche',
        'localisation_id',
        'filiere_id'
    );
    
    protected static $_belongs_to = array(

        'user' => array(
            'key_from' => 'login_id',
            'model_to' => 'Model_User',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
        'localisations' => array(
            'key_from' => 'localisation_id',
            'model_to' => 'Model_Localisation',
            'key_to' => 'id_localisation',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
        'filieres' => array(
            'key_from' => 'filiere_id',
            'model_to' => 'Model_Filiere',
            'key_to' => 'id_filiere',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),

    );
    
    protected static $_has_one = array(
        'stagiaire' => array(
            'key_from' => 'id_groupe',
            'model_to' => 'Model_Listeattente',
            'key_to' => 'groupe_id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );

    public static function getNames()
    {
        $result = array();
        $contrat = DB::select('id_groupe', 't_nom')->from('groupe')->execute();
        foreach ($contrat->as_array() as $value) {
            $result[$value['id_groupe']] = $value['t_nom'];

        }
        return $result;

    }

    public static function getNamesForem()
    {
        $result = array();
        $sql = "
        SELECT DISTINCT(id_groupe),t_nom FROM groupe g
        INNER JOIN contrat  c
        ON c.groupe_id = id_groupe
        INNER JOIN type_contrat tc
        ON tc.id_type_contrat = c.type_contrat_id
        WHERE tc.i_forem = 1
        ";
        $contrat = DB::query($sql)->execute();
        foreach ($contrat->as_array() as $value) {
            $result[$value['id_groupe']] = $value['t_nom'];

        }
        return $result;

    }

    public static function getName($groupe){

        $r =  DB::select('t_nom')->from('groupe')->where('id_groupe',$groupe)->execute();
        return $r->as_array();
    }

    /**
     * Vérifie si le groupe est dirigé par le login dont l'id est
     * passé en paramètre.
     *
     * @param type $id
     *
     * @return type
     */
    public static function hasOwner($id)
    {
        $result = DB::select('*')->from('groupe')->where('login_id', $id)->execute();
        return count($result);
    }

    public static function getGroupeUser()
    {
        $sql = "SELECT g.id_groupe,g.t_nom,g.t_code_cedefop,
                g.i_lundi,g.i_mardi,g.i_mercredi,g.i_jeudi,g.i_vendredi,
                g.i_samedi,i_dimanche,u.username
                FROM groupe g 
                INNER JOIN users u
                ON g.login = u.id;";
        $result = $query = DB::query($sql)->execute();
        return $result;
    }
    
    public static function getAsArray()
    {
        $result = \DB::select()->from('groupe')->execute();
        $key_groupes = $result->as_array('id_groupe', 't_nom');
        $groupes = array();
        foreach ($key_groupes as $id => $nom)
        {
            $groupes[$id] = $nom;
        }
        return $groupes;
    }

    public static  function getCedefop(){
        $pdo = \Maitrepylos\Db::getPdo();
        $sql = 'SELECT g.t_nom,f.i_code_cedefop
                FROM groupe AS g
                INNER JOIN filiere AS f
                ON g.filiere_id = f.id_filiere';
        $r = $pdo->prepare($sql);
        $r->execute();
        return $r->fetchAll(\PDO::FETCH_OBJ);

    }


    public static function validate($factory) 
    {
        $val = Validation::forge($factory);

        $val->add_callable('\Cranberry\MyValidation');

        $val->add_callable('\MaitrePylos\Validation');
        $val->add_field('t_nom', 'Nom', 'required');
        $val->add_field('i_lundi', 'Lundi', 'bland_hour');
        $val->add_field('i_mardi', 'Mardi', 'bland_hour');
        $val->add_field('i_mercredi', 'Mercredi', 'bland_hour');
        $val->add_field('i_jeudi', 'Jeudi', 'bland_hour');
        $val->add_field('i_vendredi', 'Vendredi', 'bland_hour');
        $val->add_field('i_samedi', 'Samedi', 'bland_hour');
        $val->add_field('i_dimanche', 'Dimanche', 'bland_hour');


        $val->set_message('required', 'Veuillez remplir le champ :label.');
        $val->set_message('exact_length', 'Le champ :label doit compter exactement :param:1 caractères.');
        $val->set_message('valid_string', 'Le champ :label ne doit contenir que des chiffres.');
        $val->set_message('bland_hour', 'Le champ :label doit être écrit de cette façon 00:00.');
        $val->set_message('min_hour', 'Le champ :label ne peux être inférieur à ' .
            \Maitrepylos\Config::getMinTime());
        $val->set_message('no_hour', 'Le champ :label ne peux être égal à 0 . ');
        
        return $val;
    }

}
