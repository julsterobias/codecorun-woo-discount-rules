<?php
/**
 * 
 * wcdr_main_class
 * @version 1.0
 * 
 */
namespace wcdr\main;

defined( 'ABSPATH' ) or die( 'No access area' );

class wcdr_main_class{

    private static $instance = null;

    public static function factory()
    {
        if(!self::$instance){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct()
    {
        //render assets
    }

    public function assets()
    {

    }

}

?>