<?php

use Way2enjoy\CurlMock;

class Way2enjoySourceTest extends TestCase {
    private $dummyFile;

    public function setUp() {
        parent::setUp();
        $this->dummyFile = __DIR__ . "/examples/dummy.png";
    }

    public function testWithInvalidApiKeyFromFileShouldThrowAccountException() {
        Way2enjoy\setKey("invalid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Credentials are invalid"}'
        ));

        $this->setExpectedException("Way2enjoy\AccountException");
        Way2enjoy\Source::fromFile($this->dummyFile);
    }

    public function testWithInvalidApiKeyFromBufferShouldThrowAccountException() {
        Way2enjoy\setKey("invalid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Credentials are invalid"}'
        ));

        $this->setExpectedException("Way2enjoy\AccountException");
        Way2enjoy\Source::fromBuffer("png file");
    }

    public function testWithInvalidApiKeyFromUrlShouldThrowAccountException() {
        Way2enjoy\setKey("invalid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Credentials are invalid"}'
        ));

        $this->setExpectedException("Way2enjoy\AccountException");
        Way2enjoy\Source::fromUrl("http://example.com/test.jpg");
    }

    public function testWithValidApiKeyFromFileShouldReturnSource() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        $this->assertInstanceOf("Way2enjoy\Source", Way2enjoy\Source::fromFile($this->dummyFile));
    }

    public function testWithValidApiKeyFromFileShouldReturnSourceWithData() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Way2enjoy\Source::fromFile($this->dummyFile)->toBuffer());
    }

    public function testWithValidApiKeyFromBufferShouldReturnSource() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        $this->assertInstanceOf("Way2enjoy\Source", Way2enjoy\Source::fromBuffer("png file"));
    }

    public function testWithValidApiKeyFromBufferShouldReturnSourceWithData() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Way2enjoy\Source::fromBuffer("png file")->toBuffer());
    }

    public function testWithValidApiKeyFromUrlShouldReturnSource() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        $this->assertInstanceOf("Way2enjoy\Source", Way2enjoy\Source::fromUrl("http://example.com/testWithValidApiKey.jpg"));
    }

    public function testWithValidApiKeyFromUrlShouldReturnSourceWithData() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Way2enjoy\Source::fromUrl("http://example.com/testWithValidApiKey.jpg")->toBuffer());
    }

    public function testWithValidApiKeyFromUrlShouldThrowExceptionIfRequestIsNotOK() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 400, "body" => '{"error":"Source not found","message":"Cannot parse URL"}'
        ));

        $this->setExpectedException("Way2enjoy\ClientException");
        Way2enjoy\Source::fromUrl("file://wrong");
    }

    public function testWithValidApiKeyResultShouldReturnResult() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201,
            "headers" => array("Location" => "https://api.way2enjoy.com/some/location"),
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertInstanceOf("Way2enjoy\Result", Way2enjoy\Source::fromBuffer("png file")->result());
    }

    public function testWithValidApiKeyPreserveShouldReturnSource() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "copyrighted file"
        ));

        $this->assertInstanceOf("Way2enjoy\Source", Way2enjoy\Source::fromBuffer("png file")->preserve("copyright", "location"));
        $this->assertSame("png file", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyPreserveShouldReturnSourceWithData() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "copyrighted file"
        ));

        $this->assertSame("copyrighted file", Way2enjoy\Source::fromBuffer("png file")->preserve("copyright", "location")->toBuffer());
        $this->assertSame("{\"preserve\":[\"copyright\",\"location\"]}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyPreserveShouldReturnSourceWithDataForArray() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "copyrighted file"
        ));

        $this->assertSame("copyrighted file", Way2enjoy\Source::fromBuffer("png file")->preserve(array("copyright", "location"))->toBuffer());
        $this->assertSame("{\"preserve\":[\"copyright\",\"location\"]}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyPreserveShouldIncludeOtherOptionsIfSet() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "copyrighted resized file"
        ));

        $source = Way2enjoy\Source::fromBuffer("png file")->resize(array("width" => 400))->preserve(array("copyright", "location"));

        $this->assertSame("copyrighted resized file", $source->toBuffer());
        $this->assertSame("{\"resize\":{\"width\":400},\"preserve\":[\"copyright\",\"location\"]}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyResizeShouldReturnSource() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "small file"
        ));

        $this->assertInstanceOf("Way2enjoy\Source", Way2enjoy\Source::fromBuffer("png file")->resize(array("width" => 400)));
        $this->assertSame("png file", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyResizeShouldReturnSourceWithData() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "small file"
        ));

        $this->assertSame("small file", Way2enjoy\Source::fromBuffer("png file")->resize(array("width" => 400))->toBuffer());
        $this->assertSame("{\"resize\":{\"width\":400}}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyStoreShouldReturnResultMeta() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201,
            "headers" => array("Location" => "https://api.way2enjoy.com/some/location"),
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "body" => '{"store":{"service":"s3","aws_secret_access_key":"abcde"}}'
        ), array("status" => 200));

        $options = array("service" => "s3", "aws_secret_access_key" => "abcde");
        $this->assertInstanceOf("Way2enjoy\Result", Way2enjoy\Source::fromBuffer("png file")->store($options));
        $this->assertSame("{\"store\":{\"service\":\"s3\",\"aws_secret_access_key\":\"abcde\"}}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyStoreShouldReturnResultMetaWithLocation() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.wy2enjoy.com/shrink", array(
            "status" => 201,
            "headers" => array("Location" => "https://api.wy2enjoy.com/some/location"),
        ));

        CurlMock::register("https://api.wy2enjoy.com/some/location", array(
            "body" => '{"store":{"service":"s3"}}'
        ), array(
            "status" => 201,
            "headers" => array("Location" => "https://bucket.s3.amazonaws.com/example"),
        ));

        $location = Way2enjoy\Source::fromBuffer("png file")->store(array("service" => "s3"))->location();
        $this->assertSame("https://bucket.s3.amazonaws.com/example", $location);
        $this->assertSame("{\"store\":{\"service\":\"s3\"}}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyStoreShouldIncludeOtherOptionsIfSet() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.wy2enjoy.com/shrink", array(
            "status" => 201,
            "headers" => array("Location" => "https://api.wy2enjoy.com/some/location"),
        ));

        CurlMock::register("https://api.wy2enjoy.com/some/location", array(
            "body" => '{"resize":{"width":300},"store":{"service":"s3","aws_secret_access_key":"abcde"}}'
        ), array("status" => 200));

        $options = array("service" => "s3", "aws_secret_access_key" => "abcde");
        $this->assertInstanceOf("Way2enjoy\Result", Way2enjoy\Source::fromBuffer("png file")->resize(array("width" => 300))->store($options));
        $this->assertSame("{\"resize\":{\"width\":300},\"store\":{\"service\":\"s3\",\"aws_secret_access_key\":\"abcde\"}}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyToBufferShouldReturnImageData() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.way2enjoy.com/some/location")
        ));
        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Way2enjoy\Source::fromBuffer("png file")->toBuffer());
    }

    public function testWithValidApiKeyToFileShouldStoreImageData() {
        Way2enjoy\setKey("valid");

        CurlMock::register("https://api.way2enjoy.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.wy2enjoy.com/some/location")
        ));

        CurlMock::register("https://api.way2enjoy.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $path = tempnam(sys_get_temp_dir(), "wy2enjoy-php");
        Way2enjoy\Source::fromBuffer("png file")->toFile($path);
        $this->assertSame("compressed file", file_get_contents($path));
    }
}
