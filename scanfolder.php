<?php
/*
*
*	Scan Dossier
*
*	Copyright (c) 2013 Logicalway
*	Date creation : 01/04/13
*	Derniere modification : 12/06/19
*
*	source : http://www.bala-krishna.com/php-recursively-file-folder-scan
*
*/


// dossier de depart
$dir = $_SERVER["DOCUMENT_ROOT"]."/".dirname($_SERVER["SCRIPT_NAME"]);
$dir = str_replace("//","/",$dir);


// init tab
$tabf = array();
$level1 = count( explode("/",$dir) );
$niveau = 4;
$niveau += $level1;


// exclude
$exeption = array(
				"",
				".",
				"..",
				"stats",
				"cache",
				"tmp"
);


function list_recursive($dir,$niveau,$dirstart,$exeption,$tabf) { 
	$level2 = count( explode("/",$dir) );
	if( $dh = opendir($dir) ) {
		if( $level2 < $niveau ) {
			while(false !== ($entry = readdir($dh))) {
				if( array_search($entry,$exeption) ) {
					continue;
				}
				$path = str_replace("//","/",$dir."/".$entry);
				$tabf[] = filemtime($path).",".str_replace($dirstart,"",$path);
				if( is_dir($path) ) {
					$tabf = list_recursive($path,$niveau,$dirstart,$exeption,$tabf);
				}
			}
		}
		closedir($dh);
	}
	return $tabf;
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>scan folder</title>
<style type="text/css">
</style>
</head>
<body>

<?php

$tab = list_recursive($dir,$niveau,$dir,$exeption,$tabf);

rsort($tab);

foreach( $tab as $keyF => $valueF ) {

	if( preg_match("#,#",$valueF) ) {
		$mtab = explode(",",$valueF);
		$time = $mtab[0];
		$path = $mtab[1];
		echo date("Y-m-d H:m:s",$time)." - ".$path."<br />\n";
	}

}

?>

</body>
</html>
