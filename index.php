<?php include "functions.php";
if(!empty($_FILES["codebin"])){//patch code.bin
	$urlbase="http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$urlbase=substr($urlbase,0,strrpos($urlbase,"/")+1);
	$diff=strlen($urlbase)-strlen("https://save.smilebasic.com/");
	
	$f=file_get_contents($_FILES["codebin"]["tmp_name"]);
	$files=scandir(".");
	foreach($files as $filename){
		if(preg_match("/\.php/",$filename)){
			$find="https://save.smilebasic.com/".$filename;//there are two subdomains for some reason
			$find2="https://load.smilebasic.com/".$filename;
			if($diff>=0){$find.=str_repeat("\000",$diff);$find2.=str_repeat("\000",$diff);}//null pad whichever one is shorter
			$replace=$urlbase.$filename;
			if($diff<0){$replace.=str_repeat("\000",$diff*-1);}
			$f=str_replace($find,$replace,$f);
			$f=str_replace($find2,$replace,$f);
		}
	}
	header("Content-Disposition: attachment; filename=\"code.bin\"");
	die($f);//exit
}

if(isset($_SERVER["PATH_INFO"])){
	$path=$_SERVER["PATH_INFO"];
}else{$path="";}
if(strlen($path)==0){//not requesting info on any files, show main page ?>
<html>
	<head>
		<title>SmileBASIC Server</title>
	</head>
	<body>
		<h2>Projects:</h2>
		<?php
			$files=scandir(".");
			foreach($files as $key){
				if(!preg_match("/\.|[a-z]/",$key) && file_exists($key) && is_dir($key)){
					$c=getkeydata($key);
					echo "<a href='index.php/$key'>$key: ".$c["Filename"]."</a><br>";
				}
			}
		?>
		<hr>
		<h2>Patcher:</h2>
		<form method="POST" action="" enctype="multipart/form-data">
			<input type="file" name="codebin">
			<input type="submit">
		</form>
	</body>
</html>
<?php
	die();
}

$p=explode("/",substr($path,1));//which files are they requesting
$key=$p[0];
$files=scandir(".");
function bad(){
?>
<html>
	<head>
		<title>
			An error
		</title>
	</head>
	<body>
		Bad
	</body>
</html>
<?php
	die();//exit
}

if(preg_match("/\.|[a-z]/",$key) || !in_array($key,$files) || !is_dir($key)){bad();}//key doesn't exist=bad
$c=getkeydata($key);
$f=file_get_contents($key."/raw");
function s($s,$index,$length){
	$res=0;
	for($i=0;$i<$length;$i++){
		$res+=ord(substr($s,$index+$i,1))*pow(256,$i);
	}
	return $res;
}
function st($s,$index,$length){
	$res="";
	for($i=$index;$i<$index+$length;$i++){
		$ch=substr($s,$i,1);
		if(ord($ch)==0){
			break;
		}else{
			$res.=$ch;
		}
	}
	return $res;
}

function getfilenames($f){
	if(s($f,2,2)!=2){bad();}
	$ret=array();
	$s1=s($f,84,4);//at index 84 there is the number of files in the project
	$base=88;
	$currofs=$base+$s1*20;
	for($i=0;$i<$s1;$i++){
		$ret[$i]=array();
		$ret[$i]["size"]=s($f,$base+$i*20,4);
		$ret[$i]["name"]=st($f,$base+$i*20+4,14);
		$ret[$i]["offset"]=$currofs;
		$currofs+=$ret[$i]["size"];
	}
	return $ret;
}
function getfilebyname($f,$name){
	$names=getfilenames($f);
	for($i=0;$i<count($names);$i++){
		if($name==$names[$i]["name"]){
			return substr($f,$names[$i]["offset"],$names[$i]["size"]);
		}
	}
}
function formats($f,$filename){
	if(isset($_GET["raw"]) && !is_null($_GET["raw"])){//?raw -> just echo it and quit
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment;filename=\"$filename\"");
		die($f);
	}
	if(isset($_GET["txt"]) && !is_null($_GET["txt"])){//txt -> verify text file, just strip header and footer
		if(s($f,2,2)!=0){bad();}
		header("Content-type: text/plain");
		die(substr($f,80,strlen($f)-100));
	}
	if(isset($_GET["png"]) && !is_null($_GET["png"])){//png -> verify grp, parse image
		if(s($f,2,2)!=1){bad();}//check dat
		if(st($f,80,8)!="PCBN0001"){bad();}//check dat magic
		if(s($f,88,2)!=3){bad();}//check dat color
		if(s($f,90,2)!=2){bad();}//check 2d
		$dim=array(s($f,92,4),s($f,96,4));//get dimensions
		$im=imagecreatetruecolor($dim[1],$dim[0]);//make it
		imagealphablending($im,false);
		imagesavealpha($im,true);//yes there can be transparency
		$tr=imagecolorallocatealpha($im,0,0,0,127);//transparent color
		imagefill($im,0,0,$tr);//fill transparency
		for($x=0;$x<$dim[1];$x++){
			for($y=0;$y<$dim[0];$y++){
				$index=108+($x+$y*$dim[0])*2;//offset in file
				$col=s($f,$index,2);//get color data
				if(($col & 1)==0){continue;}//skip transparent pixels, the image is pre-filled with transparency
				$b=($col & 0b0000000000111110)/0b10*256/32;
				$g=($col & 0b0000011111000000)/0b1000000*256/32;
				$r=($col & 0b1111100000000000)/0b100000000000*256/32;
				$color=imagecolorexact($im,$r,$g,$b);//is it in the palette already
				if($color==-1){
					$color=imagecolorallocate($im,$r,$g,$b);//get in there
				}
				imagesetpixel($im,$x,$y,$color);//set the pixel in the image
			}
		}
		header("Content-type: image/png");//tell the browser that it's a png
		imagepng($im);//do the thing
		imagedestroy($im);
		die();
	}
}

$filepath=$key;
$filename=$c["Filename"];
function parsefilepath($re){
	global $filepath,$filename,$f,$p;
	if(isset($p[$re]) && strlen($p[$re])>0 && s($f,2,2)!=2){bad();}
	if(empty($p[$re])){
		formats($f,$filename);
	}else{
		$f=getfilebyname($f,$p[$re]);
		$filepath=$filepath."/".$p[$re];
		$filename=$p[$re];
		parsefilepath($re+1);
	}
}
parsefilepath(1);

function parse($f,$name,$path){
$t=s($f,2,2);
if($t==0){//this feels wrong
	$type="TXT";
}elseif($t==1){
	$type="DAT";
}elseif($t==2){
	$type="PRJ";
}else{
	$type="UNK";
}
?>
		<table>
			<tbody>
				<tr><td>Name:</td><td><?php echo $name; ?></td></tr>
				<tr><td>Formats:</td><td>
					<a href="<?php echo $path; ?>?raw">Raw</a>
					<?php if($type=="TXT"){echo '<a href="'.$path.'?txt">Text</a>';} ?>
					<?php if($type=="DAT" && s($f,88,2)==3){echo '<a href="'.$path.'?png">PNG</a>';} ?>
				</td></tr>
				<tr>
					<td>Type:</td>
					<td><?php echo $type; ?></td>
				</tr>
				<tr>
					<td>Icon:</td>
					<td><?php echo s($f,6,2); ?></td>
				</tr>
				<tr>
					<td>Size:</td>
					<td><?php echo s($f,8,4); ?></td>
				</tr>
<?php if($type=="PRJ"){ ?>
				<tr>
					<td valign="top">Files:</td>
					<td><?php 
$files=getfilenames($f);
foreach($files as $file){
	parse(substr($f,$file["offset"],$file["size"]),$file["name"],$path.($path==""?"":"/").$file["name"]);
	echo "<br>";
}
					?></td>
				</tr>
<?php }
if($type=="DAT"){
$t=s($f,88,2);
if($t==3){
	$dattype="COL";
}elseif($t==4){
	$dattype="INT";
}elseif($t==5){
	$dattype="REAL";
}else{
	$dattype="UNK";
}
$dim=s($f,90,2);
$dims=array();
$dimstring="";
for($i=0;$i<$dim;$i++){
	$dims[$i]=s($f,92+$i*4,4);
	$dimstring.=($i==0?"":"x").$dims[$i];
}
?>
				<tr>
					<td valign="top">DAT info:</td>
					<td>
						<table>
							<tbody>
								<tr>
									<td>Type:</td>
									<td><?php echo $dattype; ?></td>
								</tr>
								<tr>
									<td>Dimensions:</td>
									<td><?php echo $dimstring; ?></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
<?php } ?>
			</tbody>
		</table>
<?php
}

?>
<html>
	<head>
		<title>
			<?php echo "$filepath: $filename"; ?>
		</title>
	</head>
	<body>
		<h2><?php echo $filename; ?></h2>
		<?php parse($f,$filename,(strrpos($filepath,"/")==0?$filepath:substr($filepath,strrpos($filepath,"/")+1))); ?>
	</body>
</html>
