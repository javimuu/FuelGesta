<?php

use Orm\Model;

class Model_Checklist extends Orm\Model
{

    protected static $_primary_key = array('id_checklist');
    protected static $_table_name = 'checklist';
    protected static $_properties = array(
        'id_checklist',
        'participant_id',
        'stagiaire_id',
        't_liste',
    );
    
    protected static $_belongs_to = array(
        'participant' => array(
            'key_from' => 'participant_id',
            'model_to' => 'Model_Participant',
            'key_to' => 'id_participant',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
    
    public static function getList($id)
    {
        $result = \DB::select('*')->from('checklist')->where('stagiaire_id', $id)->as_assoc()->execute();

        $liste = array();
        foreach ($result as $res)
        {
            $liste = $res['t_liste'];
        }
        
        return $liste;
    }
    
    public static function saveParticipant($stagiaireid, $participantid)
    {
        \Fuel\Core\DB::update('checklist')
                ->set(array(
                    'participant' => $participantid
                ))
                ->where('stagiaire_id', '=', $stagiaireid)->execute();
    }

}
