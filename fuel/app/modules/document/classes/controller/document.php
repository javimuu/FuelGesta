<?php

namespace Document;

use Fuel\Core\Debug;

/**
 * Controller gérant toute la partie "Participant".
 */
class Controller_Document extends \Controller_Main
{
    public $title = 'Impression';
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
            \Session::set('direction', '/document');
            \Response::redirect('users/login');
        } else if (!\Auth::member(100)) {
            \Response::redirect('users/no_rights'); #7F7F7F
        }
    }


    public function action_index()
    {

        $this->template->title = 'Gestion des impressions';
        $this->template->content = \View::forge('document/index', $this->data);


    }

    /**
     * @see Methode gerant la generation du PDF fiche_paye
     * @method fiche_paye
     * @return file PDF
     */
    public function action_fichepaye($id, $date)
    {
        //$id = \Session::get('id_participant');
        //$date_prestation = \Session::get('date_prestation');
        $datePrestation = \DateTime::createFromFormat('Y-m-d', $date);

        $dbPrestation = new \Model_My_Prestation();


        /**
         * On vérifie que le mois en cours est bien validé
         */
        $valide = $dbPrestation->verifieValide($id, $datePrestation);

        if ($valide[0]['compteur'] < 1) {

            $message[] = 'Vous devez d\abord validez le mois';
            \Session::set_flash('error', $message);
            \Response::redirect('prestation/modifier_participant');

        } else {

            $db = new \Model_My_Document();

            $nomMois = \Maitrepylos\Utils::mois($datePrestation->format('m'));
            $majNomMois = 'jours_' . $nomMois;
            $formData = array();

            /**
             * On récupère les contrats
             */
            $formData = $dbPrestation->getContratFull($id, $datePrestation, $nomMois, $majNomMois);

            $nombresContrat = count($formData);


            for ($i = 0; $i < $nombresContrat; $i++) {

                /**
                 * récupération des informations du participant suivante:
                 * nom
                 * prénom
                 * compte bancaire
                 *
                 */


                $formData[$i]['user'] = $db->getParticipant($id);
                //le mois de la prestation, à enlever plus tard on possède l'info dans date_prestation
                $formData[$i]['mois'] = $datePrestation->format('m');
                //l'année de la prestation à enlever plus tard on possède l'info dans date_prestation
                $formData[$i]['annee'] = $datePrestation->format('Y');
                /**
                 * Nombres de jours de prestations
                 */
                $formData[$i]['nombresPrestations'] = $db->nombresPrestations($formData[$i]['id_contrat'], $datePrestation);

                /**
                 * récupération d'un groupe liés à un contrat
                 *
                 * C'est ici que nous allons dans la suite récupérer plusieurs contrats
                 */

                $formData[$i]['groupe'] = $db->getGroupe($formData[$i]['id_contrat']);

                //récupérations des heures encodée en fonctions de l'année et du mois

                $rows = $db->heuresMois($id, $datePrestation, $formData[$i]['id_contrat']);
                //calcul pour eventuellement faire 2 fiches
                $formData[$i]['count'] = ceil((count($rows)) / 28);

                $size = (int)28 * $formData[$i]['count'];
                $formData[$i]['rows'] = \SplFixedArray::fromArray($rows);
                $formData[$i]['rows']->setSize($size);


                //Recupere le nombre d'heures à effectuer pour un mois donnee
                $formData[$i]['max_heure'] = $db->getHeuresAEffectuer($datePrestation, $id, 1);
                $deplacement[$i]['t_abonnement'] = 0; //cas de l'art 60
                $formData[$i]['deplacement'] = 0;
                $formData[$i]['salaire'] = 0;


//                //Récupère le nombres de jours effectués le mois en cours
//                $formData[$i]['nombres_jours'] = $dbPrestation->get_jours_deplacememnt($id,
//                    $formData[$i]['id_contrat'], $datePrestation);

                $formData[$i]['ajoutDeplacemement'] = $dbPrestation->getAjoutDeplacement($id, $datePrestation);


                $formData[$i]['totalHeuresMois'] = $db->totalHeureMois($id, $datePrestation, $formData[$i]['id_contrat']);

                $formData[$i]['heureRecup'] = $dbPrestation->getHourRecup($id, $datePrestation);


                /**
                 * Calcul des frais de transport.
                 */
                if ($formData[$i]['t_moyen_transport'] != 'TEC') {

                    $formData[$i]['deplacement'] = $formData[$i]['nombresPrestations'] * $formData[$i]['t_abonnement'];


                } else {

                    if ($formData[$i]['jours'] <= $formData[$i]['nombresPrestations']) {

                        $formData[$i]['deplacement'] = $formData[$i]['t_abonnement'];
                    } else {
                        $division = $formData[$i]['t_abonnement'] / $formData[$i]['jours'];
                        $formData[$i]['deplacement'] = round($division * $formData[$i]['nombresPrestations'],2);
                    }

//                    $formData[$i]['deplacement'] = $dbPrestation->getDeplacement($formData[$i]['jours'],
//                       $formData[$i]['nombresPrestations'], $formData[$i]['id_contrat']);

                    // $formData[$i]['deplacement'] = 234;

                }


                //Si le contrat permet de gérer le salaire du stagiaire.
                if ($formData[$i]['i_paye'] == 1) {

                    $totalHeuresMois = $formData[$i]['totalHeuresMois'][0]['fulltime'];
                    //$formData[$i]['salaire'] = $db->salaire($formData[$i]['heures'], $totalHeuresMois, $formData[$i]['f_tarif_horaire']);
                    $formData[$i]['salaire'] = $db->salaire((int)$totalHeuresMois, (int)$formData[$i]['max_heure'], (int)$formData[$i]['f_tarif_horaire']);
                }


                // Calcul les heures d'absences justifier
                $formData[$i]['heure_justifier'] = $db->totalHeureMoisJustifier($id, $datePrestation,
                    $formData[$i]['id_contrat']);
                //Calcul les heures d'absences non justifier
                $formData[$i]['heure_non_justifier'] = $db->totalHeureMoisNonJustifier($id,
                    $datePrestation, $formData[$i]['id_contrat']);
                //Calcul le nombres d'heures effectuer au total de la formation
                $formData[$i]['heure_total_formation'] = $dbPrestation->getHeureTotalFormation($id, $datePrestation);
                //$form_data[$i]['heure_recup'] = $db_prestation->getHourRecup($id, $date_prestation);
            }

          //  \Debug::dump($formData);


              \Maitrepylos\Pdf\Paye::pdf($formData, $nombresContrat);
            $this->template->title = 'Gestion des documents';
            $this->template->content = \View::forge('test');


        }
    }


    /**
     * @see Methode gerant la generation du PDF c98
     * @method c98
     * @return file PDF
     */
    public function action_c98($date, $nom_centre, $groupe = Null, $id = NULL)
    {

        $date_prestation = \DateTime::createFromFormat('Y-m-d', $date);

        $form_data = array();

        $db = new \Model_My_Document();

        $centre = \Model_Centre::find('first');

        if ($id != NULL && $groupe == 0 && $nom_centre == 0) {
            /**
             * On vérifie que l'on dispose d'une adresse !
             */
            if (\Model_Fin_Formation::get_count_adresse($id) == 0) {
                $message[] = 'Le participant à besoin d\'une adresse par défaut.';
                \Session::set_flash('error', $message);
                \Response::redirect('participant/modifier/' . $id);

            }
            $form_data = $db->get_c98_solo($id);

        } elseif ($nom_centre != 0) {

            $form_data = $db->getC98FullLocalisation($nom_centre, $date_prestation);

        } elseif ($groupe != null) {
            $form_data = $db->get_c98_full($groupe, $date_prestation);
        } else {
            $message[] = 'Nous ne disposons pas assez d\'information pour générer le c98.';
            \Session::set_flash('error', $message);
            \Response::redirect('participant/modifier/' . $id);
        }

        if ($form_data == null) {
            $message[] = 'Nous ne disposons pas assez d\'information pour générer le c98.';
            \Session::set_flash('error', $message);
            \Response::redirect('participant/modifier/' . $id);
        }


        \Maitrepylos\Pdf\C98::pdf($form_data, $centre, $date_prestation);
        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('test');


    }


    public function action_l1()
    {
        $db = new \Model_My_Document();
        $formData = \Input::post();

        list ($groupe, $cedefop) = explode(':', $formData['groupe']);
        $formData['cedefop'] = $cedefop;
        $formData['groupe'] = $groupe;

        $formData['xml'] = \Model_Centre::find('first');

        $nom_centre = \SplFixedArray::fromArray(explode('-', $formData['groupe']));
        $nom_centre->setSize(3);

        //compte le nombres de jours entre deux date
        $s = strtotime($formData['date2']) - strtotime($formData['date']);
        $count = intval($s / 86400) + 1;

        $date = \DateTime::createFromFormat('d-m-Y', $formData['date']);


        for ($i = 0; $i < $count; $i++) {
            //$dateFormater = $date->format('Y-m-d');
            $formData['new_date'][$i] = $date->format('d-m-Y');
            $rows = $db->groupe_l1($groupe, $date->format('Y-m-d'));
            $formData['count'][$i] = count($rows);
            $formData['nombre'][$i] = ceil($formData['count'][$i] / 18);

            $size = (int)18 * $formData['nombre'][$i];
            $formData['rows'][$i] = \SplFixedArray::fromArray($rows);
            $formData['rows'][$i]->setSize($size);


            $date->add(new \DateInterval('P1D'));
            //$this->calculeDateEcheance($date, self::AJOUTE_1_JOUR);
        }

        $formData['compteur'] = count($formData['count']);
        if ($formData['doc'] == '1') {
            \Maitrepylos\Pdf\L1::pdf($formData);
        } else {
            \Maitrepylos\Pdf\L1Bis::pdf($formData);
        }


        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('test');


    }

    public function action_l2()
    {


        $db = new \Model_My_Document();
        $formData = \Input::post();

        /**
         * Récupère les informations de filière
         */
        $filiere = \Model_Filiere::find($formData['filiere'], array('related' => array('agrements')));
        $formData['filieres'] = $filiere;


        /**
         * Récupère les informations du centre
         */
        $centre = \DB::select()->from('centre')->where('i_position', 1)->execute();
        $formData['centre'] = $centre->as_array();


        /**
         * On instancie une date
         */
        $date = \DateTime::createFromFormat('Y-m-d', $formData['annee'] . '-' . $formData['mois'] . '-01');
        /**
         * Onrécupère le nombres de participant entre deux dates pour une filière données.
         */
        $id_participant = $db->nombreParticipantEntreDeuxDate($date, $filiere->id_filiere);

        /**
         * S'il n'y a pas de participant, on renvoie une erreur
         */
        if ($id_participant == null) {
            $message[] = 'Nous ne disposons pas de données pour ce L2.';
            \Session::set_flash('error', $message);
            \Response::redirect('/document/formulaire/l2');
        }


        /**
         * On remmane le resultat sur un seul array
         */
        foreach ($id_participant as $value) {
            $participant_id[] = $value['participant_id'];
        }

        /**
         * On calcul le nombres de ? pour la préparation de la requête.
         */
        $place_holders = implode(',', array_fill(0, count($id_participant), '?'));


        $eft = 0;
        $gratuit = 0;
        $payant = 0;
        $stage = 0;
        $assimile = 0;

        /**
         * On boucle sur tout les jours du mois.
         */
        for ($d = 0; $d < $date->format('t'); $d++) {
            $eft_jours = 0;
            $gratuit_jours = 0;
            $payant_jours = 0;
            $stage_jours = 0;
            $assimile_jours = 0;


            /**
             * Ne pas tenir compte des heures de récup : schéma '-'
             */
            /*$db_eft = $db->calculHeuresMoisStatL2($id_participant[$b]['participant_id'], "'+','-'", $date);*/
            $db_eft = $db->calculHeuresMoisStatL2($place_holders, $participant_id, "'+'", $date);
            $eft_jours = $eft_jours + $db_eft[0]['iSum'];
            $eft = $eft + $db_eft[0]['iSum'];

            $db_gratuit = $db->calculHeuresMoisStatL2($place_holders, $participant_id, "'$'", $date);
            $gratuit_jours = $gratuit_jours + $db_gratuit[0]['iSum'];
            $gratuit = $gratuit + $db_gratuit[0]['iSum'];

            $db_payant = $db->calculHeuresMoisStatL2($place_holders, $participant_id, "'@','#'", $date);
            $payant_jours = $payant_jours + $db_payant[0]['iSum'];
            $payant = $payant + $db_payant[0]['iSum'];

            $db_stage = $db->calculHeuresMoisStatL2($place_holders, $participant_id, "'='", $date);
            $stage_jours = $stage_jours + $db_stage[0]['iSum'];
            $stage = $stage + $db_stage[0]['iSum'];

            $db_assimile = $db->calculHeuresMoisStatL2($place_holders, $participant_id, "'/'", $date);
            $assimile_jours = $assimile_jours + $db_assimile[0]['iSum'];
            $assimile = $assimile + $db_assimile[0]['iSum'];

            $data[$d]['heures_date'] = $d + 1;
            $data[$d]['eft'] = $eft_jours;
            $data[$d]['gratuit'] = $gratuit_jours;
            $data[$d]['payant'] = $payant_jours;
            $data[$d]['stage'] = $stage_jours;
            $data[$d]['assimile'] = $assimile_jours;
            $date->add(new \DateInterval('P1D'));
        }
        $formData['jours'] = \SplFixedArray::fromArray($data);
        $formData['jours']->setSize(32);

        $formData['eft'] = $eft;
        $formData['gratuit'] = $gratuit;
        $formData['payant'] = $payant;
        $formData['stage'] = $stage;
        $formData['assimile'] = $assimile;

        \Maitrepylos\Pdf\L2::pdf($formData);


        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('test');

    }

    public function action_signaletique()
    {
        $db = new \Model_My_Document();
        $formData = \Input::post();

        $data = $db->fiche($formData['idparticipant']);


        if ($data == null) {
            $message[] = 'la fiche du participant est imcomplète, en générale il y a un souci avec ses contrats.';
            \Session::set_flash('error', $message);
            \Response::redirect('/document/formulaire/signaletique');
        } else {

            if ($formData['fiche'] == 1) {
                \Maitrepylos\Pdf\Signaletique::pdf($data[0]);
            } else {
                \Maitrepylos\Pdf\Signaletiqued::pdf($data[0]);
            }
        }

        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('test');

    }

    public function action_formation($n)
    {
        $db = new \Model_My_Document();
        $formData = \Input::post();

        $data = $db->fiche($formData['idparticipant']);


        if ($n == 1) {
            $coordonnees = \Cranberry\MyXML::getCoordonnees();
            $nom_centre = \SplFixedArray::fromArray(explode('-', $data[0]['t_nom']));
            $nom_centre->setSize(3);

            foreach ($coordonnees as $centre) {

                if ($centre->nom_centre == $nom_centre[1] || $centre->nom_centre == $nom_centre[2]) {

                    $xml = $centre;
                }
            }
            \Maitrepylos\Pdf\Formation::pdf($data[0], $xml);
        } elseif ($n == 2) {

            \Maitrepylos\Pdf\Deplacement::pdf($data[0]);
        }


        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('test');

    }

    public function action_prestation()
    {
        $db = new \Model_My_Document();
        $formData = \Input::post();
        $groupe = \Model_Groupe::find($formData['groupe']);

        $nom_centre = \SplFixedArray::fromArray(explode('-', $groupe['t_nom']));
        $nom_centre->setSize(3);


        $centre = \Model_Centre::find('first');
        $formData['xml'] = $centre;

        $date = \DateTime::createFromFormat('d-m-Y', $formData['date']);
        $date2 = \DateTime::createFromFormat('d-m-Y', $formData['date2']);
        $id = $db->idEtatPretsation($formData['groupe'], $date, $date2);

        if ($id == null) {

            $message[] = 'Nous ne disposons pas assez d\'information pour générer ce document.';
            \Session::set_flash('error', $message);
            \Response::redirect('document/formulaire/prestation');

        }


        $count_id = count($id);
        $id_participant = '';
        for ($i = 0; $i < $count_id; $i++) {
            if (($i + 1) < $count_id) {
                $id_participant = $id_participant . $id[$i]['participant_id'] . ',';
            } else {
                $id_participant = $id_participant . $id[$i]['participant_id'];
            }
        }

        for ($i = 0; $i < $count_id; $i++) {
            $formData['rows'][$i] = $db->ficheEtatPrestationFormation($formData['groupe'], $date, $date2, $id[$i]['participant_id']);
            $rows = $db->ficheEtatPrestationStage($date, $date2, $id[$i]['participant_id']);
            $formData['rows'][$i][0]['time_partenaire_stage'] = 0;
            $formData['rows'][$i][0]['time_total_stage'] = 0;
            $formData['rows'][$i][0]['compteur_stage'] = 0;
            if ($rows != null) {
                $formData['rows'][$i][0]['time_partenaire_stage'] = $rows[0]['time_partenaire_stage'];
                $formData['rows'][$i][0]['time_total_stage'] = $rows[0]['time_total_stage'];
                $formData['rows'][$i][0]['compteur_stage'] = $rows[0]['compteur_stage'];
            }
            if ($formData['rows'][$i][0]['t_registre_national'] == NULL) {
                $formData['rows'][$i][0]['t_registre_national'] = $rows[0]['t_registre_national'];
            }
        }


        $maladie = $db->ficheEtatPrestationMaladie($formData['groupe'], $date, $date2, $id_participant);
        $count_maladie = count($maladie);
        for ($i = 0; $i < $count_maladie; $i++) {
            $formData['rows'][][] = $maladie[$i];
        }

        $formData['count'] = ceil((count($formData['rows'])) / 11);

        $trie = new \Model_My_Alphabetique();

        $recup = $trie->ordre_alphabetique($formData['rows']);
        $formData['rows'] = NULL;
        $formData['rows'] = \SplFixedArray::fromArray($recup);
        $formData['rows']->setSize($formData['count'] * 11);

   // \Debug::dump($formData);
        \Maitrepylos\Pdf\Etatprestation::pdf($formData);

        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('test');

    }

    public function action_prestationForem()
    {
        $db = new \Model_My_Document();
        $formData = \Input::post();
        $groupe = \Model_Groupe::find($formData['groupe']);

        $nom_centre = \SplFixedArray::fromArray(explode('-', $groupe['t_nom']));
        $nom_centre->setSize(3);


        $centre = \Model_Centre::find('first');
        $formData['xml'] = $centre;

        $date = \DateTime::createFromFormat('d-m-Y', $formData['date']);
        $date2 = \DateTime::createFromFormat('d-m-Y', $formData['date2']);
        $id = $db->idEtatPretsation($formData['groupe'], $date, $date2);

        if ($id == null) {

            $message[] = 'Nous ne disposons pas assez d\'information pour générer ce document.';
            \Session::set_flash('error', $message);
            \Response::redirect('document/formulaire/prestation');

        }


        $count_id = count($id);
        $id_participant = '';
        for ($i = 0; $i < $count_id; $i++) {
            if (($i + 1) < $count_id) {
                $id_participant = $id_participant . $id[$i]['participant_id'] . ',';
            } else {
                $id_participant = $id_participant . $id[$i]['participant_id'];
            }
        }

        for ($i = 0; $i < $count_id; $i++) {
            $formData['rows'][$i] = $db->ficheEtatPrestationFormationForem($formData['groupe'], $date, $date2, $id[$i]['participant_id']);
            $rows = $db->ficheEtatPrestationStageForem($date, $date2, $id[$i]['participant_id']);
            $formData['rows'][$i][0]['time_partenaire_stage'] = 0;
            $formData['rows'][$i][0]['time_total_stage'] = 0;
            $formData['rows'][$i][0]['compteur_stage'] = 0;
            if ($rows != null) {
                $formData['rows'][$i][0]['time_partenaire_stage'] = $rows[0]['time_partenaire_stage'];
                $formData['rows'][$i][0]['time_total_stage'] = $rows[0]['time_total_stage'];
                $formData['rows'][$i][0]['compteur_stage'] = $rows[0]['compteur_stage'];
            }
            if ($formData['rows'][$i][0]['t_registre_national'] == NULL) {
                $formData['rows'][$i][0]['t_registre_national'] = $rows[0]['t_registre_national'];
            }
        }


        $maladie = $db->ficheEtatPrestationMaladieForem($formData['groupe'], $date, $date2, $id_participant);
        $count_maladie = count($maladie);
        for ($i = 0; $i < $count_maladie; $i++) {
            $formData['rows'][][] = $maladie[$i];
        }

        $formData['count'] = ceil((count($formData['rows'])) / 11);

        $trie = new \Model_My_Alphabetique();

        $recup = $trie->ordre_alphabetique($formData['rows']);
        $formData['rows'] = NULL;
        $formData['rows'] = \SplFixedArray::fromArray($recup);
        $formData['rows']->setSize($formData['count'] * 11);

        // \Debug::dump($formData);
        \Maitrepylos\Pdf\Etatprestation::pdf($formData);

        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('test');

    }

    public function action_liste()
    {
        $db = new \Model_My_Document();
        $formData = \Input::post();

        $this->data['liste'] = $db->listeStagiaire($formData['groupe']);
        $this->data['groupe'] = \Model_Groupe::find($formData['groupe']); //$formData['groupe'];
        $count = count($this->data['liste']);
        for ($i = 0; $i < $count; $i++) {
            $contrat = $db->dateContrat($this->data['liste'][$i]['id_contrat']);
            $this->data['liste'][$i]['d_date_debut_contrat'] = $contrat[0]['d_date_debut_contrat'];
            $this->data['liste'][$i]['d_date_fin_contrat_prevu'] = $contrat[0]['d_date_fin_contrat_prevu'];


        }


        //\Maitrepylos\Excel\Listestagiaire::excel($data, $groupe->t_nom);

        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('document/liste', $this->data);
        // $this->template->content = \View::forge('test');


    }

    public function action_liste_excel($id_groupe)
    {
        $db = new \Model_My_Document();

        $data = $db->listeStagiaire($id_groupe);
        $groupe = \Model_Groupe::find($id_groupe);
        $count = count($data);

        for ($i = 0; $i < $count; $i++) {
            $contrat = $db->dateContrat($data[$i]['id_contrat']);
            $data[$i]['d_date_debut_contrat'] = $contrat[0]['d_date_debut_contrat'];
            $data[$i]['d_date_fin_contrat_prevu'] = $contrat[0]['d_date_fin_contrat_prevu'];


        }


        //\Debug::dump($groupe->t_nom);

        \Maitrepylos\Excel\Listestagiaire::excel($data, $groupe->t_nom);

        $this->template->title = 'Gestion des documents';
        //$this->template->content = \View::forge('document/liste',$this->data);
        $this->template->content = \View::forge('test');


    }

    public function action_inscription()
    {

        $formData = \Input::post();
        $centre = \Model_Centre::find('first');

        $annee = $formData['annee'];
        // echo $this->_request->getPost('trimestre');

        switch ($formData['trimestre']) {
            case 1:
                $date[0] = "$annee-01-01";
                $date[1] = "$annee-03-31";
                break;
            case 2:
                $date[0] = "$annee-04-01";
                $date[1] = "$annee-06-30";
                break;
            case 3:
                $date[0] = "$annee-07-01";
                $date[1] = "$annee-09-30";
                break;
            case 4:
                $date[0] = "$annee-10-01";
                $date[1] = "$annee-12-31";
                break;
            default:
                $date[0] = "$annee-01-01";
                $date[1] = "$annee-03-31";
                break;
        }

        $data = \Model_My_Document::getAttestation($date);


        $count = ceil((count($data)) / 14);

        $size = (int)14 * $count;
        $data = \SplFixedArray::fromArray($data);
        $data->setSize($size);
        // \Debug::dump($data->getSize());

        \Maitrepylos\Pdf\Attestation::pdf($data, $centre);
        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('test');


    }


}
