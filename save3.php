<?php

/*Used for uploading files and projects
X-PETC-A: token
X-PETC-B: filename
X-PETC-C: unk, 0 works
X-PETC-D: filesize
X-PETC-E: region

file contents in body of request as chunked data
*/

include "functions.php";
init(false);

$filename=$h["X-PETC-B"];
$size=$h["X-PETC-D"];

//generate a key
do{
	$key="";
	$lower=[48,65];
	$upper=[57,90];
	$len=rand(3,8);
	for($s=0;$s<$len+1;$s=$s+1){
		$chartype=rand(0,1);
		$key.=chr(rand($lower[$chartype],$upper[$chartype]));
	}
}while(file_exists($key));

mkdir($key);
file_put_contents("$key/raw",file_get_contents("php://input"));
file_put_contents("$key/about",
	"ID: $id\n".
	"Filename: $filename"
);

header("X-Petc-Pubkey: $key");
header("X-Petc-UID: 0");
header("X-Petc-Rights: 1");
header("X-Petc-FileCount: 0");
header("X-Petc-MaxCount: 100");
header("X-Petc-FileSize: ".filesize("$key/raw"));
header("X-Petc-MaxSize: 134217728");

echo("OK");