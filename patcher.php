<?php
$urlbase="http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$urlbase=substr($urlbase,0,strrpos($urlbase,"/")+1);
$diff=strlen($urlbase)-strlen("https://save.smilebasic.com/");


if(!empty($_FILES["codebin"])){
	$f=file_get_contents($_FILES["codebin"]["tmp_name"]);
	$files=scandir(".");
	foreach($files as $filename){
		if(preg_match("/\.php/",$filename)){
			$find="https://save.smilebasic.com/".$filename;
			$find2="https://load.smilebasic.com/".$filename;
			if($diff>=0){$find.=str_repeat("\000",$diff);$find2.=str_repeat("\000",$diff);}
			$replace=$urlbase.$filename;
			if($diff<0){$replace.=str_repeat("\000",$diff*-1);}
			$f=str_replace($find,$replace,$f);
			$f=str_replace($find2,$replace,$f);
		}
	}
	header("Content-Disposition: attachment; filename=\"code.bin\"");
	echo($f);
}else{
?>
<html>
	<head>
		<title>code.bin patcher</title>
		<script></script>
	</head>
	<body>
		<form method="POST" action="" enctype="multipart/form-data">
			<input type="file" name="codebin">
			<input type="submit">
		</form>
	</body>
<?php
}
?>