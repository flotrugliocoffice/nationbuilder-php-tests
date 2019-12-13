<?php
/**
 * Created by PhpStorm.
 * User: francescolotruglio
 * Date: 2019-12-13
 * Time: 01:41
 */

require_once 'constants.php';

function getHeaderList()
{
    $headerList = [];
    foreach ($_SERVER as $name => $value) {
        if (preg_match('/^HTTP_/', $name)) {
            // convert HTTP_HEADER_NAME to Header-Name
            $name = strtr(substr($name, 5), '_', ' ');
            $name = ucwords(strtolower($name));
            $name = strtr($name, ' ', '-');
            // add to list
            $headerList[$name] = $value;
        }
    }
    return $headerList;
}

$data = sprintf(
    "%s %s %s\n\nHTTP headers:\n",
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    $_SERVER['SERVER_PROTOCOL']
);
foreach (getHeaderList() as $name => $value) {
    $data .= $name . ': ' . $value . "\n";
}
$data .= "\nRequest body:\n";


$path = __DIR__;
$fileTarget = $path . "/hookslog/" . time() . "-logs.json";
//$myfile = fopen($path."/hookslog/" . time() . "-logs.json", "wb+") or die("Unable to open file!");
//fwrite($myfile, "Write payload for further usages\n\r".print_r($_REQUEST, true));
file_put_contents(
    $fileTarget,
    $data .
    print_r($_REQUEST, true) .
    "\n*************************************************************\n" .
    file_get_contents('php://input') . "\n"
);
/* do some stuff with received data...*/


