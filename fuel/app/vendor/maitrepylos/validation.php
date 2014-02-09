<?php

namespace Maitrepylos;


/**
 * Classe de validation de MaitrePylos.
 * @since Cette classe permet de faire des vérifiaction sur les contrats et la
 * géstion des heures.
 */
class Validation
{

    const HEURE_MAX_SEMAINE = 144000;

    /**
     * Validation pour une valeur n'est aps supérieur à 100.
     * @param type $value
     * @return boolean
     */
    public static function _validation_exceeds_onehundred($value)
    {
        if ($value > 100) {

            return false;
        }

        return true;
    }

    /**
     * Validation pour une valeur n'est pas supérieur à 40 heures en secondes.
     * @param type $value
     * @return boolean
     */
    public static function _validation_more_forty_hours($value)
    {
        $time = new \Maitrepylos\Timetosec();

        if ($time->StringToTime($value) > self::HEURE_MAX_SEMAINE) {
            return false;
        }

        return true;
    }

    /**
     * Validation pour une date n'est pas inférieure à l'autre
     * @param type $date1
     * @param type $date2
     * @return boolean
     */
    public static function _validation_date_less($date1, $date2)
    {


        list($day, $month, $year) = explode('/', $date1);
        $date[0] = new \DateTime();
        $date[0]->setDate($year, $month, $day);

        list($day, $month, $year) = explode('/', $date2);
        $date[1] = new \DateTime();
        $date[1]->setDate($year, $month, $day);


        if ($date[0] < $date[1]) {

            return false;
        }

        return true;
    }

    /**
     * Validation qu'une date n'est pas supérieur à 18 mois.
     * @param type $date1
     * @param type $date2
     * @return boolean
     */
    public static function _validation_eighteen_months_more($date1, $date2)
    {

        list($day, $month, $year) = explode('/', $date1);
        $date[0] = new \DateTime();
        $date[0]->setDate($year, $month, $day);

        list($day, $month, $year) = explode('/', $date2);
        $date[1] = new \DateTime();
        $date[1]->setDate($year, $month, $day);

        $date[0]->add(new \DateInterval('P18M'));

        if ($date[1] > $date[0]) {
            return false;
        }
        return true;
    }

    /**
     * Validation sur True/False
     * @param type $value
     * @return boolean
     */
    public static function _validation_true($value)
    {

        if ($value === true) {
            return true;
        }

        return false;
    }

    /**
     * Validation du paterne des heures de prestations
     * @param type $value
     * @return boolean
     */
    public static function _validation_bland_hour($value)
    {
        if (!preg_match('/^[0-9]{2,3}(\:[0-5]{1}[0-9]{1})(\:[0-5]{1}[0-9]{1})?$/', $value)) {
            return false;
        }
        return true;
    }

    public static function _validation_min_hour($value){

        $time = new \Maitrepylos\Timetosec();
        $hour = $time->StringToTime($value);
        $xml = \Maitrepylos\Config::load('config.xml','xml');
        if((int)$hour > 0 && (int)$hour < $xml->mintime ){
            return false;
        }
        return true;
    }

    public static function _validation_no_hour($value){

        $time = new \Maitrepylos\Timetosec();
        $hour = $time->StringToTime($value);

        if ((int)$hour <= 0) {
            return false;
        }
        return true;

    }

    public static function validate_hour()
    {
        $val = Validation::forge();

        $val->add_callable('\Maitrepylos\Validation');

        $val->add_field('heuresprester', 'Heures', 'required|bland_hour');
        $val->set_message('bland_hour', 'Le champ :label doit-être sous forme 00:00');

        return $val;
    }

    public static function _validation_numeric($value){

        if($value == null){
            return true;
        }

        if(is_numeric($value)){
            return true;
        }
        return false;

    }

    function _validation_iban($iban)
    {
        /*Régles de validation par pays*/
        static $rules = array(
            'AL' => '[0-9]{8}[0-9A-Z]{16}',
            'AD' => '[0-9]{8}[0-9A-Z]{12}',
            'AT' => '[0-9]{16}',
            'BE' => '[0-9]{16}',
            'BA' => '[0-9]{16}',
            'BG' => '[A-Z]{4}[0-9]{6}[0-9A-Z]{8}',
            'HR' => '[0-9]{17}',
            'CY' => '[0-9]{8}[0-9A-Z]{16}',
            'CZ' => '[0-9]{20}',
            'DK' => '[0-9]{14}',
            'EE' => '[0-9]{16}',
            'FO' => '[0-9]{14}',
            'FI' => '[0-9]{14}',
            'FR' => '[0-9]{10}[0-9A-Z]{11}[0-9]{2}',
            'GE' => '[0-9A-Z]{2}[0-9]{16}',
            'DE' => '[0-9]{18}',
            'GI' => '[A-Z]{4}[0-9A-Z]{15}',
            'GR' => '[0-9]{7}[0-9A-Z]{16}',
            'GL' => '[0-9]{14}',
            'HU' => '[0-9]{24}',
            'IS' => '[0-9]{22}',
            'IE' => '[0-9A-Z]{4}[0-9]{14}',
            'IL' => '[0-9]{19}',
            'IT' => '[A-Z][0-9]{10}[0-9A-Z]{12}',
            'KZ' => '[0-9]{3}[0-9A-Z]{3}[0-9]{10}',
            'KW' => '[A-Z]{4}[0-9]{22}',
            'LV' => '[A-Z]{4}[0-9A-Z]{13}',
            'LB' => '[0-9]{4}[0-9A-Z]{20}',
            'LI' => '[0-9]{5}[0-9A-Z]{12}',
            'LT' => '[0-9]{16}',
            'LU' => '[0-9]{3}[0-9A-Z]{13}',
            'MK' => '[0-9]{3}[0-9A-Z]{10}[0-9]{2}',
            'MT' => '[A-Z]{4}[0-9]{5}[0-9A-Z]{18}',
            'MR' => '[0-9]{23}',
            'MU' => '[A-Z]{4}[0-9]{19}[A-Z]{3}',
            'MC' => '[0-9]{10}[0-9A-Z]{11}[0-9]{2}',
            'ME' => '[0-9]{18}',
            'NL' => '[A-Z]{4}[0-9]{10}',
            'NO' => '[0-9]{15}',
            'PL' => '[0-9]{24}',
            'PT' => '[0-9]{21}',
            'RO' => '[A-Z]{4}[0-9A-Z]{16}',
            'SM' => '[A-Z][0-9]{10}[0-9A-Z]{12}',
            'SA' => '[0-9]{2}[0-9A-Z]{18}',
            'RS' => '[0-9]{18}',
            'SK' => '[0-9]{20}',
            'SI' => '[0-9]{15}',
            'ES' => '[0-9]{20}',
            'SE' => '[0-9]{20}',
            'CH' => '[0-9]{5}[0-9A-Z]{12}',
            'TN' => '[0-9]{20}',
            'TR' => '[0-9]{5}[0-9A-Z]{17}',
            'AE' => '[0-9]{19}',
            'GB' => '[A-Z]{4}[0-9]{14}'
        );
        /*On vérifie la longueur minimale*/
        if (mb_strlen($iban) < 18) {
            return false;
        }
        /*On récupère le code ISO du pays*/
        $ctr = substr($iban, 0, 2);
        if (isset($rules[$ctr]) === false) {
            return false;
        }
        /*On récupère la règle de validation en fonction du pays*/
        $check = substr($iban, 4);
        /*Si la règle n'est pas bonne l'IBAN n'est pas valide*/
        if (preg_match('~' . $rules[$ctr] . '~', $check) !== 1) {
            return false;
        }
        /*On récupère la chaine qui permet de calculer la validation*/
        $check = $check . substr($iban, 0, 4);
        /*On remplace les caractères alpha par leurs valeurs décimales*/
        $check = str_replace(
            array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'),
            array('10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35'),
            $check
        );
        /*On effectue la vérification finale*/
        return bcmod($check, 97) === '1';
    }

}

?>
