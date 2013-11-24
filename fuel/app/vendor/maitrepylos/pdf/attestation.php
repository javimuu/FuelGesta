<?php
/**
 * Classe de generation du PDF L1
 * @copyright  2008 Formatux Technologies
 * @author     info@formatux.be  Ernaelsten Gerard
 * @license    http://www.formatux.be/contact   Merci de prendre contact avec l'auteur
 * @version    Release: 0.3
 * @link       http://www.formatux.be
 * @since      Class available since Release 1.7.0
 * @deprecated Class deprecated in Release 2.0.0
 * @category   Pontaury
 * @package    Maitrepylos\Pdf
 * @subpackage paye
 */

namespace Maitrepylos\Pdf;


class Attestation
{
    //public function pdf ($formData)
    /**
     *Generation de attestation
     *@method pdf
     * @param array $formData[$z]
     * @return pdf
     */
    public static function pdf($formData,$xml)
    {
        $pdf = \Pdf::forge('fpdf', array('P', 'mm', 'A4'));
        $count = ceil(count($formData) / 14);

        $c = 0;
        for ($i = 0; $i < $count; $i++) {
            $pdf->AddPage('L', 'A4');
            $pdf->SetFont('Times', '', 12);
            $pdf->SetXY(17, 12);
            $pdf->Cell(85, 30, '', 1);
            $pdf->Text(19, 17, 'Organisme : ' . $xml->t_denomination);
            $pdf->Text(19, 21, utf8_decode('Adresse Siège Social : ' . $xml->t_adresse));
            $pdf->Text(19, 26, $xml->t_code_postal . ' ' . $xml->t_localite);
            $pdf->Text(19, 31, utf8_decode('Personne de contact : ' . $xml->t_secretaire));
            $pdf->Text(19, 37, utf8_decode('Tél : ' . $xml->t_telephone));
            $pdf->Image('assets/img/administratif/logoRw.jpeg', 122, 22, 40);
            $pdf->Text(195, 22, 'Le ' . date('d') . ' / ' . date('m') . ' / ' . date('Y'));

            $pdf->SetFont('Times', 'B', 12);
            $pdf->Text(34, 70, 'DEMANDE D\'ATTESTATION D\'INSCRIPTION COMME DEMANDEUR D\'EMPLOI A DESTINATION DES EFT/ OISP');

            $pdf->SetFont('Times', 'B', 7);
            $pdf->Text(261, 68, '1');

            $pdf->SetFont('Times', '', 12);
            $pdf->SetXY(27, 80);
            $pdf->Cell(62, 5, 'NOM', 1, '', 'C');
            $pdf->SetXY(89, 80);
            $pdf->Cell(52, 5, 'PRENOM', 1, '', 'C');
            $pdf->SetXY(141, 80);
            $pdf->Cell(35, 5, 'Sexe', 1, '', 'C');
            $pdf->SetXY(176, 80);
            $pdf->Cell(45, 5, 'Registre National', 1, '', 'C');
            $pdf->SetXY(221, 80);
            $pdf->Cell(51, 5, utf8_decode('date d\'entrée en formation'), 1, '', 'C');
            $y = 85;
            for ($a = 0; $a < 14; $a++) {
                $pdf->SetXY(27, $y);
                $pdf->Cell(62, 5, $formData[$c]['t_nom'], 1, '', 'C');
                $pdf->SetXY(89, $y);
                $pdf->Cell(52, 5, utf8_decode($formData[$c]['t_prenom']), 1, '', 'C');
                $pdf->SetXY(141, $y);
                $pdf->Cell(35, 5, $formData[$c]['t_sexe'], 1, '', 'C');
                $pdf->SetXY(176, $y);
                $pdf->Cell(45, 5, $formData[$c]['t_registre_national'], 1, '', 'C');
                $pdf->SetXY(221, $y);
                $pdf->Cell(51, 5, \Maitrepylos\Date::db_to_date($formData[$c]['d_date_debut_contrat']), 1, '', 'C');
                $y = $y + 5;
                $c++;
            }

            $pdf->Line(25, 175, 86, 175);
            $pdf->SetXY(26, 179);
            $pdf->drawTextBox(utf8_decode(' En application des articles 4, 5 et 6 du décret du 1er avril 2004 relatif à l\'agrément et au subventionnement des organismes d\'insertion socioprofessionnelle et des entreprises
de formation par le travail (M.B. du 01-06-2004).
'), 245, 10, '', '', 0);

            $pdf->SetFont('Times', 'B', 7);
            $pdf->Text(261, 68, '1');
            $pdf->Text(25, 179, '1');


            //$pdf->drawTextBox('Organisme : '.$xml->denomination,'68','23','','',
            //on initialise un compteur, pour le cas, ou il aurais plus de 15 participants

        }
        $pdf->Output();
    }
}

?>