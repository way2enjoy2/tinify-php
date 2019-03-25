<?php

require_once("curl_mock.php");
require_once("vendor/autoload.php");

class TestCase extends \PHPUnit_Framework_TestCase {
    function setUp() {
        Way2enjoy\CurlMock::reset();
        Way2enjoy\setKey(NULL);
        Way2enjoy\setProxy(NULL);
    }

    function tearDown() {
        Way2enjoy\CurlMock::reset();
    }
}
