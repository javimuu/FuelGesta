<?php

namespace Administration;

use Fuel\Core\Asset;
use Fuel\Core\Input;

/**
 * Controller gérant toute la partie administration
 */
class Controller_Administration extends \Controller_Main
{

    public $title = 'Administration';
    public $data = array();
    private $view_dir = 'administration/';
    private $partial_dir = 'administration/partials/';

    /**
     * Redirige toute personne non membre du groupe "100"
     */
    public function before()
    {
        parent::before();

        if (!\Auth::member(100)) {
            \Session::set('direction', '/administration');
            \Response::redirect('users/login');
        }

        $this->data['view_dir'] = $this->view_dir;
        $this->data['partial_dir'] = $this->partial_dir;
    }

    /**
     * Affichage du menu de l'administration
     */
    public function action_index()
    {
        $this->template->title = 'Administration';
        $this->template->content = \View::forge($this->view_dir . 'index');
        $this->template->content->view_dir = $this->view_dir;
    }

    /**
     * On récupère la liste des pays et on l'affiche.
     */
    public function action_liste_pays_xml()
    {
        $this->template->title = 'Administration - Gestion des pays';

        $pays = \Cranberry\MyXML::getXML('pays');

        $this->data['pays'] = $pays;
        $this->template->content = \View::forge($this->view_dir . 'pays', $this->data);
    }

    /**
     * Ajouter un pays au fichier XML
     */
    public function action_ajouter_pays_xml($schema)
    {
        if (\Input::method() == 'POST') {
            $val = \Validation::forge();
            $val->add_field('nom', 'Nom', 'required');
            $val->add_field('valeur', 'Valeur', 'required|exact_length[2]|valid_string[numeric]');

            $val->set_message('required', 'Veuillez remplir le champ :label.');
            $val->set_message('exact_length', 'Le champ :label doit compter exactement :param:1 caractères.');
            $val->set_message('valid_string', 'Le champ :label ne doit contenir que des chiffres.');

            if ($val->run()) {
                \Cranberry\MyXML::addNode('pays', \Cranberry\MySanitarization::ucFirstAndToLower(\Cranberry\MySanitarization::filterAlpha(\Input::post('nom'))), \Input::post('valeur'), 'pays', $schema);

                $message[] = "Le pays a bien été ajouté.";
                \Session::set_flash('success', $message);

                \Response::redirect($this->view_dir . 'liste_pays_xml');
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->template->title = 'Administration - Gestion des pays';
        $this->template->content = \View::forge($this->view_dir . 'ajouter_pays', $this->data);
    }

    /**
     * Supprimer un pays du fichier XML
     *
     * @param type $continent
     * @param type $item
     */
    public function action_supprimer_pays_xml($schema, $item)
    {
        \Cranberry\MyXML::deleteNode('pays', $schema, $item, 'pays');
        \Response::redirect($this->view_dir . 'liste_pays_xml');
    }

    /**
     * Affiche les centres
     */
    public function action_liste_centres()
    {
        $this->template->title = 'Administration - Gestion des centres';

        if (\Input::method() == 'POST') {

            \Maitrepylos\Db::updatePosition(\Input::post('table_activite'), 'centre', 'id_centre');

        }

        $centres = \Model_Centre::find('all', array('order_by' => array('i_position' => 'asc')));

        $this->data['centres'] = $centres;
        $this->template->content = \View::forge($this->view_dir . 'centres', $this->data);
    }

    /**
     * Modifie des coordonnees
     */
    public function action_modifier_centre($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_centres');

        $this->template->title = 'Administration - Gestion des coordonnées';

        $centre = \Model_Centre::find($id);

        if (\Input::method() == 'POST') {

            $val = \Model_Centre::validate('edit');

            if ($val->run()) {
                $centre->t_responsable = \Input::post('t_responsable');
                $centre->t_statut = \Input::post('t_statut');
                $centre->t_denomination = \Input::post('t_denomination');
                $centre->t_nom_centre = \Input::post('t_nom_centre');
                $centre->t_objet_social = \Input::post('t_objet_social');
                $centre->t_agregation = \Input::post('t_agregation');
                $centre->t_agence = \Input::post('t_agence');
                $centre->t_adresse = \Input::post('t_adresse');
                $centre->t_code_postal = \Input::post('t_code_postal');
                $centre->t_localite = \Input::post('t_localite');
                $centre->t_telephone = \Input::post('t_telephone');
                $centre->t_email = \Input::post('t_email');
                $centre->t_tva = \Input::post('t_tva');
                $centre->t_enregistrement = \Input::post('t_enregistrement');
                // $centre->t_agrement = \Input::post('t_agrement');
                $centre->t_responsable_pedagogique = \Input::post('t_responsable_pedagogique');
                $centre->t_secretaire = \Input::post('t_secretaire');

                // On save si c'est bon
                if ($centre and $centre->save()) {
                    $message[] = "Le centre a bien été mis à jour.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_centres');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le centre.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }
        // On récupère la liste des users pour permettre de choisir le responsable
        $users = \Model_User::find('all', array(
            'where' => array(
                array('is_actif', 1)
            )
        ));

        $select_users = array();

        foreach ($users as $value) {
            $select_users[$value->t_prenom . ' ' . $value->t_nom] = $value->t_prenom . ' ' . $value->t_nom;
        }

        $this->data['user'] = $select_users;
        $this->data['action'] = 'Modifier';
        $this->data['centre'] = $centre;
        $this->template->content = \View::forge($this->view_dir . 'form_centre', $this->data);
    }

    /**
     * Ajoute des coordonnees
     */
    public function action_ajouter_centre()
    {
        $this->template->title = 'Administration des coordonnées';

        if (\Input::method() == 'POST') {
            $val = \Model_Centre::validate('edit');

            if ($val->run()) {
                $position = \Maitrepylos\Db::getMaxPosition('centre');
                $centre = \Model_Centre::forge(array(
                    't_responsable' => \Input::post('t_responsable'),
                    't_statut' => \Input::post('t_statut'),
                    't_denomination' => \Input::post('t_denomination'),
                    't_nom_centre' => \Input::post('t_nom_centre'),
                    't_objet_social' => \Input::post('t_objet_social'),
                    't_agregation' => \Input::post('t_agregation'),
                    't_agence' => \Input::post('t_agence'),
                    't_adresse' => \Input::post('t_adresse'),
                    't_code_postal' => \Input::post('t_code_postal'),
                    't_localite' => \Input::post('t_localite'),
                    't_telephone' => \Input::post('t_telephone'),
                    't_email' => \Input::post('t_email'),
                    't_tva' => \Input::post('t_tva'),
                    't_enregistrement' => \Input::post('t_enregistrement'),
                    // 't_agrement' => \Input::post('t_agrement'),
                    't_responsable_pedagogique' => \Input::post('t_responsable_pedagogique'),
                    't_secretaire' => \Input::post('t_secretaire'),
                    'i_position' => $position[0]['i_position'],
                ));

                // On save si c'est bon
                if ($centre and $centre->save()) {
                    $message[] = "Le centre a bien été ajouté.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_centres');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le centre.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }
        // On récupère la liste des users pour permettre de choisir le responsable
        $users = \Model_User::find('all', array(
            'where' => array(
                array('is_actif', 1)
            )
        ));

        $select_users = array();

        foreach ($users as $value) {
            $select_users[$value->t_prenom . ' ' . $value->t_nom] = $value->t_prenom . ' ' . $value->t_nom;
        }

        $this->data['user'] = $select_users;
        $this->data['action'] = 'Ajouter';
        $this->template->content = \View::forge($this->view_dir . 'form_centre', $this->data);
    }

    /**
     * Supprime un centre
     *
     * @param int $id
     */
    public function action_supprimer_centre($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_centres');

        if ($centre = \Model_Centre::find($id)) {
            $centre->delete();

            $message[] = 'Le centre a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer le groupe';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_centres');
    }

    /**
     * Affiche les groupes en les récupérant depuis la db.
     */
    public function action_liste_groupes()
    {
        $this->template->title = 'Administration - Gestion des groupes';

        $groupes = \Model_Groupe::find('all', array('related' => array('localisations', 'filieres', 'user')));

        $this->data['groupes'] = $groupes;
        $this->template->content = \View::forge($this->view_dir . 'groupes', $this->data);
    }

    /**
     * Ajouter un groupe
     */
    public function action_ajouter_groupe()
    {
        $this->template->title = 'Administration - Gestion des groupes';

        if (\Input::method() == 'POST') {
            $time = new \Maitrepylos\Timetosec();

            $val = \Model_Groupe::validate('create_groupe');

            if ($val->run()) {
                $centre = \Model_Localisation::get_localisation_names(\Input::post('localisation_id'));
                // On forge un objet Groupe
                $nom_groupe = trim(\Cranberry\MySanitarization::ucFirstAndToLower(\Cranberry\MySanitarization::filterAlpha(
                        \Input::post('t_nom')))) . '-' . trim($centre[0]['t_lieu']);
                $groupe = \Model_Groupe::forge(array(
                    't_nom' => $nom_groupe,
                    't_filiere' => \Input::post('t_filiere'),
                    'filiere_id' => \Input::post('t_filiere'),
                    'localisation_id' => \Input::post('localisation_id'),
                    'login_id' => \Input::post('login_id'),
                    'i_lundi' => $time->StringToTime(\Input::post('i_lundi')),
                    'i_mardi' => $time->StringToTime(\Input::post('i_mardi')),
                    'i_mercredi' => $time->StringToTime(\Input::post('i_mercredi')),
                    'i_jeudi' => $time->StringToTime(\Input::post('i_jeudi')),
                    'i_vendredi' => $time->StringToTime(\Input::post('i_vendredi')),
                    'i_samedi' => $time->StringToTime(\Input::post('i_samedi')),
                    'i_dimanche' => $time->StringToTime(\Input::post('i_dimanche'))
                ));

                // On sauvegarde
                if ($groupe->save()) {

                    //si le groupe est créé on crée les gestionnaires, pour la gestions de sheures au niveau tableau.
                    $gestion = \Input::post('gestion');

                    foreach ($gestion as $value) {
                        $new = new \Model_Users_Groupe();
                        $new->users_id = $value;
                        $new->groupe_id = $groupe->id_groupe;
                        $new->save();
                    }


                    $message[] = 'Le groupe a bien été créé.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_groupes');
                } else {
                    $message[] = 'Impossible de créer le groupe.';
                    $message[] = $val->show_errors();
                    \Response::redirect($this->view_dir . 'ajouter_groupe');

                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }

        }

        $filieres = \Model_Filiere::find('all');

        foreach ($filieres as $value) {

            $filiere[$value->id_filiere] = $value->t_nom;

        }


        // On récupère la liste des users pour permettre de choisir le responsable
        $users = \Model_User::find('all', array(
            'where' => array(
                array('is_actif', 1)
            )
        ));

        $select_users = array();

        foreach ($users as $value) {
            $select_users[$value->id] = $value->username;
        }

        $gestionnaire = \Model_Users_Groupe::getGestionnaireAffiche();

        $gestion_users = array();

        foreach ($gestionnaire as $value) {
            $gestion_users[$value->id][] = $value->username;
            $gestion_users[$value->id][] = $value->coche;
        }

//        //on récupère le nom des centres dans le fichier xml
//        $path = Asset::find_file('coordonnees.xml', 'xml');
//        $xml = simplexml_load_file($path);
//        $select_lieu = array();
//        foreach($xml->centre as $value) {
//            $select_lieu[(string)$value->nom_centre] = (string)$value->nom_centre;
//        }
        /**
         * On récupère les centres dans la db
         */
        $centre = \Model_Localisation::find('all');
        $select_lieu = array();

        foreach ($centre as $value) {
            $select_lieu[$value->id_localisation] = $value->t_lieu;

        }


        $this->data['action'] = 'Ajouter';
        $this->data['centre'] = $select_lieu;
        $this->data['users'] = $select_users;
        $this->data['gestionnaire'] = $gestion_users;
        $this->data['filiere'] = $filiere;
        $this->template->content = \View::forge($this->view_dir . 'form_groupe', $this->data);
    }

    /**
     * Modifier Groupe
     * @param $id
     */
    public function action_modifier_groupe($id)
    {
        $this->template->title = 'Administration des groupes';

        $groupe = \Model_Groupe::find($id);
        $time = new \Maitrepylos\Timetosec();


        if (\Input::method() == 'POST') {

            $val = \Model_Groupe::validate('create_groupe');

            if ($val->run()) {
                // On forge un objet Groupe
                $centre = \Model_Localisation::get_localisation_names(\Input::post('localisation_id'));
                // On forge un objet Groupe
                $nom_groupe = trim(\Cranberry\MySanitarization::ucFirstAndToLower(\Cranberry\MySanitarization::filterAlpha(
                        \Input::post('t_nom')))) . '-' . trim($centre[0]['t_lieu']);
                $groupe->t_nom = $nom_groupe;
                $groupe->t_filiere = \Input::post('t_filiere');
                $groupe->filiere_id = \Input::post('t_filiere');
                $groupe->login_id = \Input::post('login_id');
                $groupe->localisation_id = \Input::post('localisation_id');
                $groupe->i_lundi = $time->StringToTime(\Input::post('i_lundi'));
                $groupe->i_mardi = $time->StringToTime(\Input::post('i_mardi'));
                $groupe->i_mercredi = $time->StringToTime(\Input::post('i_mercredi'));
                $groupe->i_jeudi = $time->StringToTime(\Input::post('i_jeudi'));
                $groupe->i_vendredi = $time->StringToTime(\Input::post('i_vendredi'));
                $groupe->i_samedi = $time->StringToTime(\Input::post('i_samedi'));
                $groupe->i_dimanche = $time->StringToTime(\Input::post('i_dimanche'));


                // On sauvegarde
                if ($groupe->save()) {

                    //on supprime les gestionnaires

                    $gestionnaire = new \Model_Users_Groupe();
                    $gestionnaire->delete_gestionnaire($id);
                    //on les recrées
                    $gestion = \Input::post('gestion');

                    foreach ($gestion as $value) {
                        $new = new \Model_Users_Groupe();
                        $new->users_id = $value;
                        $new->groupe_id = $groupe->id_groupe;
                        $new->save();
                    }

                    $message[] = 'Le groupe a bien été modifié.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_groupes');
                } else {
                    $message[] = 'Impossible de créer le groupe.';
                    $message[] = $val->show_errors();
                    \Response::redirect($this->view_dir . 'form_groupe');

                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $groupe->i_lundi = $time->TimeToString($groupe->i_lundi);
        $groupe->i_mardi = $time->TimeToString($groupe->i_mardi);
        $groupe->i_mercredi = $time->TimeToString($groupe->i_mercredi);
        $groupe->i_jeudi = $time->TimeToString($groupe->i_jeudi);
        $groupe->i_vendredi = $time->TimeToString($groupe->i_vendredi);
        $groupe->i_samedi = $time->TimeToString($groupe->i_samedi);
        $groupe->i_dimanche = $time->TimeToString($groupe->i_dimanche);
        list($nom) = explode('-', $groupe->t_nom);
        $groupe->t_nom = $nom;

        // On récupère tous les types de cedefop
        //$cedefop = \Model_Type_Cedefop::getAsSelect();

        $filieres = \Model_Filiere::find('all');

        foreach ($filieres as $value) {

            $filiere[$value->id_filiere] = $value->t_nom;

        }


        // On récupère la liste des users pour permettre de choisir le responsable
        $users = \Model_User::find('all', array(
            'where' => array(
                array('is_actif', 1),
            )
        ));

        $select_users = array();

        foreach ($users as $value) {
            $select_users[$value->id] = $value->username;
        }

        $gestionnaire = \Model_Users_Groupe::getGestionnaireAffiche($id);


        $gestion_users = array();

        foreach ($gestionnaire as $value) {
            $gestion_users[$value->id][] = $value->username;
            $gestion_users[$value->id][] = $value->coche;
        }

//        //on récupère le nom des centres dans le fichier xml
//        $path = Asset::find_file('coordonnees.xml', 'xml');
//        $xml = simplexml_load_file($path);
//        $select_lieu = array();
//        foreach($xml->centre as $value) {
//            $select_lieu[(string)$value->nom_centre] = (string)$value->nom_centre;
//        }
        /**
         * On récupère les centres dans la db
         */
        $centre = \Model_Localisation::find('all');
        $select_lieu = array();

        foreach ($centre as $value) {
            $select_lieu[$value->id_localisation] = $value->t_lieu;

        }

        $this->data['centre'] = $select_lieu;
        $this->data['users'] = $select_users;
        $this->data['gestionnaire'] = $gestion_users;
        $this->data['groupe'] = $groupe;
        $this->data['filiere'] = $filiere;
        $this->data['action'] = 'Modifier';

        $this->template->content = \View::forge($this->view_dir . 'form_groupe', $this->data);
    }

    /**
     * Supprimer un groupe
     *
     * @param type $item
     */
    public function action_supprimer_groupe($id = NULL)
    {
        if ($groupe = \Model_Groupe::find($id)) {

            //supression des gestionnaires
            $gestionnaire = new \Model_Users_Groupe();

            $gestionnaire->delete_gestionnaire($id);


            //supression du groupe;
            $groupe->delete();

            $message[] = 'Le groupe a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer le groupe.';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_groupes');
//        $this->template->title = 'Administration - Gestion des fins de formation';
//        $this->template->content = \View::forge('test');
    }

    /**
     * Afficher les fins de formation
     */
    public function action_liste_fins_formation()
    {
        $this->template->title = 'Administration - Gestion des fins de formation';

        $types_formation = \Model_Type_Formation::find('all', array('order_by' => array('t_nom' => 'ASC'), 'related' => array('fins_formation' => array('order_by' => array('i_position' => 'ASC')))));

        $this->data['types_formation'] = $types_formation;
        $this->template->content = \View::forge($this->view_dir . 'fins_formation', $this->data);
    }

    /**
     * Ajoute une fin de formation
     */
    public function action_ajouter_fin_formation($id_type = null)
    {
        if (\Input::method() == 'POST') {
            $val = \Model_Fin_Formation::validate('edit');

            if ($val->run()) {
                $position = \Maitrepylos\Db::getMaxPositionStatut('fin_formation', 'type_formation_id', \Input::post('type_formation_id'));
                $formation = \Model_Fin_Formation::forge(array(
                    't_nom' => \Input::post('t_nom'),
                    't_valeur' => \Input::post('t_valeur'),
                    'i_position' => $position[0]['i_position'],
                    'type_formation_id' => \Input::post('type_formation_id'),
                ));

                // On save si c'est bon
                if ($formation and $formation->save()) {
                    $message[] = "La fin de formation a bien été ajoutée.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_fins_formation');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver la fin de formation.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $types = \Model_Type_Formation::getAsSelect();
        $this->data['types'] = $types;
        $this->data['id_type'] = $id_type;
        $this->data['action'] = 'Ajouter';
        $this->template->title = 'Administration - Gestion des fins de formation';
        $this->template->content = \View::forge($this->view_dir . 'form_fin_formation', $this->data);
    }

    /**
     * Modifie une fin de formation
     */
    public function action_modifier_fin_formation($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'fins_formation');

        $this->template->title = 'Administration - Gestion des fins de formation';

        $formation = \Model_Fin_Formation::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Fin_Formation::validate('edit');

            if ($val->run()) {
                $formation->t_nom = \Input::post('t_nom');
                $formation->t_valeur = \Input::post('t_valeur');
                $formation->type_formation_id = \Input::post('type_formation_id');

                // On save si c'est bon
                if ($formation and $formation->save()) {
                    $message[] = "La fin de formation a bien été mise à jour.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_fins_formation');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver la fin de formation.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $types = \Model_Type_Formation::getAsSelect();
        $this->data['types'] = $types;
        $this->data['action'] = 'Modifier';
        $this->data['formation'] = $formation;
        $this->template->content = \View::forge($this->view_dir . 'form_fin_formation', $this->data);
    }

    /**
     * Supprimer une fin de formation
     *
     * @param int $id
     */
    public function action_supprimer_fin_formation($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'fins_formation');

        if ($formation = \Model_Fin_Formation::find($id)) {
            $formation->delete();

            $message[] = 'La fin de formation a bien été supprimée.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer la fin de formation';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_fins_formation');
    }

    /**
     * Afficher le photogramme
     */
    public function action_photogramme_xml()
    {
        $this->template->title = 'Administration des items de fin de formation';

        $photogramme = \Cranberry\MyXML::getXML('photogramme');

        $this->data['photogramme'] = $photogramme;
        $this->template->content = \View::forge($this->view_dir . 'photogramme', $this->data);
    }

    /**
     * Modifier le photogramme item par item.
     *
     * @param type $item
     */
    public function action_modifier_photogramme_xml($item)
    {
        if (\Input::method() == 'POST') {
            $val = \Validation::forge();
            $val->add_field('nom', 'Nom', 'required');

            $val->set_message('required', 'Veuillez remplir le champ :label.');

            if ($val->run()) {
                \Cranberry\MyXML::editXMLItem('photogramme', $item, \Input::post('nom'));

                $message[] = "Le photogramme a bien été édité.";
                \Session::set_flash('success', $message);

                \Response::redirect($this->view_dir . 'photogramme_xml');
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        // On récupère le nom de l'item qu'on veut modifier
        $nom = \Cranberry\MyXML::getXMLItem('photogramme', $item);

        $this->data['nom'] = $nom;
        $this->data['item'] = $item;
        $this->template->title = 'Administration des items du photogramme';
        $this->template->content = \View::forge($this->view_dir . 'modifier_photogramme', $this->data);
    }

    /**
     * Affiche la liste des logins/users
     */
    public function action_liste_logins()
    {
        $this->template->title = 'Administration - Gestion des logins';

        // On récupère tous les users actifs (comportement par défaut)
        $users = \Model_User::find('all', array(
            'where' => array(
                array('is_actif', 1),
            )
        ));

        // Récupération des groupes spécifiés dans le fichier /config/simpleauth.php
        $groups = \Config::get('simpleauth');

        $this->data['users'] = $users;
        $this->data['groupes'] = $groups['groups'];
        $this->template->content = \View::forge($this->view_dir . 'logins', $this->data);
    }

    /**
     * Ajouter un login
     */
    public function action_ajouter_login()
    {
        $this->template->title = 'Administration - Gestion des logins';

        if (\Input::method() == 'POST') {
            $val = \Model_User::validate('create');

            $val->set_message('required', 'Veuillez remplir le champ :label.');

            if ($val->run()) {
                $login = \Model_User::forge(array(
                    'username' => \Input::post('username'),
                    'password' => \Auth::instance()->hash_password(\Input::post('password')),
                    'group' => \Input::post('group'),
                    'last_login' => 0,
                    'login_hash' => \Input::post('username'),
                    'profile_fields' => 'a:0:{}',
                    'is_actif' => 1,
                    't_nom' => \Input::post('t_nom'),
                    't_prenom' => \Input::post('t_prenom'),
                    't_acl' => \Input::post('t_acl'),
                ));

                if ($login and $login->save()) {
                    $message[] = 'Le login a bien été créé.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_logins');
                } else {
                    $message[] = 'Impossible de créer le login.';
                    \Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        // Récupération des groupes spécifiés dans le fichier /config/simpleauth.php
        $groups = \Config::get('simpleauth');
        $groups = $groups['groups'];
        $droits = array();
        foreach ($groups as $key => $value) {
            $droits[$key] = $value['name'];
        }

        $this->data['action'] = 'Ajouter';
        $this->data['droits'] = $droits;
        $this->data['reset_password'] = false;
        $this->template->content = \View::forge($this->view_dir . 'form_login', $this->data);
    }

    /**
     * Modifie un login selon son id
     *
     * @param type $id
     */
    public function action_modifier_login($id = NULL)
    {
        $this->template->title = 'Administration - Gestion des logins';

        $user = \Model_User::find($id);

        if (\Input::method() == 'POST') {
            $modif_pass = (bool)\Input::post('required_password');
            $val = \Model_User::validate('edit', $modif_pass);

            if ($val->run()) {
                $user->username = \Input::post('username');
                if ($modif_pass)
                    $user->password = \Auth::instance()->hash_password(\Input::post('password'));
                $user->group = \Input::post('group');
                $user->last_login = 0;
                $user->login_hash = \Input::post('username');
                $user->profile_fields = 'a:0:{}';
                $user->is_actif = 1;
                $user->t_nom = \Input::post('t_nom');
                $user->t_prenom = \Input::post('t_prenom');
                $user->t_acl = \Input::post('t_acl');

                if ($user and $user->save()) {
                    $message[] = 'Le login a bien été modifié.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_logins');
                } else {
                    $message[] = 'Impossible de modifier le login.';
                    Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $groups = \Config::get('simpleauth');
        $groups = $groups['groups'];
        $droits = array();
        foreach ($groups as $key => $value) {
            $droits[$key] = $value['name'];
        }

        $this->data['action'] = 'Modifier';
        $this->data['droits'] = $droits;
        $this->data['user'] = $user;
        $this->data['reset_password'] = true;
        $this->template->content = \View::forge($this->view_dir . 'form_login', $this->data);
    }

    /**
     * Supprime un login
     *
     * @param type $id
     */
    public function action_supprimer_login($id = NULL)
    {
        if ($login = \Model_User::find($id)) {
            // On vérifie si le logine st responsable d'un groupe.
            $isOwner = \Model_Groupe::hasOwner($id);
            if ($isOwner) {
                $message[] = 'Le login est responsable d\'un groupe, il est impossible de le supprimer.';
                \Session::set_flash('error', $message);
            } else {
                // Désactiver le login
                $login->is_actif = 0;
                $login->save();
                $message[] = 'Le login a bien été supprimé.';
                \Session::set_flash('success', $message);
            }
        } else {
            $message[] = 'Impossible de trouver le login.';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_logins');
    }

    /**
     * Affiche la liste des types de contrat (venant de la db)
     */
    public function action_liste_types_contrat()
    {
        if (\Input::method() == 'POST') {
            //$db = new \Model_My_Contrat();
            //$db->updateTypeContrat(\Input::post('table_type_contrat'));

            \Maitrepylos\Db::updatePosition(\Input::post('table_type_contrat'), 'type_contrat', 'id_type_contrat');
        }
        $this->template->title = 'Administration - Gestion des types de contrat';

        $types = \Model_Contrat::getContrat();

        $this->data['types'] = $types;
        $this->template->content = \View::forge($this->view_dir . 'types_contrat', $this->data);
    }

    /**
     * Ajoute un type de contrat dans la db
     */
    public function action_ajouter_type_contrat()
    {
        $this->template->title = 'Administration - Gestion des types de contrat';

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Contrat::validate('create');

            $val->set_message('required', 'Veuillez remplir le champ :label.');
            $val->set_message('bland_hour', 'le champ :label doit être de forme 00:00');
            $val->set_message('more_forty_hours', 'le champs :label ne peut dépasser 40 heures');

            if ($val->run()) {
                $time = new \Maitrepylos\Timetosec();

                $actif = \Input::post('b_type_contrat_actif');
                $paye = \Input::post('i_paye');
                $rw = \Input::post('subside');

                $contrat = \Model_Type_Contrat::forge(array(
                    't_type_contrat' => \Input::post('t_type_contrat'),
                    'b_type_contrat_actif' => isset($actif) ? $actif : 0,
                    'i_heures' => $time->StringToTime(\Input::post('i_heures')),
                    'i_paye' => isset($paye) ? $paye : 0,
                    'subside_id' => isset($rw) ? $rw : 2

                ));

                if ($contrat and $contrat->save()) {
                    $message[] = 'Le type de contrat a bien été créé.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_contrat');
                } else {
                    $message[] = 'Impossible de créer le type de contrat.';
                    Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }
        $subside = \Model_Subside::find('all');
        $select_subside = array();

        foreach ($subside as $value) {
            $select_subside[$value->id_subside] = $value->t_nom;
        }

        $this->data['action'] = 'Ajouter';
        $this->data['subside'] = $select_subside;
        $this->template->content = \View::forge($this->view_dir . 'form_type_contrat', $this->data);
    }

    /**
     * Modifier le type de contrat selon son id
     *
     * @param type $id
     */
    public function action_modifier_type_contrat($id)
    {
        $this->template->title = 'Administration - Gestion des types de contrat';

        $contrat = \Model_Type_Contrat::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Contrat::validate('edit');

            $val->set_message('required', 'Veuillez remplir le champ :label.');
            $val->set_message('bland_hour', 'le champ :label doit être de forme 00:00');
            $val->set_message('more_forty_hours', 'le champs :label ne peut dépasser 40 heures');

            if ($val->run()) {
                $time = new \Maitrepylos\Timetosec();
                $contrat->t_type_contrat = \Input::post('t_type_contrat');
                $contrat->b_type_contrat_actif = \Input::post('b_type_contrat_actif');
                $contrat->i_heures = $time->StringToTime(\Input::post('i_heures'));
                $contrat->i_paye = \Input::post('i_paye');
                $contrat->subside_id = \Input::post('subside_id');

                if ($contrat and $contrat->save()) {
                    $message[] = 'Le type de contrat a bien été modifié.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_contrat');
                } else {
                    $message[] = 'Impossible de modifier le type de contrat.';
                    Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $subside = \Model_Subside::find('all');
        $select_subside = array();

        foreach ($subside as $value) {
            $select_subside[$value->id_subside] = $value->t_nom;
        }

        $this->data['action'] = 'Modifier';
        $this->data['contrat'] = $contrat;
        $this->data['subside'] = $select_subside;
        $this->template->content = \View::forge($this->view_dir . 'form_type_contrat', $this->data);
    }

    /**
     * Supprime un type de contrat selon son id
     *
     * @param type $id
     */
    public function action_supprimer_type_contrat($id = null)
    {
        if ($contrat = \Model_Type_Contrat::find($id)) {

            try {
                $contrat->delete();
                $message[] = 'Le type de contrat a bien été supprimé.';
                \Session::set_flash('success', $message);

            } catch (\Exception $e) {

                $message[] = 'Impossible de supprimer ce type de contrat<br />Il est utilisé par des contrats en cours.';

                \Session::set_flash('error', $message);
                \Response::redirect($this->view_dir . 'liste_types_contrat');

            }

        } else {
            $message[] = 'Impossible de trouver le type de contrat.';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_types_contrat');
    }

    /**
     * Affiche la liste des types de contact (venant de la db)
     */
    public function action_typesContact()
    {
        $this->template->title = 'Administration des types de contact';

        $types = \Model_Type_Contact::find('all');

        $this->template->set_global('types', $types, false);

        $this->template->content = \View::forge('administration/contacts');
    }

    /**
     * Ajoute un type de contact dans la db
     */
    public function action_ajouter_typesContact()
    {
        $this->template->title = 'Administration des types de contact';

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Contact::validate('create');

            $val->set_message('required', 'Veuillez remplir le champ :label.');

            if ($val->run()) {
                $contact = \Model_Type_Contact::forge(array(
                    't_typecontact' => \Input::post('t_typecontact'),
                ));

                if ($contact and $contact->save()) {
                    $message[] = 'Le type de contact a bien été créé.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'typesContact');
                } else {
                    $message[] = 'Impossible de créer le type de contact.';
                    Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->template->content = \View::forge($this->view_dir . 'add_contact');
    }

    /**
     * Modifier le type de contact selon son id
     *
     * @param type $id
     */
    public function action_modifier_typesContact($id)
    {
        $this->template->title = 'Administration des types de contact';

        $contact = \Model_Type_Contact::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Contact::validate('edit');

            $val->set_message('required', 'Veuillez remplir le champ :label.');

            if ($val->run()) {
                $contact->t_typecontact = \Input::post('t_typecontact');

                if ($contact and $contact->save()) {
                    $message[] = 'Le type de contact a bien été modifié.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'typesContact');
                } else {
                    $message[] = 'Impossible de modifier le type de contact.';
                    Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->template->set_global('contact', $contact, false);
        $this->template->content = \View::forge($this->view_dir . 'add_contact');
    }

    /**
     * Supprime un type de contact selon son id
     *
     * @param type $id
     */
    public function action_supprimer_typesContact($id = null)
    {
        if ($contact = \Model_Type_Contact::find($id)) {
            $contact->delete();
            $message[] = 'Le type de contact a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de trouver le type de contact.';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'typesContact');
    }

    /**
     * Affiche le menu du l ien "administration des heures"
     */
    public function action_heures()
    {
        $this->template->title = 'Administration des heures';
        $this->template->content = \View::forge($this->view_dir . 'menu_heures');
        $this->template->content->view_dir = $this->view_dir;
    }

    /**
     * Affichage du menu de l'administration
     */
    public function action_liste_subsides()
    {
        $this->template->title = 'Administration - Gestion des ubsides';

        $this->data['subside'] = \Model_Subside::find('all');
        $this->template->content = \View::forge($this->view_dir . 'subsides', $this->data);
    }

    /**
     * Ajoute un type de subide dans la db
     */
    public function action_ajouter_subside()
    {
        $this->template->title = 'Administration - Gestion des subsides';

        if (\Input::method() == 'POST') {
            $val = \Model_Subside::validate('create');

            $val->set_message('required', 'Veuillez remplir le champ :label.');

            if ($val->run()) {
                $subside = \Model_Subside::forge(array(
                    't_nom' => \Input::post('t_nom'),
                ));

                if ($subside and $subside->save()) {
                    $message[] = 'Le subside a bien été créé.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_subsides');
                } else {
                    $message[] = 'Impossible de créer le subside.';
                    Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['action'] = 'Ajouter';
        $this->template->content = \View::forge($this->view_dir . 'form_subside', $this->data);
    }

    /**
     * Supprime un subside selon son id
     *
     * @param type $id
     */
    public function action_supprimer_subside($id = null)
    {
        if ($subside = \Model_Subside::find($id)) {
            $subside->delete();
            $message[] = 'Le subside a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de trouver le subside';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_subsides');
    }

    /**
     * Modifier le type de contact selon son id
     *
     * @param type $id
     */
    public function action_modifier_subside($id)
    {
        $this->template->title = 'Administration - Gestion des subsides';

        $subside = \Model_Subside::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Subside::validate('edit');

            $val->set_message('required', 'Veuillez remplir le champ :label.');

            if ($val->run()) {
                $subside->t_nom = \Input::post('t_nom');

                if ($subside and $subside->save()) {
                    $message[] = 'Le subside a bien été modifié.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_subsides');
                } else {
                    $message[] = 'Impossible de modifier le subside.';
                    Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['action'] = 'Modifier';
        $this->data['subside'] = $subside;
        $this->template->content = \View::forge($this->view_dir . 'form_subside', $this->data);
    }

    /**
     * Affiche la liste des activités
     */
    public function action_liste_activites()
    {
        if (\Input::method() == 'POST') {
            $db = new \Model_My_Activite();
            $db->updateActivite(\Input::post('table_activite'));

        }

        $this->template->title = 'Administration - Gestion des activités';
        $activites = \Model_Activite::find('all', array('order_by' => array('i_position' => 'asc')));

        $this->data['activites'] = $activites;
        $this->template->content = \View::forge($this->view_dir . 'activites', $this->data);

    }

    /**
     * Ajoute une activité dans le fichier prestations.xml
     */
    public function action_ajouter_activite()
    {
        if (\Input::method() == 'POST') {
            $val = \Model_Activite::validate('create');

            if ($val->run()) {
                // On ajoute l'item dans le xml
                $db = new \Model_My_Activite();
                $db->add_activite(\Input::post('t_nom'), \Input::post('t_schema'));
                \Response::redirect($this->view_dir . 'liste_activites');
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->template->title = 'Administration - Gestion des activités';
        $this->template->content = \View::forge($this->view_dir . 'ajouter_activite', $this->data);
    }

    /**
     * Supprime une activité
     *
     * @param type $item
     */
    public function action_supprimer_activite($item)
    {
        // On supprime l'item
        $db = new \Model_My_Activite();
        $db->del_activite($item);

        \Response::redirect($this->view_dir . 'liste_activites');
    }

    public function action_modifier_activite($id)
    {

        is_null($id) and \Response::redirect($this->view_dir . 'liste_activites');

        $activite = \Model_Activite::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Activite::validate_modify('edit');

            if ($val->run()) {

                $activite->t_nom = \Input::post('t_nom');
                $activite->t_schema = \Input::post('t_schema');


                // On save si c'est bon
                if ($activite and $activite->save()) {
                    $message[] = 'L\'activité a bien été modifié.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_activites');
                } else // sinon on affiche les erreurs
                {

                    $message[] = 'Impossible de modifier l\'activité.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }

        }
        $this->data['activite'] = $activite;
        $this->template->title = 'Administration - Gestion des activités';
        $this->template->content = \View::forge($this->view_dir . 'ajouter_activite', $this->data);

    }

    /**
     * Affiche la page pour choisir un groupe pour les prestations
     */
    public function action_prestations()
    {
        $this->template->title = 'Administration des heures : prestations sur l\'année';

        if (\Input::method() == 'POST') {
            \Response::redirect($this->view_dir . 'liste_prestations/' . \Input::post('groupe'));
        }

        $groupes = \Model_Groupe::find('all');

        $select_groupes = array();

        foreach ($groupes as $value) {
            $select_groupes[$value->id_groupe] = $value->t_nom;
        }
        $this->data['view_dir'] = $this->view_dir;
        $this->template->set_global('groupes', $select_groupes);
        $this->template->content = \View::forge('administration/prestations', $this->data);
    }

    /**
     * Affichage des prestations selon un groupe choisi
     *
     * @param type $idgroupe
     */
    public function action_liste_prestations($idgroupe)
    {
        $this->template->title = 'Administration des heures : prestations sur l\'année';
        /**
         * Récupération des heures de prestations suivant le groupe
         */
        $heures = \Model_Heures_Prestation::find('all', array(
            'where' => array(
                array('groupe_id', $idgroupe),
            ),
            'order_by' => array('annee' => 'asc'),
        ));
        /**
         * Récupération du nom de groupe
         */
        $groupe = \Model_Groupe::find($idgroupe);

        $this->template->set_global('groupe', $idgroupe);
        $this->template->set_global('nomGroupe', $groupe);
        $this->template->set_global('heures', $heures);

        $this->template->content = \View::forge($this->view_dir . 'liste_prestations', $this->data);
    }

    /**
     * Affichage du formulaire pour ajouter des  heures
     */
    public function
    action_ajouter_heures_prestation($idgroupe)
    {
        if (\Input::method() == 'POST') {
            $val = \Model_Heures_Prestation::validate('create', $idgroupe);

            $val->set_message('required', 'Veuillez remplir le champ :label.');
            $val->set_message('annee', 'Veuillez remplir le champ :label.');
            $val->set_message('exact_length', 'Le champ :label doit compter exactement :param:1 caractères.');
            $val->set_message('bland_hour', 'Le champ :label doit-être sous forme 00:00');

            if ($val->run()) {
                $time = new \MaitrePylos\timeToSec();
                $heures = \Model_Heures_Prestation::forge(array(
                    'annee' => \Input::post('annee'),
                    'janvier' => $time->StringToTime(\Input::post('janvier')),
                    'fevrier' => $time->StringToTime(\Input::post('fevrier')),
                    'mars' => $time->StringToTime(\Input::post('mars')),
                    'avril' => $time->StringToTime(\Input::post('avril')),
                    'mai' => $time->StringToTime(\Input::post('mai')),
                    'juin' => $time->StringToTime(\Input::post('juin')),
                    'juillet' => $time->StringToTime(\Input::post('juillet')),
                    'aout' => $time->StringToTime(\Input::post('aout')),
                    'septembre' => $time->StringToTime(\Input::post('septembre')),
                    'octobre' => $time->StringToTime(\Input::post('octobre')),
                    'novembre' => $time->StringToTime(\Input::post('novembre')),
                    'decembre' => $time->StringToTime(\Input::post('decembre')),
                    'jours_janvier' => \Input::post('jours_janvier'),
                    'jours_fevrier' => \Input::post('jours_fevrier'),
                    'jours_mars' => \Input::post('jours_mars'),
                    'jours_avril' => \Input::post('jours_avril'),
                    'jours_mai' => \Input::post('jours_mai'),
                    'jours_juin' => \Input::post('jours_juin'),
                    'jours_juillet' => \Input::post('jours_juillet'),
                    'jours_aout' => \Input::post('jours_aout'),
                    'jours_septembre' => \Input::post('jours_septembre'),
                    'jours_octobre' => \Input::post('jours_octobre'),
                    'jours_novembre' => \Input::post('jours_novembre'),
                    'jours_decembre' => \Input::post('jours_decembre'),
                    'groupe_id' => $idgroupe,
                ));

                if ($heures and $heures->save()) {
                    $message[] = 'Les prestations ont bien été ajoutées.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_prestations/' . $idgroupe);
                } else {
                    $message[] = 'Impossible de créer les prestations.';
                    Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }
        $groupe = \Model_Groupe::find($idgroupe);
        $this->data['id'] = $idgroupe;
        $this->data['nom_groupe'] = $groupe->t_nom;
        $this->data['action'] = 'Ajouter';
        $this->template->title = 'Administration des heures : prestations sur l\'année';
        $this->template->content = \View::forge($this->view_dir . 'add_heures', $this->data);
    }

    /**
     * Affichage du formulaire pour modifier des  heures
     */
    public function action_modifier_heures_prestation($id)
    {
        $heures = \Model_Heures_Prestation::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Heures_Prestation::validate('edit', $id);

            $val->set_message('required', 'Veuillez remplir le champ :label.');
            $val->set_message('exact_length', 'Le champ :label doit compter exactement :param:1 caractères.');
            $val->set_message('bland_hour', 'Le champ :label doit-être sous forme 00:00');

            if ($val->run()) {
                $time = new \MaitrePylos\timeToSec();
                $heures->janvier = $time->StringToTime(\Input::post('janvier'));
                $heures->fevrier = $time->StringToTime(\Input::post('fevrier'));
                $heures->mars = $time->StringToTime(\Input::post('mars'));
                $heures->avril = $time->StringToTime(\Input::post('avril'));
                $heures->mai = $time->StringToTime(\Input::post('mai'));
                $heures->juin = $time->StringToTime(\Input::post('juin'));
                $heures->juillet = $time->StringToTime(\Input::post('juillet'));
                $heures->aout = $time->StringToTime(\Input::post('aout'));
                $heures->septembre = $time->StringToTime(\Input::post('septembre'));
                $heures->octobre = $time->StringToTime(\Input::post('octobre'));
                $heures->novembre = $time->StringToTime(\Input::post('novembre'));
                $heures->decembre = $time->StringToTime(\Input::post('decembre'));
                $heures->jours_janvier = \Input::post('jours_janvier');
                $heures->jours_fevrier = \Input::post('jours_fevrier');
                $heures->jours_mars = \Input::post('jours_mars');
                $heures->jours_avril = \Input::post('jours_avril');
                $heures->jours_mai = \Input::post('jours_mai');
                $heures->jours_juin = \Input::post('jours_juin');
                $heures->jours_juillet = \Input::post('jours_juillet');
                $heures->jours_aout = \Input::post('jours_aout');
                $heures->jours_septembre = \Input::post('jours_septembre');
                $heures->jours_octobre = \Input::post('jours_octobre');
                $heures->jours_novembre = \Input::post('jours_novembre');
                $heures->jours_decembre = \Input::post('jours_decembre');

                if ($heures and $heures->save()) {
                    $message[] = 'Les heures ont bien été modifiées.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_prestations/' . $heures->groupe_id);
                } else {
                    $message[] = 'Impossible de modifier les heures.';
                    Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $groupe = \Model_Groupe::find($heures->groupe_id);

        $this->data['nom_groupe'] = $groupe->t_nom;
        $this->data['heures'] = $heures;
        $this->data['id'] = $heures->groupe_id;
        $this->data['action'] = 'Modifier';
        $this->template->title = 'Administration des heures : prestations sur l\'année';
        $this->template->content = \View::forge($this->view_dir . 'add_heures', $this->data);
    }

    /**
     * Suppression d'une ligne dans la table heures_prestations
     * Meme action pour le field Heures et le field Jours
     *
     * @param type $id
     */
    public function action_supprimer_heures_prestation($id)
    {
        if ($heures = \Model_Heures_Prestation::find($id)) {
            $heures->delete();
            $message[] = 'La prestation a bien été supprimée.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de trouver la prestation.';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_prestations/' . $heures->groupe_id);
    }

    /**
     * Affichage du formulaire pour ajouter des  jours
     */
    public function action_ajouter_jours_prestation($idgroupe)
    {
        if (\Input::method() == 'POST') {
            $val = \Model_Heures_Prestation::validate_jours('create', $idgroupe);

            $val->set_message('required', 'Veuillez remplir le champ :label.');
            $val->set_message('exact_length', 'Le champ :label doit compter exactement :param:1 caractères.');
            $val->set_message('valid_string', 'Le champ :label ne doit contenir que des chiffres.');

            if ($val->run()) {
                $jours = \Model_Heures_Prestation::forge(array(
                    'annee' => \Input::post('annee'),
                    'janvier' => 0,
                    'fevrier' => 0,
                    'mars' => 0,
                    'avril' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juillet' => 0,
                    'aout' => 0,
                    'septembre' => 0,
                    'octobre' => 0,
                    'novembre' => 0,
                    'decembre' => 0,
                    'jours_janvier' => \Input::post('jours_janvier'),
                    'jours_fevrier' => \Input::post('jours_fevrier'),
                    'jours_mars' => \Input::post('jours_mars'),
                    'jours_avril' => \Input::post('jours_avril'),
                    'jours_mai' => \Input::post('jours_mai'),
                    'jours_juin' => \Input::post('jours_juin'),
                    'jours_juillet' => \Input::post('jours_juillet'),
                    'jours_aout' => \Input::post('jours_aout'),
                    'jours_septembre' => \Input::post('jours_septembre'),
                    'jours_octobre' => \Input::post('jours_octobre'),
                    'jours_novembre' => \Input::post('jours_novembre'),
                    'jours_decembre' => \Input::post('jours_decembre'),
                    'groupe_id' => $idgroupe,
                ));

                if ($jours and $jours->save()) {
                    $message[] = 'Les jours ont bien été ajoutés.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_prestations/' . $idgroupe);
                } else {
                    $message[] = 'Impossible de créer les jours.';
                    Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }
        $groupe = \Model_Groupe::find($idgroupe);

        $this->data['nom_groupe'] = $groupe->t_nom;
        $this->data['id'] = $idgroupe;
        $this->data['view_dir'] = $this->view_dir;
        $this->template->title = 'Administration des heures : prestations sur l\'année';
        $this->template->content = \View::forge($this->view_dir . 'add_jours', $this->data);
    }

    /**
     * Affichage du formulaire pour modifier des  jours
     *
     * @param type $idgroupe
     */
    public function action_modifier_jours_prestation($id)
    {
        $jours = \Model_Heures_Prestation::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Heures_Prestation::validate_jours('edit');

            $val->set_message('required', 'Veuillez remplir le champ :label.');
            $val->set_message('exact_length', 'Le champ :label doit compter exactement :param:1 caractères.');
            $val->set_message('valid_string', 'Le champ :label ne doit contenir que des chiffres.');

            if ($val->run()) {

                $jours->jours_janvier = \Input::post('jours_janvier');
                $jours->jours_fevrier = \Input::post('jours_fevrier');
                $jours->jours_mars = \Input::post('jours_mars');
                $jours->jours_avril = \Input::post('jours_avril');
                $jours->jours_mai = \Input::post('jours_mai');
                $jours->jours_juin = \Input::post('jours_juin');
                $jours->jours_juillet = \Input::post('jours_juillet');
                $jours->jours_aout = \Input::post('jours_aout');
                $jours->jours_septembre = \Input::post('jours_septembre');
                $jours->jours_octobre = \Input::post('jours_octobre');
                $jours->jours_novembre = \Input::post('jours_novembre');
                $jours->jours_decembre = \Input::post('jours_decembre');

                if ($jours and $jours->save()) {
                    $message[] = 'Les jours ont bien été modifiés.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_prestations/' . $jours->groupe_id);
                } else {
                    $message[] = 'Impossible de modifier les jours.';
                    Session::set_flash('error', $message);
                }
            } else {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }
        $groupe = \Model_Groupe::find($jours->groupe_id);

        $this->template->set_global('nom_groupe', $groupe->t_nom);
        $this->template->set_global('jours', $jours);
        $this->data['id'] = $id;
        $this->template->title = 'Administration des heures : prestations sur l\'année';
        $this->template->content = \View::forge($this->view_dir . 'add_jours');
    }

    /**
     * Affiche les types de cedefop en les récupérant depuis la db.
     */
    public function action_liste_types_cedefop()
    {
        $this->template->title = 'Administration - Gestion des types de Cedefop';

        $types = \Model_Type_Cedefop::find('all', array('order_by' => array('i_position' => 'asc')));

        $this->data['types'] = $types;
        $this->template->content = \View::forge($this->view_dir . 'types_cedefop', $this->data);
    }

    /**
     * Permet d'ajouter un type de cedefop
     */
    public function action_ajouter_type_cedefop()
    {
        if (\Input::method() == 'POST') {
            $val = \Model_Type_Cedefop::validate('create');
            $position = \Maitrepylos\Db::getMaxPosition('type_cedefop');

            if ($val->run()) {
                $type_cedefop = \Model_Type_Cedefop::forge(array(
                    't_nom' => \Input::post('t_nom'),
                    'i_code' => \Input::post('i_code'),
                    'i_position' => $position[0]['i_position'],
                ));

                if ($type_cedefop and $type_cedefop->save()) {
                    $message[] = 'Le cedefop a bien été ajouté.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_cedefop');
                } else {
                    $message[] = $val->show_errors();
                    \Session::set_flash('error', $message);
                }
            } else {
                \Session::set_flash('error', $val->error());
            }
        }

        $this->data['action'] = 'Ajouter';
        $this->template->title = 'Administration - Gestion des types de Cedefop';
        $this->template->content = \View::forge($this->view_dir . 'form_type_cedefop', $this->data);
    }

    /**
     * Permet de modifier un type de cedefop
     * @param int $id
     */
    public function action_modifier_type_cedefop($id = null)
    {
        is_null($id) and \Response::redirect('liste_types_cedefop');

        if (!$type_cedefop = \Model_Type_Cedefop::find($id)) {
            $message[] = 'Impossible de trouver le type';
            \Session::set_flash('error', $message);
            Response::redirect('liste_types_cedefop');
        }

        if (Input::method() == 'POST') {
            $val = \Model_Type_Cedefop::validate('edit');


            if ($val->run()) {
                $type_cedefop->t_nom = \Input::post('t_nom');
                $type_cedefop->i_code = \Input::post('i_code');


                if ($type_cedefop->save()) {
                    $message[] = 'Cedefop mis à jour';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_cedefop');
                } else {
                    $message[] = 'Impossible de mettre le cedefop à jour';
                    \Session::set_flash('error', $message);
                }
            }
        }

        $this->data['action'] = 'Modifier';
        $this->data['type_cedefop'] = $type_cedefop;
        $this->template->title = 'Administration - Gestion des types de Cedefop';
        $this->template->content = \View::forge($this->view_dir . 'form_type_cedefop', $this->data);
    }

    /**
     * Supprime un type de cedefop
     * @param int $id
     */
    public function action_supprimer_type_cedefop($id = null)
    {
        is_null($id) and \Response::redirect('liste_types_cedefop');
        $type_cedefop = \Model_Type_Cedefop::find($id);
        /**
         * @Remarque pour respecter les foreigns key on doit faire la vérification que le codecedefop n'est pas utiliser
         * il faut suivre le framework pour faire la getion d'erreur, qui actuellemnt n'est pas clair au niveau de l'ORM
         */
        $filiere = \Model_Filiere::query()->where('i_code_cedefop', '=', $type_cedefop->i_code);

        if ($filiere->count() != 0) {


            $message[] = 'Impossible de supprimer le cedefop, car celui-ci est utilisé par au moins une filière';
            \Session::set_flash('error', $message);

        } else {

            $type_cedefop->delete();
            $message[] = 'Cedefop supprimé';
            \Session::set_flash('success', $message);


        }


        \Response::redirect($this->view_dir . 'liste_types_cedefop');
    }

    /**
     * Affiche les types de formation
     */
    public function action_liste_types_formation()
    {
        $this->template->title = 'Administration - Gestion des types de formation';

        $types = \Model_Type_Formation::find('all');

        $this->data['types'] = $types;
        $this->template->content = \View::forge($this->view_dir . 'types_formation', $this->data);
    }

    /**
     * Modifie un type de formation
     */
    public function action_modifier_type_formation($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_types_formation');

        $this->template->title = 'Administration - Gestion des types de formation';

        $type = \Model_Type_Formation::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Formation::validate('edit');

            if ($val->run()) {
                $type->t_nom = \Input::post('t_nom');

                // On save si c'est bon
                if ($type and $type->save()) {
                    $message[] = "Le type de formation a bien été mis à jour.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_formation');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le type de formation.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['action'] = 'Modifier';
        $this->data['type'] = $type;
        $this->template->content = \View::forge($this->view_dir . 'form_type_formation', $this->data);
    }

    /**
     * Ajoute un type de formation
     */
    public function action_ajouter_type_formation()
    {
        $this->template->title = 'Administration des types de formation';

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Formation::validate('edit');

            if ($val->run()) {
                $type = \Model_Type_Formation::forge(array(
                    't_nom' => \Input::post('t_nom'),
                ));

                // On save si c'est bon
                if ($type and $type->save()) {
                    $message[] = "Le type de formation a bien été ajouté.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_formation');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le type de formation.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }

            \Response::redirect($this->view_dir . 'liste_types_formation');
        }

        $this->data['action'] = 'Ajouter';
        $this->template->content = \View::forge($this->view_dir . 'form_type_formation', $this->data);
    }

    /**
     * Supprime un type de formation
     *
     * @param int $id
     */
    public function action_supprimer_type_formation($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_types_formation');

        if ($type = \Model_Type_Formation::find($id)) {
            $type->delete();

            $message[] = 'Le type de formation a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer le type de formation';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_types_formation');
    }

    public function action_liste_types_connaissance()
    {
        $this->template->title = 'Administration - Gestion des types de connaissance de l\'Eft';

        $types = \Model_Type_Connaissance::find('all');

        $this->data['types'] = $types;
        $this->template->content = \View::forge($this->view_dir . 'types_connaisssance', $this->data);
    }

    public function action_ajouter_type_connaissance()
    {
        $this->template->title = 'Administration des types de connaissance';

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Statut::validate('edit');

            if ($val->run()) {
                $type = new \Model_Type_Connaissance();
                $type->add_activite(\Input::post('t_nom'));

                    $message[] = "Le type de statut a bien été ajouté.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_connaissance');

            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['action'] = 'Ajouter';
        $this->template->content = \View::forge($this->view_dir . 'form_type_connaissance', $this->data);
    }

    public function action_supprimer_type_connaissance($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_types_connaissance');

        if ($type = \Model_Type_Connaissance::find($id)) {
            $type->delete();

            $message[] = 'Le type de connaissance eft a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer le type de connaissance eft';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_types_connaissance');
    }

    public function action_modifier_type_connaissance($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_types_connaissance');

        $this->template->title = 'Administration - Gestion des types de connaissance';

        $type = \Model_Type_Connaissance::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Connaissance::validate('edit');

            if ($val->run()) {
                $type->t_nom = \Input::post('t_nom');

                // On save si c'est bon
                if ($type and $type->save()) {
                    $message[] = "Le type de connaissance a bien été mis à jour.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_connaissance');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le type de connaissance.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['action'] = 'Modifier';
        $this->data['type'] = $type;
        $this->template->content = \View::forge($this->view_dir . 'form_type_connaissance', $this->data);
    }

    public function action_liste_types_ressource()
    {
        $this->template->title = 'Administration - Gestion des types de ressource';

        $types = \Model_Type_Ressource::find('all');

        $this->data['types'] = $types;
        $this->template->content = \View::forge($this->view_dir . 'types_ressource', $this->data);
    }

    public function action_ajouter_type_ressource()
    {
        $this->template->title = 'Administration des types de ressource';

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Statut::validate('edit');

            if ($val->run()) {
                $type = new \Model_Type_Ressource();
                // On save si c'est bon
                $type->add_activite(\Input::post('t_nom'));
                $message[] = "Le type de ressource a bien été ajouté.";
                \Session::set_flash('success', $message);
                \Response::redirect($this->view_dir . 'liste_types_ressource');

            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['action'] = 'Ajouter';
        $this->template->content = \View::forge($this->view_dir . 'form_type_ressource', $this->data);
    }

    public function action_supprimer_type_ressource($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_types_ressource');

        if ($type = \Model_Type_Ressource::find($id)) {
            $type->delete();

            $message[] = 'Le type de ressource a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer le type de ressource';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_types_ressource');
    }

    public function action_modifier_type_ressource($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_types_ressource');

        $this->template->title = 'Administration - Gestion des types de ressource';

        $type = \Model_Type_Ressource::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Ressource::validate('edit');

            if ($val->run()) {
                $type->t_nom = \Input::post('t_nom');

                // On save si c'est bon
                if ($type and $type->save()) {
                    $message[] = "Le type de ressource a bien été mis à jour.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_ressource');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le type de ressource.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['action'] = 'Modifier';
        $this->data['type'] = $type;
        $this->template->content = \View::forge($this->view_dir . 'form_type_ressource', $this->data);
    }

    public function action_liste_types_statut()
    {
        $this->template->title = 'Administration - Gestion des types de statut';

        $types = \Model_Type_Statut::find('all');

        $this->data['types'] = $types;
        $this->template->content = \View::forge($this->view_dir . 'types_statut', $this->data);
    }

    public function action_modifier_type_statut($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_types_statut');

        $this->template->title = 'Administration - Gestion des types de statut';

        $type = \Model_Type_Statut::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Statut::validate('edit');

            if ($val->run()) {
                $type->t_nom = \Input::post('t_nom');

                // On save si c'est bon
                if ($type and $type->save()) {
                    $message[] = "Le type de statut a bien été mis à jour.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_statut');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le type de statut.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['action'] = 'Modifier';
        $this->data['type'] = $type;
        $this->template->content = \View::forge($this->view_dir . 'form_type_statut', $this->data);
    }

    public function action_ajouter_type_statut()
    {
        $this->template->title = 'Administration des types de statut';

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Statut::validate('edit');

            if ($val->run()) {
                $type = \Model_Type_Statut::forge(array(
                    't_nom' => \Input::post('t_nom'),
                ));

                // On save si c'est bon
                if ($type and $type->save()) {
                    $message[] = "Le type de statut a bien été ajouté.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_statut');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le type de statut.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['action'] = 'Ajouter';
        $this->template->content = \View::forge($this->view_dir . 'form_type_statut', $this->data);
    }

    public function action_supprimer_type_statut($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_types_statut');

        if ($type = \Model_Type_Statut::find($id)) {
            $type->delete();

            $message[] = 'Le type de statut a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer le type de statut';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_types_statut');
    }

    public function action_liste_statuts_entree()
    {
        $this->template->title = 'Administration - Gestion des statuts à l\'entrée';

        $types_statut = \Model_Type_Statut::find('all', array('order_by' => array('t_nom' => 'ASC'), 'related' => array('statuts_entree' => array('order_by' => array('i_position' => 'ASC')))));

        $this->data['types_statut_entree'] = $types_statut;
        $this->template->content = \View::forge($this->view_dir . 'statuts_entree', $this->data);
    }

    public function action_ajouter_statut_entree($id_type = null)
    {
        if (\Input::method() == 'POST') {
            $val = \Model_Statut_Entree::validate('edit');

            if ($val->run()) {
                $position = \Maitrepylos\Db::getMaxPosition('statut_entree', 'type_statut_id', $id_type);
                $statut = \Model_Statut_Entree::forge(array(
                    't_nom' => \Input::post('t_nom'),
                    't_valeur' => \Input::post('t_valeur'),
                    'i_position' => $position[0]['i_position'],
                    'type_statut_id' => \Input::post('type_statut_id'),
                ));

                // On save si c'est bon
                if ($statut and $statut->save()) {
                    $message[] = "Le statut a bien été ajouté.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_statuts_entree');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le statut.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $types = \Model_Type_Statut::getAsSelect();
        $this->data['types'] = $types;
        $this->data['id_type'] = $id_type;
        $this->data['action'] = 'Ajouter';
        $this->template->title = 'Administration - Gestion des statuts à l\'entrée';
        $this->template->content = \View::forge($this->view_dir . 'form_statut_entree', $this->data);
    }

    public function action_modifier_statut_entree($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_statuts_entree');

        $this->template->title = 'Administration - Gestion des statuts à l\'entrée';

        $statut = \Model_Statut_Entree::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Statut_Entree::validate('edit');

            if ($val->run()) {
                $statut->t_nom = \Input::post('t_nom');
                $statut->t_valeur = \Input::post('t_valeur');
                $statut->type_statut_id = \Input::post('type_statut_id');

                // On save si c'est bon
                if ($statut and $statut->save()) {
                    $message[] = "Le statut a bien été mis à jour.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_statuts_entree');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le statut.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $types = \Model_Type_Statut::getAsSelect();
        $this->data['types'] = $types;
        $this->data['action'] = 'Modifier';
        $this->data['statut'] = $statut;
        $this->template->content = \View::forge($this->view_dir . 'form_statut_entree', $this->data);
    }

    public function action_supprimer_statut_entree($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_statuts_entree');

        if ($statut = \Model_Statut_Entree::find($id)) {
            $statut->delete();

            $message[] = 'Le statut a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer le statut';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_statuts_entree');
    }

    public function action_liste_types_enseignement()
    {
        $this->template->title = 'Administration - Gestion des types d\'enseignement';

        $types = \Model_Type_Enseignement::find('all');

        $this->data['types'] = $types;
        $this->template->content = \View::forge($this->view_dir . 'types_enseignement', $this->data);
    }

    public function action_modifier_type_enseignement($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_types_enseignement');

        $this->template->title = 'Administration - Gestion des types d\'enseignement';

        $type = \Model_Type_Enseignement::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Enseignement::validate('edit');

            if ($val->run()) {
                $type->t_nom = \Input::post('t_nom');

                // On save si c'est bon
                if ($type and $type->save()) {
                    $message[] = 'Le type d\'enseignement a bien été mis à jour.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_enseignement');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le type d\'enseignement.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['action'] = 'Modifier';
        $this->data['type'] = $type;
        $this->template->content = \View::forge($this->view_dir . 'form_type_enseignement', $this->data);
    }

    public function action_ajouter_type_enseignement()
    {
        $this->template->title = 'Administration des types d\'enseignement';

        if (\Input::method() == 'POST') {
            $val = \Model_Type_Enseignement::validate('edit');

            if ($val->run()) {
                $type = \Model_Type_Enseignement::forge(array(
                    't_nom' => \Input::post('t_nom'),
                ));

                // On save si c'est bon
                if ($type and $type->save()) {
                    $message[] = 'Le type d\'enseignement a bien été ajouté.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_types_enseignement');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver le type d\'enseignement.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['action'] = 'Ajouter';
        $this->template->content = \View::forge($this->view_dir . 'form_type_enseignement', $this->data);
    }

    public function action_supprimer_type_enseignement($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_types_enseignement');

        if ($type = \Model_Type_Enseignement::find($id)) {
            $type->delete();

            $message[] = 'Le type d\'enseignement a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer le type d\'enseignement';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_types_enseignement');
    }

    public function action_liste_enseignements()
    {
        $this->template->title = 'Administration - Gestion des enseignements';

        $types_enseignement = \Model_Type_Enseignement::find('all', array('order_by' => array('t_nom' => 'ASC'), 'related' => array('enseignements' => array('order_by' => array('i_position' => 'ASC')))));

        $this->data['types_enseignement'] = $types_enseignement;
        $this->template->content = \View::forge($this->view_dir . 'enseignements', $this->data);
    }

    public function action_ajouter_enseignement($id_type = null)
    {
        if (\Input::method() == 'POST') {
            $val = \Model_Enseignement::validate('edit');

            if ($val->run()) {
                $position = \Maitrepylos\Db::getMaxPositionStatut('enseignement', 'type_enseignement_id', $id_type);
                $enseignement = \Model_Enseignement::forge(array(
                    't_nom' => \Input::post('t_nom'),
                    't_valeur' => \Input::post('t_valeur'),
                    'i_position' => $position[0]['i_position'],
                    'type_enseignement_id' => \Input::post('type_enseignement_id'),
                ));

                // On save si c'est bon
                if ($enseignement and $enseignement->save()) {
                    $message[] = "Le statut a bien été ajouté.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_enseignements');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver l\'enseignement.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $types = \Model_Type_Enseignement::getAsSelect();
        $this->data['types'] = $types;
        $this->data['id_type'] = $id_type;
        $this->data['action'] = 'Ajouter';
        $this->template->title = 'Administration - Gestion des enseignements';
        $this->template->content = \View::forge($this->view_dir . 'form_enseignement', $this->data);
    }

    public function action_modifier_enseignement($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_enseignements');

        $this->template->title = 'Administration - Gestion des enseignements';

        $enseignement = \Model_Enseignement::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Enseignement::validate('edit');

            if ($val->run()) {
                $enseignement->t_nom = \Input::post('t_nom');
                $enseignement->t_valeur = \Input::post('t_valeur');
                $enseignement->type_enseignement_id = \Input::post('type_enseignement_id');

                // On save si c'est bon
                if ($enseignement and $enseignement->save()) {
                    $message[] = "L'enseignement a bien été mis à jour.";
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_enseignements');
                } else // sinon on affiche les erreurs
                {
                    $message[] = 'Impossible de sauver l\'enseignement.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $types = \Model_Type_Enseignement::getAsSelect();
        $this->data['types'] = $types;
        $this->data['action'] = 'Modifier';
        $this->data['enseignement'] = $enseignement;
        $this->template->content = \View::forge($this->view_dir . 'form_enseignement', $this->data);
    }

    public function action_supprimer_enseignement($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_enseignements');

        if ($enseignement = \Model_Enseignement::find($id)) {
            $enseignement->delete();

            $message[] = 'Le statut a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer l\'enseignement';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_enseignements');
    }

    public function action_liste_agrement()
    {
        $this->template->title = 'Gestion des agréments';

        $agrement = \Model_Agrement::find('all', array('related' => array('centres', 'users')));
        $this->data['agrement'] = $agrement;
        $this->template->content = \View::forge($this->view_dir . 'liste_agrement', $this->data);
    }

    public function action_modifier_agrement($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_agrement');
        $agrement = \Model_Agrement::find($id, array('related' => array('centres', 'users')));

        if (\Input::method() == 'POST') {
            $val = \Model_Agrement::validate('edit');

            if ($val->run()) {


                $agrement->t_agrement = \Input::post('t_agrement');
                $agrement->t_origine_agrement = \Input::post('t_origine_agrement');
                $agrement->centre_id = \Input::post('centre_id');
                $agrement->users_id = \Input::post('users_id');


                // On save si c'est bon
                if ($agrement and $agrement->save()) {
                    $message[] = 'L\'agrémént a bien été modifié.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_agrement');
                } else // sinon on affiche les erreurs
                {

                    $message[] = 'Impossible de modifier l\'agrémentt.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $centre = \Model_Centre::get_centre();
        $users = \Model_User::get_users();

        foreach ($users->as_array() as $value) {
            $user[$value['id']] = $value['username'];
        }

        foreach ($centre->as_array() as $value) {
            $centres[$value['id_centre']] = $value['t_nom_centre'];
        }

        $this->data['users'] = $user;
        $this->data['centre'] = $centres;
        $this->data['agrement'] = $agrement;
        $this->data['intitule'] = 'Modification agrément';
        $this->template->title = 'Gestion des agréments';
        $this->template->content = \View::forge($this->view_dir . 'form_agrement', $this->data);


    }

    public function action_ajouter_agrement()
    {

        if (\Input::method() == 'POST') {
            $val = \Model_Agrement::validate('edit');

            if ($val->run()) {

                $agrement = \Model_Agrement::forge(array(
                    't_agrement' => \Input::post('t_agrement'),
                    't_origine_agrement' => \Input::post('t_origine_agrement'),
                    'centre_id' => \Input::post('centre_id'),
                    'users_id' => \Input::post('users_id'),
                ));

                // On save si c'est bon
                if ($agrement and $agrement->save()) {
                    $message[] = 'L\'agrémént a bien été ajouté.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_agrement');
                } else // sinon on affiche les erreurs
                {

                    $message[] = 'Impossible de sauver l\'agrémentt.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $centre = \Model_Centre::get_centre();
        $users = \Model_User::get_users();

        foreach ($users->as_array() as $value) {

            $user[$value['id']] = $value['username'];
        }

        foreach ($centre->as_array() as $value) {
            $centres[$value['id_centre']] = $value['t_nom_centre'];
        }

        $this->data['users'] = $user;
        $this->data['centre'] = $centres;
        $this->data['intitule'] = 'Insertion agrément';

        $this->template->title = 'Gestion des agréments';

        $this->template->content = \View::forge($this->view_dir . 'form_agrement', $this->data);


    }

    public function action_supprimer_agrement($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_agrement');

        $agrement = \Model_Agrement::find($id);
        try {
            $agrement->delete();

            $message[] = 'L\'agrémént a bien été supprimé.';
            \Session::set_flash('success', $message);
        } catch (\Exception $e) {
            $message[] = 'Impossible de supprimer un agrément utilisé !';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_agrement');
    }

    public function action_liste_filiere()
    {

        $filiere = \Model_Filiere::find('all');

        $this->data['filiere'] = $filiere;
        $this->template->title = 'Listes des filières';
        $this->template->content = \View::forge($this->view_dir . 'liste_filiere', $this->data);


    }

    public function action_ajouter_filiere()
    {

        if (\Input::method() == 'POST') {
            $val = \Model_Filiere::validate('edit');

            if ($val->run()) {

                $filiere = \Model_Filiere::forge(array(
                    't_nom' => \Input::post('t_nom'),
                    't_code_forem' => \Input::post('t_code_forem'),
                    'i_code_cedefop' => \Input::post('i_code_cedefop'),
                    'agrement_id' => \Input::post('agrement_id'),
                ));

                // On save si c'est bon
                if ($filiere and $filiere->save()) {
                    $message[] = 'La filière a bien été ajoutée.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_filiere');
                } else // sinon on affiche les erreurs
                {

                    $message[] = 'Impossible de sauver la filière.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $cedefop = \Model_Type_Cedefop::getCedefop();
        $agrement = \Model_Agrement::getAgrement();


        foreach ($cedefop->as_array() as $value) {
            $cedefops[$value['i_code']] = $value['i_code'];
        }
        foreach ($agrement->as_array() as $value) {
            $agrements[$value['id_agrement']] = $value['t_agrement'];
        }

        $this->data['agrement'] = $agrements;
        $this->data['cedefop'] = $cedefops;

        $this->template->title = 'Gestion des filières';
        $this->template->content = \View::forge($this->view_dir . 'form_filiere', $this->data);


    }

    public function action_modifier_filiere($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_filiere');
        $filiere = \Model_Filiere::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Filiere::validate('edit');

            if ($val->run()) {

                $filiere->t_nom = \Input::post('t_nom');
                $filiere->t_code_forem = \Input::post('t_code_forem');
                $filiere->i_code_cedefop = \Input::post('i_code_cedefop');
                $filiere->agrement_id = \Input::post('agrement_id');


                // On save si c'est bon
                if ($filiere and $filiere->save()) {
                    $message[] = 'La filière a bien été modifié.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_filiere');
                } else // sinon on affiche les erreurs
                {

                    $message[] = 'Impossible de sauver la filière.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $cedefop = \Model_Type_Cedefop::getCedefop();
        $agrement = \Model_Agrement::getAgrement();


        foreach ($cedefop->as_array() as $value) {
            $cedefops[$value['i_code']] = $value['i_code'];
        }
        foreach ($agrement->as_array() as $value) {
            $agrements[$value['id_agrement']] = $value['t_agrement'];
        }

        $this->data['agrement'] = $agrements;
        $this->data['cedefop'] = $cedefops;
        $this->data['filiere'] = $filiere;

        $this->template->title = 'Gestion des filières';
        $this->template->content = \View::forge($this->view_dir . 'form_filiere', $this->data);


    }

    public function action_supprimer_filiere($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_filiere');

        if ($agrement = \Model_Filiere::find($id)) {
            try {
                $agrement->delete();
            } catch (\Database_Exception $e) {
                $message[] = 'Désolé, mais vous ne pouvez supprimer cette filière, elle est utilisée dans des groupes et contrats !';
                \Session::set_flash('error', $message);
                \Response::redirect($this->view_dir . 'liste_filiere');
            }

            $message[] = 'La filière a bien été supprimée';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer la filière';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_filiere');
    }

    public function action_liste_localisation()
    {

        $localisation = \Model_Localisation::find('all');

        $this->data['localisation'] = $localisation;
        $this->template->title = 'Listes des localisation';
        $this->template->content = \View::forge($this->view_dir . 'liste_localisation', $this->data);


    }

    public function action_ajouter_localisation()
    {


        if (\Input::method() == 'POST') {
            $val = \Model_Localisation::validate('edit');

            if ($val->run()) {

                $localisation = new \Model_Localisation();

                $localisation->t_lieu = \Input::post('t_lieu');

                $localisation->adresses = new \Model_Adresse();

                $localisation->adresses->t_nom_rue = \Input::post('t_nom_rue');
                $localisation->adresses->t_bte = \Input::post('t_bte');
                $localisation->adresses->t_code_postal = \Input::post('t_code_postal');
                $localisation->adresses->t_commune = \Input::post('t_commune');
                $localisation->adresses->t_telephone = \Input::post('t_telephone');
                $localisation->adresses->t_courrier = 0;


                // On save si c'est bon
                if ($localisation and $localisation->save()) {
                    $message[] = 'La localisation a bien été ajouté.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_localisation');
                } else // sinon on affiche les erreurs
                {

                    $message[] = 'Impossible de sauver la localisation.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }


        $this->template->title = 'Gestion des localisations';
        $this->template->content = \View::forge($this->view_dir . 'form_localisation', $this->data);


    }

    public function action_modifier_localisation($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_filiere');
        $localisation = \Model_Localisation::find($id);

        if (\Input::method() == 'POST') {
            $val = \Model_Localisation::validate('edit');

            if ($val->run()) {

                $localisation->t_lieu = \Input::post('t_lieu');
                $localisation->adresses->t_nom_rue = \Input::post('t_nom_rue');
                $localisation->adresses->t_bte = \Input::post('t_bte');
                $localisation->adresses->t_code_postal = \Input::post('t_code_postal');
                $localisation->adresses->t_commune = \Input::post('t_commune');
                $localisation->adresses->t_telephone = \Input::post('t_telephone');


                // On save si c'est bon
                if ($localisation and $localisation->save()) {
                    $message[] = 'La localisation a bien été modifié.';
                    \Session::set_flash('success', $message);
                    \Response::redirect($this->view_dir . 'liste_localisation');
                } else // sinon on affiche les erreurs
                {

                    $message[] = 'Impossible de modifier la localisation.';
                    \Session::set_flash('error', $message);
                }
            } else // si la validation a échoué
            {
                $message[] = $val->show_errors();
                \Session::set_flash('error', $message);
            }
        }

        $this->data['localisation'] = $localisation;
        $this->template->title = 'Gestion des localisations';
        $this->template->content = \View::forge($this->view_dir . 'form_localisation', $this->data);


    }

    public function action_supprimer_localisation($id)
    {
        is_null($id) and \Response::redirect($this->view_dir . 'liste_localisation');

        if ($localisation = \Model_Localisation::find($id)) {
            $localisation->delete();

            $message[] = 'La localisation a bien été supprimé.';
            \Session::set_flash('success', $message);
        } else {
            $message[] = 'Impossible de supprimer la localisation';
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'liste_localisation');
    }

    public function action_config()
    {

        $path = Asset::find_file('config' . '.xml', 'xml');
        $xml = simplexml_load_file($path);
        $time = \Maitrepylos\Helper::time();
        $xml->mintime = $time->TimeToString($xml->mintime);
        $this->data['config'] = $xml;


        $this->template->title = 'Configuration';
        $this->template->content = \View::forge($this->view_dir . 'form_config', $this->data);

    }

    public function action_modif_config()
    {

        $xml = \Maitrepylos\Config::load('config.xml', 'xml');
        $formData = \Input::post();

        $val = \Validation::forge('config');
        $val->add_callable('\Maitrepylos\Validation');
        $val->add_field('mintime', 'Heures à prester', 'required|bland_hour|no_hour');
        $val->set_message('required', 'Veuillez remplir le champ :label.');
        $val->set_message('bland_hour', 'Le champ :label doit-être de forme 00:00');
        $val->set_message('no_hour', 'Le champ :label ne peut-être 0 .');

        if ($val->run()) {

            \Maitrepylos\Config::setMintime($formData['mintime']);
            $message[] = 'Mise à jour du fichier de configuration';
            \Session::set_flash('success', $message);

        } else {

            $message[] = $val->show_errors();
            \Session::set_flash('error', $message);
        }

        \Response::redirect($this->view_dir . 'config');
    }

    public function action_liste_contrat($id)
    {

        $this->data['contrat'] = \Model_Type_Contrat::getListeContrat($id);
        $this->data['type_contrat'] = \Model_Type_Contrat::find($id);
        $this->template->title = 'Configuration';
        $this->template->content = \View::forge($this->view_dir . 'liste_contrat', $this->data);

    }

}

?>
