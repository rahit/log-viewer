<?php
require_once(dirname(__FILE__)."/../ReadLogFile.php");
use PHPUnit\Framework\TestCase;

/**
 * Test ReadLogFile Class
 * 
 * @covers ReadLogFile
 */
class ReadLogFileTest extends TestCase {

    private $test_log_file_name = 'demo.log';
    private $test_log_dir = 'app/Test/TestLogFiles';
    private $test_log_path;
    private $test_log_lines = 119977;

    public function setup() {
        $this->test_log_path = join(DIRECTORY_SEPARATOR, [$this->test_log_dir, $this->test_log_file_name]);
    }

    public function testInitValues(){
        $read_log_file = new ReadLogFile();
        $this->assertEmpty($read_log_file->getPath());
        $this->assertEmpty($read_log_file->getTotalPage());
        $this->assertEmpty($read_log_file->getLines());
        $this->assertEquals(1, $read_log_file->getPage());
    }

    public function testStringSanitization(){
        $read_log_file = new ReadLogFile();
        $this->assertEquals(__DIR__.'/TestLogFiles/demo.log', 
                            $read_log_file->sanitizePath($this->test_log_path));
    }

    public function testPathException(){
        $this->expectException(\Exception::class);
        $read_log_file = new ReadLogFile();
        $read_log_file->setPath('garbage');
    }

    public function testSetPath() {
        $read_log_file = new ReadLogFile();
        $read_log_file->setPath($this->test_log_path);
        $this->assertEquals($read_log_file->sanitizePath($this->test_log_path), $read_log_file->getPath());
    }

    public function testGetPage() {
        $read_log_file = new ReadLogFile();
        $read_log_file->setPage('4');
        $this->assertInternalType('int', $read_log_file->getPage());
        $this->assertEquals(4, $read_log_file->getPage());
    }

    public function testPathValidation() {
        $read_log_file = new ReadLogFile();
        $this->assertFalse($read_log_file->isValidPath('garbage'));
        $this->assertFalse($read_log_file->isValidPath('/not/real/path'));
        $this->assertFalse($read_log_file->isValidPath($this->test_log_dir));

        $this->assertTrue($read_log_file->isValidPath($this->test_log_path));
    }

    public function testFileObject() {
        $read_log_file = new ReadLogFile($this->test_log_path);
        $this->assertInstanceOf(SplFileObject::class, $read_log_file->getFile());
    }

    public function testCountTotalPage() {
        $read_log_file = new ReadLogFile($this->test_log_path);
        $total_page = (int)ceil($this->test_log_lines/ReadLogFile::LIMIT);
        $this->assertInternalType('int', $read_log_file->countTotalPage());
        $this->assertEquals($total_page, $read_log_file->countTotalPage());        
    }

    public function testAddingLine() {
        $read_log_file = new ReadLogFile($this->test_log_path);
        $read_log_file->addLine("Hello PropertyGuru");
        $this->assertContains("Hello PropertyGuru", $read_log_file->getLines());
        $this->assertCount(1, $read_log_file->getLines());

        $read_log_file->addLine("Test Case for PropertyGuru");
        $this->assertContains("Hello PropertyGuru", $read_log_file->getLines());
        $this->assertCount(2, $read_log_file->getLines());
    }

    public function testReadFile(){
        $read_log_file = new ReadLogFile($this->test_log_path);
        $read_log_file->readFile();
        $p = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Inde igitur, inquit, ordiendum est. Si longus, levis. Verum hoc idem saepe faciamus. Duo Reges: constructio interrete.\n";
        $this->assertContains($p, $read_log_file->getLines());
        $this->assertEquals($p, $read_log_file->getLines()[0]);
        $this->assertCount(ReadLogFile::LIMIT, $read_log_file->getLines());

        $total_page = (int)ceil($this->test_log_lines/ReadLogFile::LIMIT);
        $read_log_file->setPage($total_page);
        $read_log_file->readFile();
    }

    public function testGetResult() {
        $read_log_file = new ReadLogFile($this->test_log_path);
        $read_log_file->readFile();
        $result = $read_log_file->getResult();
        $this->assertInternalType('string', $result);
    }


}
