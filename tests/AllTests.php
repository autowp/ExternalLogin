<?php

require_once 'TestHelper.php';

class AllTests
{

    public static function main()
    {
        /*$parameters = array();

        PHPUnit_TextUI_TestRunner::run(self::suite(), $parameters);*/
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Autowp Library');
        $suite->addTestSuite('Autowp_AllTests');

        return $suite;
    }
}