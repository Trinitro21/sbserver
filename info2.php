<?php

/*Retrieves info about a key without downloading the file

X-PETC-A: token if no nnid
X-PETC-B: token if nnid
X-PETC-C: key
*/

include "functions.php";
init(true);

if(!isset($h["X-PETC-C"])){
	die();
}

$key=$h["X-PETC-C"];

fromkey(false);

echo("OK");