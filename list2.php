<?php

/*Lists all the user's programs

X-PETC-A: token
*/

include "functions.php";
init(false);

$sb=geturl("https://save.smilebasic.com/list2.php","X-PETC-A: ".$origid."\r\n");
$keyct=intval($response_headers["X-Petc-FileCount"]);
if($response_headers["http_code"]!=200){
	$sb="";
	$keyct=0;
}

$keys=getallkeys();
header("X-Petc-FileCount: ".(count($keys)+$keyct));

$out=$sb;
foreach($keys as $key=>$about){
	if($out!="" && substr($out,strlen($out)-1,1)!="\n"){
		$out.="\n";
	}
	$out.=$key."\t".$about["Filename"]."\tdate date\t".$about["filesize"]."\t3\t127";
}
echo($out);