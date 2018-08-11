<?php

/*Used for publishing keys
X-PETC-A: token
X-PETC-B: key
X-PETC-C: unk, 1 works
*/

include "functions.php";
init(false);

if(!isset($h["X-PETC-B"])){
	die();
}

$key=$h["X-PETC-B"];

fromkey(false,2,false,"show2");
