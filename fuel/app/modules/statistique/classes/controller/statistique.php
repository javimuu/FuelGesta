<?php

namespace Statistique;
use Fuel\Core\Debug;

/**
 *Gestion des liens de modification d'un participant.
 */
class Controller_Statistique extends \Controller_Main
{
    public $data = array();

    /**
     * Redirige toute personne non membre du groupe "100"
     */
    public function before()
    {
        parent::before();

        if (!\Auth::member(100)) {
            \Session::set('direction', '/statistique');
            \Response::redirect('users/login');
        }

    }

    public function action_index()
    {


        $this->template->title = 'Gestion des fichier Excel';
        $this->template->content = \View::forge('statistique/index');
    }

    public function action_l3()
    {
        $formData = \Input::post();
        $db = new \Model_My_Statistique();

        //calcul du temps de script
//        $debut_calcul = microtime(true);

        $date = new \DateTime();
        $date->setDate((int)$formData['annee'], 01, 01);

        $groupe = $db->getGroupe($date);

        $count_groupe = count($groupe);

        $data = array();
        $filtre = \Maitrepylos\Helper::time();

        for ($a = 0; $a < $count_groupe; $a++) {
            $date_boucle = clone $date;

            $total_eft = 0;
            $total_gratuit = 0;
            $total_payant = 0;
            $total_stage = 0;
            $total_assimile = 0;

            for ($i = 0; $i < 12; $i++) {
              //  $jours = $date_boucle->format('t');
                $id_participant = $db->getIdParticipant($groupe[$a]['id_groupe'], $date_boucle);

                $count_id_participant = count($id_participant);
                $eft = 0;
                $gratuit = 0;
                $payant = 0;
                $stage = 0;
                $assimile = 0;

                for ($b = 0; $b < $count_id_participant; $b++) {

                 //   $date_jour = clone $date_boucle;

                 //   for ($d = 0; $d < $jours; $d++) {


                        $db_eft = $db->calculHeuresMoisStat($id_participant[$b]['participant_id'], "'+'", $date_boucle);
                        $eft = $eft + $db_eft[0]['iSum'];

                        $db_gratuit = $db->calculHeuresMoisStat($id_participant[$b]['participant_id'], "'$','#'", $date_boucle);
                        $gratuit = $gratuit + $db_gratuit[0]['iSum'];

                        $db_payant = $db->calculHeuresMoisStat($id_participant[$b]['participant_id'], "'@'", $date_boucle);
                        $payant = $payant + $db_payant[0]['iSum'];

                        $db_stage = $db->calculHeuresMoisStat($id_participant[$b]['participant_id'], "'='", $date_boucle);
                        $stage = $stage + $db_stage[0]['iSum'];

                        $db_assimile = $db->calculHeuresMoisStat($id_participant[$b]['participant_id'], "'/'", $date_boucle);
                        $assimile = $assimile + $db_assimile[0]['iSum'];

                 //       $date_jour->add(new \DateInterval('P1D'));
                //    }

                }


                $data[$groupe[$a]['t_nom']][\Maitrepylos\Utils::mois($i + 1)]['eft'] = $filtre->TimeToString($eft);
                $data[$groupe[$a]['t_nom']][\Maitrepylos\Utils::mois($i + 1)]['gratuit'] = $filtre->TimeToString($gratuit);
                $data[$groupe[$a]['t_nom']][\Maitrepylos\Utils::mois($i + 1)]['payant'] = $filtre->TimeToString($payant);
                $data[$groupe[$a]['t_nom']][\Maitrepylos\Utils::mois($i + 1)]['stage'] = $filtre->TimeToString($stage);
                $data[$groupe[$a]['t_nom']][\Maitrepylos\Utils::mois($i + 1)]['assimile'] = $filtre->TimeToString($assimile);

                $total_eft = $total_eft + $eft;
                $total_gratuit = $total_gratuit + $gratuit;
                $total_payant = $total_payant + $payant;
                $total_stage = $total_stage + $stage;
                $total_assimile = $total_assimile + $assimile;

                /**
                 * On ajoute 1 mois à la date pour boucler
                 */
                $date_boucle->add(new \DateInterval('P1M'));


            }
            $data[$groupe[$a]['t_nom']]['totaleft'] = $filtre->TimeToString($total_eft);
            $data[$groupe[$a]['t_nom']]['totalgratuit'] = $filtre->TimeToString($total_gratuit);
            $data[$groupe[$a]['t_nom']]['totalpayant'] = $filtre->TimeToString($total_payant);
            $data[$groupe[$a]['t_nom']]['totalstage'] = $filtre->TimeToString($total_stage);
            $data[$groupe[$a]['t_nom']]['totalassimile'] = $filtre->TimeToString($total_assimile);
            $total_general = $total_eft + $total_gratuit + $total_payant + $total_stage;

            $data[$groupe[$a]['t_nom']]['totalgeneral'] = $filtre->TimeToString($total_general);

        }


        //    $fin_calcul = microtime(true);
        //    $total_temp = ($fin_calcul - $debut_calcul);


        $this->data['nom_groupe'] = $groupe;
        $this->data['compteur'] = $count_groupe;
        $this->data['data'] = $data;
        $this->data['annee'] = $date->format('Y');
        // $this->data['duree'] = $total_temp;

        $this->template->title = 'Gestion des fichier Excel';
        $this->template->content = \View::forge('statistique/l3', $this->data);


    }

    public function action_stat()
    {
        $formData = \Input::post();
        $db = new \Model_My_Statistique();


        $date = new \DateTime();
        $date->setDate((int)$formData['annee'], 01, 01);

        $groupe = $db->getGroupe($date);
        $data['groupe'] = $groupe;

        //$count_groupe = count($groupe);


        $participant = array();

        foreach ($groupe as $groupes) {


            $participant[$groupes['t_nom']] = $db->getIdParticipantContrat($date, $groupes['id_groupe']);


            /**
             * Insertion des heures préstées depuis le début de la formation
             */


            $compteur = count($participant[$groupes['t_nom']]);
            $total = 0;
            $total_heure_eft_rw = 0;
            $heure_eft_rw = 0;
            $total_absenceJ = 0;
            $total_absenceNJ = 0;
            $total_conge = 0;
            $total_social = 0;

            for ($a = 0; $a < $compteur; $a++) {

                $id = $participant[$groupes['t_nom']][$a]['participant'];
                $participant[$groupes['t_nom']][$a]['type_contrat'] = \Model_Type_Contrat::find($participant[$groupes['t_nom']][$a]['type_contrat_id'])->to_array();

                $date_premier_contrat = new \DateTime($db->firstContrat($id));

                $total_heure = $db->totalFullHeuresParticipant($id, $date_premier_contrat, "'+', '@', '=', '$','#'");
                $participant[$groupes['t_nom']][$a]['heures_full'] = $total_heure[0]['fullTime'];
                $total = $total + $total_heure[0]['fullTime'];

                $heure_eft_rw = $db->totalFullHeuresSubside($id, $date_premier_contrat, "'+', '@', '='");
                $participant[$groupes['t_nom']][$a]['heures_eft_rw'] = $heure_eft_rw[0]['fullTime'];
                $total_heure_eft_rw = $total_heure_eft_rw + $heure_eft_rw[0]['fullTime'];

                $absenceJ = $db->totalFullHeuresParticipant($id, $date_premier_contrat, "'%', '/'");
                $participant[$groupes['t_nom']][$a]['heures_absenceJ'] = $absenceJ[0]['fullTime'];
                $total_absenceJ = $total_absenceJ + $absenceJ[0]['fullTime'];

                $absenceNJ = $db->totalFullHeuresParticipant($id, $date_premier_contrat, "'*'");
                $participant[$groupes['t_nom']][$a]['heures_absenceNJ'] = $absenceNJ[0]['fullTime'];
                $total_absenceNJ = $total_absenceNJ + $absenceNJ[0]['fullTime'];

                $conge = $db->totalFullHeuresParticipant_motif($id, $date_premier_contrat, "'Conge'");
                $participant[$groupes['t_nom']][$a]['heures_conge'] = $conge[0]['fullTime'];
                $total_conge = $total_conge + $conge[0]['fullTime'];

                $social = $db->totalFullHeuresParticipant_motif($id, $date_premier_contrat, "'Debriefing_ateliers','Gestion_Collective','Suivi_individuel'");
                $participant[$groupes['t_nom']][$a]['heures_social'] = $social[0]['fullTime'];
                $total_social = $total_social + $social[0]['fullTime'];

                $participant[$groupes['t_nom']][$a]['avertissement'] = $db->getAvertissememnt($id);


                /**
                 * Calcul de pourcentage par rapport aux heures préstée
                 */
                $fullTotalHeure = $total_heure[0]['fullTime'] + $absenceJ[0]['fullTime'] + $absenceNJ[0]['fullTime'];
                if ($total_heure[0]['fullTime'] == null) {
                    $participant[$groupes['t_nom']][$a]['pourcent_present'] = 0;
                } else {
                    $participant[$groupes['t_nom']][$a]['pourcent_present'] = (100 / $fullTotalHeure) * $total_heure[0]['fullTime'];
                }

                if ($absenceJ[0]['fullTime'] == null) {
                    $participant[$groupes['t_nom']][$a]['pourcent_absentj'] = 0;
                } else {
                    $participant[$groupes['t_nom']][$a]['pourcent_absentj'] = (100 / $fullTotalHeure) * $absenceJ[0]['fullTime'];
                }

                if ($absenceNJ[0]['fullTime'] == null) {
                    $participant[$groupes['t_nom']][$a]['pourcent_absentnj'] = 0;
                } else {
                    $participant[$groupes['t_nom']][$a]['pourcent_absentnj'] = (100 / $fullTotalHeure) * $absenceNJ[0]['fullTime'];
                }


                $date_diff = \DateTime::createFromFormat('d-m-Y', $participant[$groupes['t_nom']][$a]['d_date_debut_contrat']);
                $differenceMois = $db->nombresMoisEntreDeuxDate($date_diff);
                $participant[$groupes['t_nom']][$a]['heures_mois'] = $differenceMois[0]['mois'];


            }
            $participant['total_heure'][$groupes['t_nom']] = $total;
            $participant['total_heureEftRw'][$groupes['t_nom']] = $total_heure_eft_rw;
            $participant['total_absenceJ'][$groupes['t_nom']] = $total_absenceJ;
            $participant['total_absenceNJ'][$groupes['t_nom']] = $total_absenceNJ;
            $participant['total_conge'][$groupes['t_nom']] = $total_conge;
            $participant['total_social'][$groupes['t_nom']] = $total_social;


            //  }


        }


        \Maitrepylos\Excel\Statexcel::excel($data, $participant);
//Debug::dump($participant);

        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('test');

    }

    public function action_trimestre()
    {

        $formData = \Input::post();

        $date = new \DateTime();
        $date->setDate((int)$formData['annee'], 01, 01);

        /**
         * Nous devions récupérer les heures de l'année précedente.
         * Maintenant nous devons récupérer toutes les heures antérieur à l'année en cours
         * Je laisse pour historique et si jamais nous devons de nouveau changer.
         * L'utilisation en état faite dans $db->getHeuresPrecedente
         */
        //$anneePrecedente = ((int)$formData['annee'] - 1);


        $db = new \Model_My_Statistique();


        /**
         * Récupération des contrat par filière et par dérogation
         */
        //$formData['annexe1'][1] = $db->getCountContratgroupe($date);
        $formData['annexe1'][1] = $db->getCountContratFiliere($date);
        $formData['annexe1'][2] = $db->getCountContratFiliereDerogation($date);
        //$formData['annexe1'][2] = $db->getCountContratgroupeDerogation($date);
        $formData['xml'] = \Model_Centre::find('first');

        $count = count($formData['annexe1'][1]);
        $derogation = NULL;
        $result = NULL;


        for ($i = 0; $i < $count; $i++) {
            foreach ($formData['annexe1'][2] as $compteur) {
                //$boucle++;

                if ($formData['annexe1'][1][$i]['t_nom'] == $compteur['t_nom']) {
                    $derogation = $compteur['compteur'];
                    $result = ((int)$formData['annexe1'][1][$i]['compteur']) - ((int)$compteur['compteur']);

                }
            }

            $formData['annexe1'][1][$i]['derogation'] = $derogation;
            $formData['annexe1'][1][$i]['resultat'] = (int)$result;
        }

        $formData['agrement'] = $agrement = \Model_agrement::find($formData['agrement']);


        /*
         * Récupération des filière en fonctions des agréments
         */
        $filiere = $db->getFiliere($formData['agrement']->id_agrement);

        /*
         * Récupération de tout les contrats concerné par la filière durant l'année choisie
         */
        foreach ($filiere as $filieres) {

            /**
             * Récupération des contrats par filière
             */
            $formData['filiere'][$filieres['t_nom']] = $db->getContratTrimestre($date, $filieres['id_filiere']);


            $countContrat = count($formData['filiere'][$filieres['t_nom']]);


            //si une filière ne comprend aucun contrat alors on ne l'affiche pas.
            if($countContrat == 0){
                unset($formData['filiere'][$filieres['t_nom']]);
                continue;
            }

            for ($i = 0; $i < $countContrat; $i++) {
                /**
                 * Recherche motif fin de contrat
                 */

                $formData['filiere'][$filieres['t_nom']][$i]['type_fin_contrat'] = $db->getFinFormation($formData['filiere'][$filieres['t_nom']][$i]['id_contrat']);

                /**
                 * Calcul des heures éffectuées l'année précédente
                 * 25/07/2014, supression des heures assimilable.
                 */

                $formData['filiere'][$filieres['t_nom']][$i]['precedente'] =
                    $db->getHeuresPrecedente($formData['filiere'][$filieres['t_nom']][$i]['participant_id'], $date, "'+','$','@','#','='");
                /**
                 * Récupération des informations du stagiaire
                 */
                $formData['filiere'][$filieres['t_nom']][$i]['signaletique'] =
                    $db->participant($formData['filiere'][$filieres['t_nom']][$i]['participant_id'], $formData['filiere'][$filieres['t_nom']][$i]['id_contrat']);

                /**
                 * Calcule des heures de prestations pour l'année définie
                 */
                $formData['filiere'][$filieres['t_nom']][$i]['eft'] = $db->getHeuresTotalContrat($date, $formData['filiere'][$filieres['t_nom']][$i]['id_contrat'], "'+'");
                $formData['filiere'][$filieres['t_nom']][$i]['gratuit'] = $db->getHeuresTotalContrat($date, $formData['filiere'][$filieres['t_nom']][$i]['id_contrat'], "'$','#'");
                $formData['filiere'][$filieres['t_nom']][$i]['payant'] = $db->getHeuresTotalContrat($date, $formData['filiere'][$filieres['t_nom']][$i]['id_contrat'], "'@'");
                $formData['filiere'][$filieres['t_nom']][$i]['stage'] = $db->getHeuresTotalContrat($date, $formData['filiere'][$filieres['t_nom']][$i]['id_contrat'], "'='");
                $formData['filiere'][$filieres['t_nom']][$i]['assimile'] = $db->getHeuresTotalContrat($date, $formData['filiere'][$filieres['t_nom']][$i]['id_contrat'], "'/'");

            }

            for ($ii = 1; $ii < 13; $ii++) {

                /**
                 * Formation de la date pour l'extraire sur l'année et le mois
                 */
                $extract = $date->format('Y'). str_pad($ii, 2, 0, STR_PAD_LEFT);




                $formData['filiere'][$filieres['t_nom']]['mois'][$ii]['eft'] = $db->getHeuresTotalFiliere($extract, $filieres['id_filiere'], "'+'");
                $formData['filiere'][$filieres['t_nom']]['mois'][$ii]['gratuit'] = $db->getHeuresTotalFiliere($extract, $filieres['id_filiere'], "'$','#'");
                $formData['filiere'][$filieres['t_nom']]['mois'][$ii]['payant'] = $db->getHeuresTotalFiliere($extract, $filieres['id_filiere'], "'@'");
                $formData['filiere'][$filieres['t_nom']]['mois'][$ii]['stage'] = $db->getHeuresTotalFiliere($extract, $filieres['id_filiere'], "'='");
                $formData['filiere'][$filieres['t_nom']]['mois'][$ii]['assimile'] = $db->getHeuresTotalFiliere($extract, $filieres['id_filiere'], "'/'");

            }


        }


//        $path = \Asset::find_file('coordonnees.xml', 'xml');
//        $xml = simplexml_load_file($path);
//        $data['xml'] = $xml;
//
//        $cedefop = $db->getGroupe($date);
//
//
//
//        foreach($cedefop as $value) {
//            list ($nom) = explode('-', $value['t_nom']);
//            $nomGroupe[] = $nom;
//
//        }
//
//
//
//        $nomGroupe = array_unique($nomGroupe);
//        $data['cedefop'] = $cedefop;
//
//
//
//
//        $data['annexe1'][1] = $db->getCountGroupe($annee.'-01-01');
//
//        $data['annexe1'][2] = $db->getcountDerogationRw($annee.'-01-01');
//
//
//
//        $count = count($data['annexe1'][1]);
//        $derogation = NULL;
//        $result = NULL;
//
//
//
//        for($i = 0; $i < $count; $i++) {
//            //$boucle++;
//            //$result = NULL;
//            foreach($data['annexe1'][2] as $compteur) {
//                //$boucle++;
//
//                if ($data['annexe1'][1][$i]['t_nom'] == $compteur['t_nom']) {
//                    $derogation = $compteur['compteur'];
//                    $result = ((int)$data['annexe1'][1][$i]['compteur']) - ((int)$compteur['compteur']);
//
//                }
//
////                foreach($cedefop as $nom) {
////                    //$boucle++ ;
////                    if ($data['annexe1'][1][$i]['t_nom'] === (string)$nom['t_nom']) {
////                        $nom_cedefop = (string)$nom['t_nom'];
////                    }
//                }
//
//            $data['annexe1'][1][$i]['cedefop'] = null;
//            $data['annexe1'][1][$i]['derogation'] = null;
//            $data['annexe1'][1][$i]['resultat'] = null;
//                foreach($cedefop as $nom) {
//                    $nom_cedefop = null;
//                    //$boucle++ ;
//                    if ($data['annexe1'][1][$i]['t_nom'] === (string)$nom['t_nom']) {
//                        $nom_cedefop = (string)$nom['t_nom'];
//                    }
//
//                $data['annexe1'][1][$i]['cedefop'] = $nom_cedefop;
//                $data['annexe1'][1][$i]['derogation'] = $derogation;
//                $data['annexe1'][1][$i]['resultat'] = (int)$result;
//            }
//        }
//
//
//
//        /**
//         * Création de la table temporaire.
//         */
//        $db->createStatTatble($annee);
//        $db->stat();
//
//
//
//        foreach($nomGroupe as $value) {
//            //$boucle++;
//
//
//            $data['participant-' . $value] = $db->participant($value, $data['annee']);
//            $data['heure-' . $value] = array();
//
//
//            $count_participant = count($data['participant-' . $value]);
//
//            for($y = 0; $y < $count_participant; $y++) {
//                //$boucle++;
//                $data['participant-' . $value][$y]['t_nationalite'] = \Cranberry\MyXML::get_valeurPays($data['participant-' . $value][$y]['t_nationalite']);
//            }
//
//
//            $date_boucle = clone $date;
//            $date_boucle_precedente = clone $date;
//            $date_boucle_precedente->sub(new \DateInterval('P1Y'));
//
//            for($i = 0; $i < 12; $i++) {
//                  //$boucle++;
//                $jours = $date_boucle->format('t');
//                $jours_annee_precedente = $date_boucle_precedente->format('t');
//
//               // $jours = cal_days_in_month(CAL_JULIAN, $i+1, $annee);
//               // $jours_annee_precedente = cal_days_in_month(CAL_JULIAN, $i+1, $annee_precedente);
//
//
//                $eft = 0;
//                $gratuit = 0;
//                $payant = 0;
//                $stage = 0;
//                $assimile = 0;
//
//                for($a = 0; $a < $count_participant; $a++) {
//                    //$boucle++;
//
//                    $date_z = clone $date_boucle_precedente;
//                    $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['precedente'] = 0;
//                    $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['eft'] = 0;
//                    $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['gratuit'] = 0;
//                    $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['payant'] = 0;
//                    $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['stage'] = 0;
//                    $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['assimile'] = 0;
//
//
//                    for($z = 0; $z < $jours_annee_precedente; $z++) {
//                         //$boucle++ ;
//                        $heure_precedente = $db->calculHeuresMoisStatL2($data['participant-' . $value][$a]['id_participant'],
//                            "'+','-','$','@','#','/','='",$date_z->format('Y-m-d'));
//
//
//
//                        if ($heure_precedente[0]['iSum'] == null) {
//
//                            $heure_precedente[0]['iSum'] = 0;
//                        }
//
//
//
//
//
//                        $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['precedente'] =
//                            $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['precedente'] + $heure_precedente[0]['iSum'];
//
//                        $date_z->add(new \DateInterval('P1D'));
//
//                    }
//
//
//                    //      cacul des heures de l'année en cours.
//
//                    $date_b = clone $date_boucle;
//                    for($b=0;$b<$jours;$b++) {
//                       // \Maitrepylos\Debug::dump($date_b);
//                        //$boucle++;
//                        $db_eft =  $db->calculHeuresMoisStatTemporary($data['participant-'.$value][$a]['id_participant'],
//                            "'+','-'",$date_b->format('Y-m-d'));
//                        //calcul de tout les participants par mois
//                        $eft = $eft + $db_eft[0]['iSum'];
//
//                        //calcul des heures par participant et par année
//                        $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['eft'] =
//                            $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['eft'] + $db_eft[0]['iSum'];
//
//                        $db_gratuit =  $db->calculHeuresMoisStatTemporary($data['participant-'.$value][$a]['id_participant'],
//                            "'$'",$date_b->format('Y-m-d'));
//                        $gratuit = $gratuit + $db_gratuit[0]['iSum'];
//                        //calcul des heures par participant et par année
//                        $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['gratuit'] =
//                            $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['gratuit'] + $db_gratuit[0]['iSum'];
//
//                        $db_payant =  $db->calculHeuresMoisStatTemporary($data['participant-'.$value][$a]['id_participant'],
//                            "'@','#'",$date_b->format('Y-m-d'));
//                        $payant = $payant + $db_payant[0]['iSum'];
//                        //calcul des heures par participant et par année
//                        $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['payant'] =
//                            $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['payant'] + $db_payant[0]['iSum'];
//
//                        $db_stage =  $db->calculHeuresMoisStatTemporary($data['participant-'.$value][$a]['id_participant'],
//                            "'='",$date_b->format('Y-m-d'));
//                        $stage = $stage + $db_stage[0]['iSum'];
//                        //calcul des heures par participant et par année
//                        $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['stage'] =
//                            $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['stage'] + $db_stage[0]['iSum'];
//
//                        $db_assimile =  $db->calculHeuresMoisStatTemporary($data['participant-'.$value][$a]['id_participant'],
//                            "'/'",$date_b->format('Y-m-d'));
//                        $assimile = $assimile + $db_assimile[0]['iSum'];
//                        //calcul des heures par participant et par année
//                        $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['assimile'] =
//                            $data['heure-'.$value][$data['participant-'.$value][$a]['id_participant']]['assimile'] + $db_assimile[0]['iSum'];
//
//                        $date_b->add(new \DateInterval('P1D'));
//
//                    }
//                }
//                $data[$value][$i]['eft'] = $eft;
//                $data[$value][$i]['gratuit'] = $gratuit;
//                $data[$value][$i]['payant'] = $payant;
//                $data[$value][$i]['stage'] = $stage;
//                $data[$value][$i]['assimile'] = $assimile;
//
//                $date_boucle->add(new \DateInterval('P1M'));
//                $date_boucle_precedente->add(new \DateInterval('P1M'));
//            }
//        }
//

        $formData['annexe1'][1] = \SplFixedArray::fromArray($formData['annexe1'][1]);
        $formData['annexe1'][1]->setSize(24);

        \Maitrepylos\Excel\L3excel::excel($formData);
       // Debug::dump($formData);


        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('test');

    }


    public function action_fse(){

        $formData = \Input::post();

        $date = new \DateTime();
        $date->setDate((int)$formData['annee'], 01, 01);

        $db = new \Model_My_Statistique();

        $formData['contrat'] = $db->getGroupe($date);

        foreach($formData['contrat'] as $groupe){

            $formData['groupe'][$groupe['t_nom']] = $db->getContratFse($date,$groupe['id_groupe']);

            $countContrat = count($formData['groupe'][$groupe['t_nom']]);


            //si une filière ne comprend aucun contrat alors on ne l'affiche pas.
            if ($countContrat == 0) {
                unset($formData['groupe'][$groupe['t_nom']]);
                continue;
            }

            for ($i = 0; $i < $countContrat; $i++) {
                /**
                 * Recherche motif fin de contrat
                 */

                $formData['groupe'][$groupe['t_nom']][$i]['type_fin_contrat'] = $db->getFinFormation($formData['groupe'][$groupe['t_nom']][$i]['id_contrat']);

                /**
                 * Calcul des heures éffectuées l'année précédente
                 */

                $formData['groupe'][$groupe['t_nom']][$i]['precedente'] =
                    $db->getHeuresPrecedente($formData['groupe'][$groupe['t_nom']][$i]['participant_id'], $date, "'+','$','@','#','/','='");
                /**
                 * Récupération des informations du stagiaire
                 */
                $formData['groupe'][$groupe['t_nom']][$i]['signaletique'] =
                    $db->participant($formData['groupe'][$groupe['t_nom']][$i]['participant_id'], $formData['groupe'][$groupe['t_nom']][$i]['id_contrat']);

                /**
                 * Calcule des heures de prestations pour l'année définie
                 */
                $formData['groupe'][$groupe['t_nom']][$i]['eft'] = $db->getHeuresTotalContrat($date, $formData['groupe'][$groupe['t_nom']][$i]['id_contrat'], "'+'");
                $formData['groupe'][$groupe['t_nom']][$i]['gratuit'] = $db->getHeuresTotalContrat($date, $formData['groupe'][$groupe['t_nom']][$i]['id_contrat'], "'$','#'");
                $formData['groupe'][$groupe['t_nom']][$i]['payant'] = $db->getHeuresTotalContrat($date, $formData['groupe'][$groupe['t_nom']][$i]['id_contrat'], "'@'");
                $formData['groupe'][$groupe['t_nom']][$i]['stage'] = $db->getHeuresTotalContrat($date, $formData['groupe'][$groupe['t_nom']][$i]['id_contrat'], "'='");
                $formData['groupe'][$groupe['t_nom']][$i]['assimile'] = $db->getHeuresTotalContrat($date, $formData['groupe'][$groupe['t_nom']][$i]['id_contrat'], "'/'");

            }

            for ($ii = 1; $ii < 13; $ii++) {

                /**
                 * Formation de la date pour l'extraire sur l'année et le mois
                 */
                $extract = $date->format('Y') . str_pad($ii, 2, 0, STR_PAD_LEFT);


                $formData['groupe'][$groupe['t_nom']]['mois'][$ii]['eft'] = $db->getHeuresTotalFse($extract, $groupe['id_groupe'], "'+'");
                $formData['groupe'][$groupe['t_nom']]['mois'][$ii]['gratuit'] = $db->getHeuresTotalFse($extract, $groupe['id_groupe'], "'$','#'");
                $formData['groupe'][$groupe['t_nom']]['mois'][$ii]['payant'] = $db->getHeuresTotalFse($extract, $groupe['id_groupe'], "'@'");
                $formData['groupe'][$groupe['t_nom']]['mois'][$ii]['stage'] = $db->getHeuresTotalFse($extract, $groupe['id_groupe'], "'='");
                $formData['groupe'][$groupe['t_nom']]['mois'][$ii]['assimile'] = $db->getHeuresTotalFse($extract, $groupe['id_groupe'], "'/'");

            }
        }




        \Maitrepylos\Excel\Fseexcel::excel($formData);
      //   Debug::dump($formData);


        $this->template->title = 'Gestion des documents';
        $this->template->content = \View::forge('test');



    }


    public function action_menu($id)
    {
        $annees = \Model_Heures_Prestation::find('all', array('order_by' => array('annee' => 'desc')));


        $select_annees = array();
        foreach ($annees as $annee) {
            $select_annees[$annee->annee] = $annee->annee;
        }


        $route = array(1 => 'statistique/stat/', 2 => 'statistique/l3/', 3 => 'statistique/trimestre/', 4 => 'statistique/fse/');
        $title = array(1 => 'Statistiques de présence', 2 => 'Stat l3', 3 => 'Recensement annuel des stagiaires (xls)', 4 => 'Recensement des stagiaires FSE (xls)');
        $agrement = \Model_agrement::find('all');

        foreach ($agrement as $value) {

            $agrements[$value->id_agrement] = $value->t_agrement;
        }

        $this->data['route'] = $route[$id];
        $this->data['annee'] = $select_annees;
        $this->data['titre'] = $title[$id];
        $this->data['agrements'] = $agrements;
        $this->data['id'] = $id;
        $this->template->title = $title[$id];
        $this->template->content = \View::forge('statistique/menu', $this->data);

    }
}

?>
