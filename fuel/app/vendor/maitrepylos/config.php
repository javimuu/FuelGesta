<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gg
 * Date: 24/07/13
 * Time: 17:54
 * To change this template use File | Settings | File Templates.
 */

namespace Maitrepylos;


class Config {

    static $_path = null;


    public static function load($file,$path){

        self::$_path = \Asset::find_file($file, $path);
        return simplexml_load_file(self::$_path);
    }

    public static function setMintime($data){
        $time = new \Maitrepylos\Timetosec();
        $xml = self::load('config.xml','xml');
        unset($xml->mintime);
        $xml->addChild('mintime', $time->StringToTime($data));
        $xml->asXML(self::$_path);

    }

    public static function getMinTime(){
        $time = new \Maitrepylos\Timetosec();
        $xml = self::load('config.xml', 'xml');
        return $time->TimeToString($xml->mintime);

    }

}