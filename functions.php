<?php

function id($value){
	$value=preg_replace("/\*/","=",$value);
	$value=preg_replace("/\./","/",$value);
	$value=preg_replace("/\-/","+",$value);
	$value=base64_decode($value);
	$value=bin2hex($value);
	$value=substr($value,0,strlen($value)-64);
	return $value;
}

function init($dontdieifnoid){
	global $h,$id;
	$h=getallheaders();
	if(isset($h["X-PETC-A"])){
		$id=id($h["X-PETC-A"]);
	}else{
		$id=id($h["X-PETC-B"]);
	}
	
	if(strlen($id)<64 && !$dontdieifnoid){
		die("Error");
	}
}

function fromkey($load){
	global $key,$filename;
	$c=getkeydata($key);
	if($c!=false){
		header("X-Petc-FileName: ".$c["Filename"]);
		header("X-Petc-UID: 0");
		header("X-Petc-Author: ");
		header("X-Petc-Date: ");
		header("X-Petc-Size: ".filesize("$key/raw"));
		header("X-Petc-IsSystem: 0");
		header("X-Petc-State: 3");
		header("X-Petc-RefCount: 0");
		if($load){
			header("Content-Disposition: attachment; filename=\"".$c["Filename"]."\"");
			echo(file_get_contents("$key/raw"));
		}
	}else{
		header("X-Petc-ErrorCode: 4");
		http_response_code(400);
		die("Error");
	}
}

function getkeydata($key){
	if(strlen($key)<15 && !preg_match("/\.|[a-z]/",$key) && file_exists($key) && is_dir($key)){
		$c=array();
		$config=file_get_contents("$key/about");
		$config=explode("\n",$config);
		foreach($config as $cfg){
			$tempvar=explode(": ",$cfg);
			$c[$tempvar[0]]=$tempvar[1];
		}
		$c["filesize"]=filesize("$key/raw");
		return $c;
	}else{
		return false;
	}
}

function getallkeys(){
	global $id;
	$files=scandir(".");
	$out=array();
	foreach($files as $k){
		$c=getkeydata($k);
		if($c!=false && $c["ID"]==$id){
			$out[$k]=$c;
		}
	}
	return $out;
}