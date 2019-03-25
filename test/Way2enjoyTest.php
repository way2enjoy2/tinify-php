<?php

use Way2enjoy\CurlMock;

class ClientTest extends TestCase {
    private $dummyFile;

    public function setUp() {
        parent::setUp();
        $this->dummyFile = __DIR__ . "/examples/dummy.png";
    }

    public function testKeyShouldResetClientWithNewKey() {
        CurlMock::register("https://api.way2enjoy.com/", array("status" => 200));
        Way2enjoy\setKey("abcde");
        Way2enjoy\Way2enjoy::getClient();
        Way2enjoy\setKey("fghij");
        $client = Way2enjoy\Way2enjoy::getClient();
        $client->request("get", "/");

        $this->assertSame("api:fghij", CurlMock::last(CURLOPT_USERPWD));
    }

    public function testAppIdentifierShouldResetClientWithNewAppIdentifier() {
        CurlMock::register("https://api.way2enjoy.com/", array("status" => 200));
        Way2enjoy\setKey("abcde");
        Way2enjoy\setAppIdentifier("MyApp/1.0");
        Way2enjoy\Way2enjoy::getClient();
        Way2enjoy\setAppIdentifier("MyApp/2.0");
        $client = Way2enjoy\Way2enjoy::getClient();
        $client->request("get", "/");

        $this->assertSame(Way2enjoy\Client::userAgent() . " MyApp/2.0", CurlMock::last(CURLOPT_USERAGENT));
    }

    public function testProxyShouldResetClientWithNewProxy() {
        CurlMock::register("https://api.way2enjoy.com/", array("status" => 200));
        Way2enjoy\setKey("abcde");
        Way2enjoy\setProxy("http://localhost");
        Way2enjoy\Way2enjoy::getClient();
        Way2enjoy\setProxy("http://user:pass@localhost:8080");
        $client = Way2enjoy\Way2enjoy::getClient();
        $client->request("get", "/");

        $this->assertSame(Way2enjoy\Client::userAgent() . " MyApp/2.0", CurlMock::last(CURLOPT_USERAGENT));
    }

    public function testClientWithKeyShouldReturnClient() {
        Way2enjoy\setKey("abcde");
        $this->assertInstanceOf("Way2enjoy\Client", Way2enjoy\Way2enjoy::getClient());
    }

    public function testClientWithoutKeyShouldThrowException() {
        $this->setExpectedException("Way2enjoy\AccountException");
        Way2enjoy\Way2enjoy::getClient();
    }

    public function testClientWithInvalidProxyShouldThrowException() {
        $this->setExpectedException("Way2enjoy\ConnectionException");
        Way2enjoy\setKey("abcde");
        Way2enjoy\setProxy("http-bad-url");
        Way2enjoy\Way2enjoy::getClient();
    }

    public function testSetClientShouldReplaceClient() {
        Way2enjoy\setKey("abcde");
        Way2enjoy\Way2enjoy::setClient("foo");
        $this->assertSame("foo", Way2enjoy\Way2enjoy::getClient());
    }

    public function testValidateWithValidKeyShouldReturnTrue() {
        Way2enjoy\setKey("valid");
        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 400, "body" => '{"error":"Input missing","message":"No input"}'
        ));
        $this->assertTrue(Way2enjoy\validate());
    }

    public function testValidateWithLimitedKeyShouldReturnTrue() {
        v\setKey("invalid");
        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 429, "body" => '{"error":"Too many requests","message":"Your monthly limit has been exceeded"}'
        ));
        $this->assertTrue(Way2enjoy\validate());
    }

    public function testValidateWithErrorShouldThrowException() {
        Way2enjoy\setKey("invalid");
        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Credentials are invalid"}'
        ));
        $this->setExpectedException("Way2enjoy\AccountException");
        Way2enjoy\validate();
    }

    public function testFromFileShouldReturnSource() {
        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));
        Way2enjoy\setKey("valid");
        $this->assertInstanceOf("Way2enjoy\Source", Way2enjoy\fromFile($this->dummyFile));
    }

    public function testFromBufferShouldReturnSource() {
        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));
        Way2enjoy\setKey("valid");
        $this->assertInstanceOf("Way2enjoy\Source", Way2enjoy\fromBuffer("png file"));
    }

    public function testFromUrlShouldReturnSource() {
        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));
        Way2enjoy\setKey("valid");
        $this->assertInstanceOf("Way2enjoy\Source", Way2enjoy\fromUrl("http://example.com/test.jpg"));
    }
}
