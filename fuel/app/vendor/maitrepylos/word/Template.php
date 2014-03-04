<?php

namespace Maitrepylos;


require __DIR__ . '../../Classes/PHPWord.php';;

//ini_set('date.timezone','Europe/Brussels');


class Contrat
{


    public function __constructu()
    {

        $PHPWord = new PHPWord();

        $document = $PHPWord->loadTemplate('../../../data/Template.docx');

        $document->setValue('{Value1}', 'Sun');
        $document->setValue('{Value2}', 'Mercury');
        $document->setValue('Value3', 'Venus');
        $document->setValue('Value4', 'Earth');
        $document->setValue('Value5', 'Mars');
        $document->setValue('Value6', 'Jupiter');
        $document->setValue('Value7', 'Saturn');
        $document->setValue('Value8', 'Uranus');
        $document->setValue('Value9', 'Neptun');
        $document->setValue('Value10', 'Pluto');

        $document->setValue('weekday', date('l'));
        $document->setValue('time', date('H:i'));

//$header = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
//header('Content-type:' . $header);
//header('Content-Disposition:inline;filename=Solarsystem.docx');
//$document->save('php://output');
//$document->save('Solarsystem.docx');
        $file = '../../../data/Solarsystem.docx';
        $document->save($file);

        if (!$file) {
            // File doesn't exist, output error
            die('file not found');
        } else {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$file");
            header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
            header("Content-Transfer-Encoding: binary");

            readfile($file);
        }

        unlink($file);

        exit;

    }


}


