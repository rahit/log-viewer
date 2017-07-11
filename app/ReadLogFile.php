<?php
/**
 * This file read logs from a specific path and return paginated
 * log data in json format
 * 
 * @author Tahsin Hassan Rahit <tahsin.rahit@gmail.com>
 */

class ReadLogFile {

    /** @var LIMIT lines per page */
    const LIMIT = 10;
    /** @var string valid path location regex */
    private $valid_path = '/[A-Za-z0-9\/:\.\\\\]+.log/';
    /** @var string file location */
    private $path;
    /** @var int page number to extract */
    private $page;
    /** @var array Lines read from file */
    private $lines;
    /** @var int total number of pages */
    private $total_page;
    /** @var SplFileObject File object to read */
    private $file;

    /**
     * Construct ReadLogFile object with file path
     *
     * @param string $path
     */
    public function __construct($path=null) {
        $this->page = 1;
        $this->total_page = 0;
        $this->lines = [];
        if ($path) {
            $this->setPath($path);
            $this->createFileObject();
        }
    }


    /**
     * Strip HTML tags and unecessary characters form path string
     * and extract full (real) path of the file
     *
     * @param string $str file path
     * @return string full path to the file
     */
    public function sanitizePath($str) {
        return realpath(strip_tags($str));
    }

    /**
     * Check for a valid path
     *
     * @param string  $path File Path
     * @return boolean
     */
    public function isValidPath($path) {
        $path = $this->sanitizePath($path);
        return preg_match($this->valid_path, $path) && !is_dir($path) && file_exists($path);
    }

    /**
     * Set file path. Before setting, validate and sanitize
     * file path
     *
     * @param string $path
     * @return void
     * @throws Exception Invalid Path
     */
    public function setPath($path) {
        if($this->isValidPath($path)) {
            $this->path = $this->sanitizePath($path);
        }
        else {
            throw new \Exception('Invalid Log File Path');
        }
    }

    /**
     * Get file location
     *
     * @return void
     */
    public function getPath() {
        return $this->path;
    }


    /**
     * Set Page number to extract
     *
     * @param int $page page number
     * @return void
     */
    public function setPage($page) {
        $this->page = (int)$page;
    }

    /**
     * Get page number to extract
     *
     * @return int page number
     */
    public function getPage() {
        return (int)$this->page;
    }


    /**
     * Create File object to read the streamed file and set it to
     * class property
     *
     * @return void
     * @throws Exception Permission Error
     */
    public function createFileObject() {
        if (is_readable($this->path)) {
            $this->file = new SplFileObject($this->path, 'r');
        }
        else {
            throw new \Exception('You do not have permission to read the file');
        }
    }

    /**
     * Get File Object
     *
     * @return SplFileObject
     */
    public function getFile() {
        return $this->file;
    }


    /**
     * Count total number of page for the file.
     *
     * @param SplFileObject $file
     * @return int number of page
     */
    public function countTotalPage($file=null){
        if($file == null) {
            $file = $this->file;
        }
        $file->seek($file->getSize());
        $total_line = $file->key();
        return (int)ceil($total_line / ReadLogFile::LIMIT);
    }
    
    /**
     * Get total number of pages in the file
     *
     * @return int
     */
    public function getTotalPage() {
        return (int)$this->total_page;
    }

    /**
     * Add new line from the file to array
     *
     * @param string $line a single line of log file
     * @return void
     */
    public function addLine($line) {
        $this->lines[] = $line;
    }
       
    /**
     * Get the lines read from the file
     *
     * @return array
     */
    public function getLines() {
        return $this->lines;
    }

    /**
     * Read the file from specified path and 
     * put specific lines in an array for later use
     * 
     * @return void
     */
    public function readFile() {
        $this->createFileObject();
        $this->total_page = $this->countTotalPage($this->file);
        if($this->page <= $this->total_page){
            $line = $this->page*ReadLogFile::LIMIT - ReadLogFile::LIMIT;
            $this->file->seek($line);
            for($i = 0; !$this->file->eof() && $i < ReadLogFile::LIMIT; $i++) {
                $this->addLine($this->file->current());
                $this->file->next();
            }
        }
    }

    /**
     * Return formatted result in json format
     *
     * @return json
     */
    public function getResult() {
        $this->lines = [ 'page'     =>  $this->page, 
                         'total_page' =>  $this->total_page, 
                         'logs'      =>  $this->lines ];
        $json = json_encode($this->lines);
        return $json;
    }

}
