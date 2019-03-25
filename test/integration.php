<?php

if (!getenv("WAY2ENJOY_KEY")) {
    exit("Set the WAY2ENJOY_KEY environment variable.\n");
}

class ClientIntegrationTest extends PHPUnit_Framework_TestCase {
    static private $optimized;

    static public function setUpBeforeClass() {
        \Way2enjoy\setKey(getenv("WAY2ENJOY_KEY"));
        \Way2enjoy\setProxy(getenv("WAY2ENJOY_PROXY"));
        \Way2enjoy\validate();

        $unoptimizedPath = __DIR__ . "/examples/voormedia.png";
        self::$optimized = \Tinify\fromFile($unoptimizedPath);
    }

    public function testShouldCompressFromFile() {
        $path = tempnam(sys_get_temp_dir(), "tinify-php");
        self::$optimized->toFile($path);

        $size = filesize($path);
        $contents = fread(fopen($path, "rb"), $size);

        $this->assertGreaterThan(1000, $size);
        $this->assertLessThan(1500, $size);

        /* width == 137 */
        $this->assertContains("\0\0\0\x89", $contents);
        $this->assertNotContains("Copyright Way2enjoy", $contents);
    }

    public function testShouldCompressFromUrl() {
        $path = tempnam(sys_get_temp_dir(), "tinify-php");
        $source = \Way2enjoy\fromUrl("https://raw.githubusercontent.com/way2enjoy2/tinify-php/master/test/examples/voormedia.png");
        $source->toFile($path);

        $size = filesize($path);
        $contents = fread(fopen($path, "rb"), $size);

        $this->assertGreaterThan(1000, $size);
        $this->assertLessThan(1500, $size);

        /* width == 137 */
        $this->assertContains("\0\0\0\x89", $contents);
        $this->assertNotContains("Copyright Way2enjoy", $contents);
    }

    public function testShouldResize() {
        $path = tempnam(sys_get_temp_dir(), "way2enjoy-php");
        self::$optimized->resize(array("method" => "fit", "width" => 50, "height" => 20))->toFile($path);

        $size = filesize($path);
        $contents = fread(fopen($path, "rb"), $size);

        $this->assertGreaterThan(500, $size);
        $this->assertLessThan(1000, $size);

        /* width == 50 */
        $this->assertContains("\0\0\0\x32", $contents);
        $this->assertNotContains("Copyright Way2enjoy", $contents);
    }

    public function testShouldPreserveMetadata() {
        $path = tempnam(sys_get_temp_dir(), "way2enjoy-php");
        self::$optimized->preserve("copyright", "creation")->toFile($path);

        $size = filesize($path);
        $contents = fread(fopen($path, "rb"), $size);

        $this->assertGreaterThan(1000, $size);
        $this->assertLessThan(2000, $size);

        /* width == 137 */
        $this->assertContains("\0\0\0\x89", $contents);
        $this->assertContains("Copyright Way2enjoy", $contents);
    }
}
