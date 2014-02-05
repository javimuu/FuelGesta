<?php

use Orm\Model;

class Model_Type_Ressource extends Model
{

    protected static $_primary_key = array('id_type_ressource');
    protected static $_table_name = 'type_ressource';
    protected static $_properties = array(
        'id_type_ressource',
        't_nom',
        'i_position',
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
        $query = \DB::select()->from('type_ressource')->execute();
        $result = $query->as_array('id_type_ressource', 't_nom');
        $types = array();
        foreach ($result as $id => $nom)
        {
            $types[$nom] = $nom;
        }
        return $types;
    }

    public function updateActivite($tab)
    {

        $sql = "SELECT COUNT(id_type_ressource) FROM type_ressource";
        $result = $this->_db->prepare($sql);
        $result->execute();
        $count = $result->fetchAll(PDO::FETCH_ASSOC);

        $count++;

        for ($i = 1; $i < $count; $i++) {
            $id = $i - 1;
            $sql = 'UPDATE type_ressource SET i_position = ? WHERE id_type_ressource = ?';
            $result = $this->_db->prepare($sql);
            $result->execute(array($i, $tab[$id]));
            //$this->_db->update('activite', array('i_position' => $i), array('id_activite' => $tab[$id]));


        }

    }

    public function add_activite($nom)
    {
        $this->_db = \Maitrepylos\Db::getPdo();
        $sql = 'SELECT (MAX(i_position) + 1) AS number FROM type_ressource';
        $req = $this->_db->query($sql);


        $count = $req->fetchAll();
        if($count[0]['number'] == null){
            $count[0]['number'] = 1;
        }
        $sql = 'INSERT type_ressource (t_nom,i_position) VALUES(?,?)';
        $req = $this->_db->prepare($sql);
        $req->execute(array($nom, $count[0]['number']));

        // $this->_db->insert('activite',array('t_nom'=>$nom,'t_schema'=>$schema,'i_position'=>$count[0]['number']));


    }

    public function del_activite($id)
    {

        $sql = 'DELETE FROM type_ressource type WHERE id_type_ressource = ?';
        $req = $this->_db->prepare($sql);
        $req->execute(array($id));

        //$this->_db->delete('activite',array('id_activite'=>$id));
    }

}
