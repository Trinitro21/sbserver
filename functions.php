<?php

function id($value){
	$orig=$value;
	$value=preg_replace("/\*/","=",$value);
	$value=preg_replace("/\./","/",$value);
	$value=preg_replace("/\-/","+",$value);
	$value=base64_decode($value);
	$value=bin2hex($value);
	$value=substr($value,0,strlen($value)-64);
	return $value;
}

function init($dontdieifnoid){
	global $h,$id,$origid,$origtype;
	$h=getallheaders();
	if(isset($h["X-PETC-A"])){
		$origid=$h["X-PETC-A"];
		$origtype="A";
	}else if(isset($h["X-PETC-B"])){
		$origid=$h["X-PETC-B"];
		$origtype="B";
	}else{
		if(!$dontdieifnoid){
			die("Error");
		}else{
			$origid="";
		}
	}
	
	$id=id($origid);
	if(strlen($id)<64 && !$dontdieifnoid){
		die("Error");
	}
}

function fromkey($load,$err,$fromsb,$urlloc){
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
		}else{
			echo("OK");
		}
	}else{
		if(!$fromsb){
			header("X-Petc-ErrorCode: ".$err);
			http_response_code(400);
			die("Error");
		}else{
			global $response_headers,$origtype,$origid;
			$sb=geturl("https://load.smilebasic.com/".$urlloc.".php","X-PETC-".$origtype.": ".$origid."\r\nX-PETC-C: ".$key."\r\n");
			if($response_headers["http_code"]!=200){
				header("X-Petc-ErrorCode: ".$err);
				http_response_code(400);
				die("Error");
			}else{
				foreach($response_headers as $name=>$val){
					if($name!="http_code" && !is_numeric($name)){
						header($name.": ".$val);
					}
				}
				if($load){
					echo($sb);
				}else{
					echo("OK");
				}
			}
		}
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

function geturl($url,$headers){
	$context=stream_context_create(array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		),
		"http"=>array(
			'method'=>"GET",
			'header'=>$headers
		)
	));
	$res=file_get_contents($url,false,$context);
	global $response_headers;
	$response_headers=array();
	for($s=0;$s<count($http_response_header);$s=$s+1){
		$t=explode(": ",$http_response_header[$s],2);
		if(preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#",$t[0],$out)){
			$response_headers["http_code"]=intval($out[1]);
		}
		if(isset($t[1])){
			$response_headers[$t[0]]=$t[1];
		}else{
			$response_headers[]=$t[0];
		}
	}
	return $res;
}