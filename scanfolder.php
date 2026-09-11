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
				"_DEL",
				"stats",
				"tmp",
				"cache",
				"session",
				"product",
				"code"
);


function list_recursive($dir,$niveau,$dirstart,$exeption,$tabf) { 
	$level2 = count( explode("/",$dir) );
	if( $dh = opendir($dir) ) {
		if( $level2 < $niveau ) {
			while(false !== ($entry = readdir($dh))) {
				if( in_array($entry,$exeption) ) {
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
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Scan Folder</title>
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body {
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
			background-color: #f5f5f5;
			color: #333;
			padding: 20px;
		}
		.container {
			max-width: 1200px;
			margin: 0 auto;
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			padding: 30px;
		}
		h1 {
			margin-bottom: 20px;
			color: #2c3e50;
			font-size: 28px;
		}
		.file-list {
			font-family: "Courier New", monospace;
			font-size: 14px;
			line-height: 1.6;
		}
		.file-entry {
			padding: 8px 12px;
			border-bottom: 1px solid #eee;
			display: flex;
			gap: 20px;
		}
		.file-entry:hover {
			background-color: #f9f9f9;
		}
		.file-date {
			color: #666;
			white-space: nowrap;
			min-width: 120px;
		}
		.file-path {
			color: #0066cc;
			word-break: break-all;
		}
		.empty-message {
			text-align: center;
			color: #999;
			padding: 40px 20px;
		}
	</style>
</head>
<body>
<div class="container">
	<h1>📁 Scan Folder</h1>

<?php

$tab = list_recursive($dir,$niveau,$dir,$exeption,$tabf);

rsort($tab);

if( empty($tab) ) {
	echo '<div class="empty-message">Aucun fichier trouvé</div>';
} else {
	echo '<div class="file-list">';
	foreach( $tab as $keyF => $valueF ) {
		if( preg_match("#,#",$valueF) ) {
			$mtab = explode(",",$valueF);
			$time = $mtab[0];
			$path = $mtab[1];
			$date = date("Y-m-d H:i:s",$time);
			echo '<div class="file-entry"><span class="file-date">'.$date.'</span><span class="file-path">'.$path.'</span></div>'."\n";
		}
	}
	echo '</div>';
}

?>
</div>
</body>
</html>
