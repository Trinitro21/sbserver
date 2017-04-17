<?php

/*Used for publishing keys
X-PETC-A: token
X-PETC-B: key
X-PETC-C: unk, 1 works
*/

include "functions.php";
init(false);

$key=$h["X-PETC-B"];

$c=getkeydata($key);
if($c!=false){
	header("X-Petc-FileName: ".$c["Filename"]);
	header("X-Petc-UID: ");
	header("X-Petc-Author: ");
	header("X-Petc-Date: ");
	header("X-Petc-IsSystem: 0");
	header("X-Petc-Size: ".$c["filesize"]);
	header("X-Petc-State: 3");
	header("X-Petc-RefCount: 0");
	echo("OK");
}else{
	header("X-Petc-ErrorCode: 2");
	http_response_code(400);
	die("Error");
}

