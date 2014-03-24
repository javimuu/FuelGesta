<?php

namespace Maitrepylos\Word;


require __DIR__ . '/../../word/PHPWord.php';;

//ini_set('date.timezone','Europe/Brussels');


class Contrat
{


    public function __construct($contrat)
    {


        $PHPWord = new \PHPWord();

        $document = $PHPWord->loadTemplate(APPPATH.'data/Template.docx');
        //$data = \Model_My_Contrat::getImpressionContrat($contrat);

        $pdo = \Maitrepylos\Db::getPdo();
        $sql = "SELECT p.t_nom,t_prenom,t_nom_rue,t_bte,t_code_postal,t_commune,d_date_naissance,
        d_date_inscription_forem,d_date_debut_contrat,d_date_fin_contrat_prevu,t_lieu_naissance,
        t_telephone,t_gsm,t_nationalite,t_registre_national,t_numero_inscription_forem,t_compte_bancaire,
        f.t_nom as filiere
        FROM contrat c
        INNER JOIN participant p
        ON p.id_participant = c.participant_id
        INNER JOIN adresse a
		ON p.id_participant = a.participant_id
		INNER JOIN groupe g
        ON g.id_groupe = c.groupe_id
        INNER JOIN filiere f
        ON f.id_filiere = g.filiere_id
        WHERE c.id_contrat = ?";

        $r = $pdo->prepare($sql);
        $r->execute(array($contrat));
        $data = $r->fetch(\PDO::FETCH_ASSOC);

        $adresse = $data['t_nom_rue'].' '.$data['t_bte'].' '.$data['t_code_postal'].' '.$data['t_commune'];
        $date = \DateTime::createFromFormat('Y-m-d',$data['d_date_naissance']);
        $dateForem = \DateTime::createFromFormat('Y-m-d',$data['d_date_inscription_forem']);
        $dateDebutContrat = \DateTime::createFromFormat('Y-m-d',$data['d_date_debut_contrat']);
        $dateFinContrat = \DateTime::createFromFormat('Y-m-d',$data['d_date_fin_contrat_prevu']);
        $dateNaissanceLieu = $date->format('d-m-Y').' '.$data['t_lieu_naissance'];

        $interval = $dateDebutContrat->diff($dateFinContrat);
        $nbmonth = ($interval->y * 12) + $interval->m ;
        $heures = 150 * $nbmonth;

        $document->setValue('{nom}', $data['t_nom']);
        $document->setValue('{prenom}', $data['t_prenom']);
        $document->setValue('{adresse}', $adresse);
        $document->setValue('{telephone}',$data['t_telephone']);
        $document->setValue('{gsm}', $data['t_gsm']);
        $document->setValue('{naissance}', $dateNaissanceLieu);
        $document->setValue('{nationalite}', $data['t_nationalite']);
        $document->setValue('{national}', $data['t_registre_national']);
        $document->setValue('{numforem}', $data['t_numero_inscription_forem']);
        $document->setValue('{dateforem}', $dateForem->format('d-m-Y'));
        $document->setValue('{comptebancaire}', $data['t_compte_bancaire']);
        $document->setValue('{filiere}', $data['filiere']);
        $document->setValue('{dateformation}', $dateDebutContrat->format('Y-m-d'));
        $document->setValue('{heures}', $heures);


        $file = APPPATH.'data/contrat.docx';
        $document->save($file);

        if (!$file) {
            // File doesn't exist, output error     
            die('file not found');
        } else {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=".$data['t_nom'].'_'.$data['t_prenom'].'.doc'."");
            header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
            header("Content-Transfer-Encoding: binary");

            readfile($file);
        }

        unlink($file);

        exit;

    }


}


