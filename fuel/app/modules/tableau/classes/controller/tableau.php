<?php

namespace Tableau;

use Fuel\Core\Input;

/**
 * Controller gérant toute la partie "Participant".
 */
class Controller_Tableau extends \Controller_Main
{
    public $title = 'Tableau';
    public $data = array();

    /**
     * Override la function before().
     * Permet de vérifier si un membre est bien authentifié, sinon il est renvoyé
     * vers la page users/login et s'il a les bons droits, sinon il est renvoyé
     * vers la page users/no_rights.
     */
    public function before()
    {
        parent::before();

        if ($this->current_user == NULL) {
            \Session::set('direction', '/tableau');
            \Response::redirect('users/login');
        } else if (!\Auth::member(100)) {
            \Response::redirect('users/no_rights'); #7F7F7F
        }
    }


    public function action_index()
    {

        //création de la date en fonction du jour ou du passage de paramètres
        $lundi = new \DateTime();

        if (\Input::post('change')) {

            $c = explode('-', \Input::post('change'));

            $lundi->setDate($c[2], $c[1], $c[0]);

        }
        /**
         * Ici nous récupérons les heures introduites par les formateurs et nous les traitons
         */
        if (\Input::post('action')) {


            $heure = \Input::post('action');

            foreach ($heure as $heures) {

                list($date, $id_participant, $prestation, $schema, $id_contrat, $nom) = explode('/', $heures);
                // $h = new \Maitrepylos\Timetosec();
                $insert = new \Model_My_Prestation();
                $datetime = \DateTime::createFromFormat('Y-m-d', $date);
                $insert->insertion_heures_prestation($id_participant, $datetime, $id_contrat, $prestation, $nom, $schema, 1);
                //$insert->insertHeures($date, $prestation, $nom, $schema, $id_participant, $id_contrat, 0,1);


            }
            $c = explode('-', \Input::post('affiche'));

            $lundi->setDate($c[2], $c[1], $c[0]);

        }


        /**
         * Récupération du lundi de la semaine concerné par l'affichage du tableau
         */
        $semaine = $lundi->format('W');
        $annee = $lundi->format('Y');

        /**
         * Gestion spécifique au dernier jours de l'année
         * Exemple le 31-12-2013 donne la première semaine de l'année, du coup si on ne change pas d'année, on se retrouve
         * à la première semaine de l'année en cours au lieu de passé en 2014.
         */
        $newYear  = date('Y', strtotime('+7 days', strtotime($lundi->format('Y-m-d'))));
        ($newYear == ($annee+1))?$annee++:$annee;


        $lundi->setISOdate($annee, $semaine);


        /**
         * Création des dates de toutes la semaines iterator_to_array permet de créer un tableau
         */
        $dateTableau = iterator_to_array(new \DatePeriod($lundi, new \DateInterval('P1D'), 4));

        /**
         * Semaine suivante
         */

        $semaine_next = clone $lundi;
        $semaine_next->add(new \DateInterval('P7D'));


        /**
         * Semaine précédente
         */
        $semaine_pre = clone $lundi;
        $semaine_pre->sub(new \DateInterval('P7D'));


        $dbTableau = new \Model_My_Tableau();

        //Récupération des groupes que le formateur gère.
         $groupe = $dbTableau->getGroupe(\Session::get('id_login'));
        /**
         * Au 20/01/2014, nous avons mis en place la gestion des groupes
         * pour l'instant on donne accès a tout le monde, quand il faudra revenir en arrière passer la fonction ligne 96
        //$groupe = $dbTableau->getGroupe();
         */

        $countGroupe = count($groupe);


        for ($i = 0; $i < $countGroupe; $i++) {
            /**
             * Récupération des participants concerné par les dates du formateurs
             */
            $groupe[$i]['participant'] = $dbTableau->getParticipantTableau($groupe[$i]['id_groupe'], $dateTableau[0]);

            $countParticipant = count($groupe[$i]['participant']);
            for ($a = 0; $a < $countParticipant; $a++) {
                //Vérifications des jours que les participant peuvent ou pas avoir des heures
                foreach ($dateTableau as $time) {
                    $groupe[$i]['participant'][$a]['contrat'][] = $dbTableau->get_contrat($groupe[$i]['participant'][$a]['id_participant']
                        , $time, $groupe[$i]['participant'][$a]['id_groupe']);

                }
                //Si le participant n'a pas de contrat à cette date précise, on ne l'affiche pas.
                if (!in_array(true, $groupe[$i]['participant'][$a]['contrat'])) {
                    unset($groupe[$i]['participant'][$a]);
                } else {
                    $groupe[$i]['participant'][$a]['id_contrat'] = $dbTableau->get_unique_id_contrat($groupe[$i]['participant'][$a]['id_participant']);
                }

            }


        }


        $this->data['date'] = $dateTableau;
        $this->data['groupe'] = $groupe;
        $this->data['next'] = $semaine_next->format('d-m-Y');
        $this->data['pre'] = $semaine_pre->format('d-m-Y');
        //\Maitrepylos\Debug::dump(\Input::post('action'));
        $this->template->title = $this->title;

        if (\Cranberry\MyMobileDetection::is_mobile())
            $this->template->content = \View::forge('tableau/mobile', $this->data);
        else
            $this->template->content = \View::forge('tableau/index', $this->data);
        //$this->template->content = \View::forge('test');


    }

}
