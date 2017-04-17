<?php

/*Lists all the user's programs

X-PETC-A: token
*/

include "functions.php";
init(false);

$keys=getallkeys();

header("X-Petc-FileCount: ".count($keys));

$out="";
foreach($keys as $key=>$about){
	if($out!=""){
		$out.="\n";
	}
	$out.=$key."\t".$about["Filename"]."\tdate date\t".$about["filesize"]."\t3\t127";
}
echo($out);