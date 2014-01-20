<?php

use Orm\Model;

class Model_Users_Groupe extends Orm\Model
{

    private $_db = null;
    protected static $_primary_key = array('id_users_groupe');
    protected static $_table_name = 'users_groupe';

    protected static $_properties = array(
        'id_users_groupe',
        'users_id',
        'groupe_id',

    );


    public function delete_gestionnaire($id)
    {
        $this->_db = \Maitrepylos\Db::getPdo();

        $sql = 'DELETE FROM users_groupe WHERE groupe_id = ?';
        $req = $this->_db->prepare($sql);
        $req->execute(array($id));

        //$this->_db->delete('activite',array('id_activite'=>$id));
    }

    public static function getGestionnaire($id)
    {
        $db = \Maitrepylos\Db::getPdo();

        $sql = 'SELECT users_id FROM users_groupe WHERE groupe_id = ?';
        $req = $db->prepare($sql);
        $req->execute(array($id));
        return $req->fetchAll(PDO::FETCH_ASSOC);

    }

    public static function getGestionnaireAffiche($id=0){

        $db = \Maitrepylos\Db::getPdo();

        $sql = "SELECT id,username,coche FROM (
                  	SELECT u.id as id, 1  as coche,u.username
		            FROM users_groupe ug
				      INNER JOIN users u
					    ON u.id = ug.users_id
		            WHERE groupe_id = ?

                  UNION

                  SELECT id as id ,0 as coche,username
                  FROM users
                  WHERE is_actif = 1
                  ) tbl
                GROUP BY id";

        $req = $db->prepare($sql);
        $req->execute(array($id));
        return $req->fetchAll(PDO::FETCH_OBJ);
    }


}
