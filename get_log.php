<?php
/**
 * This file recieves request from API 
 * and process it using ReadLogFile class
 * 
 * @author Tahsin Hassan Rahit <tahsin.rahit@gmail.com>
 */
require_once(dirname(__FILE__)."/app/ReadLogFile.php");

header('Content-type:application/json;charset=utf-8');
try {
    $read_log = new ReadLogFile();
    $read_log->setPath($_GET['path']);
    $read_log->setPage($_GET["page"]);
    $read_log->readFile();
    echo $read_log->getResult();
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'error' => array(
            'msg' => $e->getMessage(),
            'code' => $e->getCode(),
        ),
    ));
}

