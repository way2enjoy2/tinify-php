<?php

use Way2enjoy\CurlMock;

class Way2enjoyResultTest extends TestCase {
    public function testWithMetaAndDataWidthShouldReturnImageWidth() {
        $result = new Way2enjoy\Result(array("image-width" => "100"), "image data");
        $this->assertSame(100, $result->width());
    }

    public function testWithMetaAndDataHeightShouldReturnImageHeight() {
        $result = new Way2enjoy\Result(array("image-height" => "60"), "image data");
        $this->assertSame(60, $result->height());
    }

    public function testWithMetaAndDataLocationShouldReturnNull() {
        $result = new Way2enjoy\ResultMeta(array(), "image data");
        $this->assertSame(null, $result->location());
    }

    public function testWithMetaAndDataSizeShouldReturnContentLength() {
        $result = new Way2enjoy\Result(array("content-length" => "450"), "image data");
        $this->assertSame(450, $result->size());
    }

    public function testWithMetaAndDataContentTypeShouldReturnMimeType() {
        $result = new Way2enjoy\Result(array("content-type" => "image/png"), "image data");
        $this->assertSame("image/png", $result->contentType());
    }

    public function testWithMetaAndDataToBufferShouldReturnImageData() {
        $result = new Way2enjoy\Result(array(), "image data");
        $this->assertSame("image data", $result->toBuffer());
    }
}
