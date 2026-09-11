<?php
/*
*
*	Scan Dossier
*
*	Copyright (c) 2013 Logicalway Sàrl
*	Date creation : 01/04/13
*	Derniere modification : 01/09/26
*
*	source : http://www.bala-krishna.com/php-recursively-file-folder-scan
*
*/


// dossier de depart
$dir = realpath($_SERVER["DOCUMENT_ROOT"]."/".dirname($_SERVER["SCRIPT_NAME"]));


// nombre de niveaux listes sous $dir
$niveau = 4;


// exclude en cles : test isset() au lieu d'un parcours in_array()
$exeption = array_flip(array(
	"_DEL",
	"stats",
	"tmp",
	"cache",
	"session",
	"product",
	"code"
));


// $tabf par reference : evite de recopier le tableau a chaque appel
function list_recursive($dir,$profondeur,$cut,$exeption,array &$tabf) {
	if( $profondeur < 1 || !($dh = opendir($dir)) ) {
		return;
	}
	while( false !== ($entry = readdir($dh)) ) {
		if( $entry === "." || $entry === ".." || isset($exeption[$entry]) ) {
			continue;
		}
		$path = $dir."/".$entry;
		$tabf[] = array(filemtime($path), substr($path,$cut));
		if( is_dir($path) ) {
			list_recursive($path,$profondeur - 1,$cut,$exeption,$tabf);
		}
	}
	closedir($dh);
}


$files = array();

if( $dir !== false ) {

	list_recursive($dir,$niveau,strlen($dir),$exeption,$files);

	// plus recent d'abord, puis par chemin pour un ordre stable a mtime egal
	usort($files, function ($a,$b) {
		return ($b[0] <=> $a[0]) ?: strcmp($a[1],$b[1]);
	});
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

if( empty($files) ) {
	echo '<div class="empty-message">Aucun fichier trouvé</div>';
} else {
	$rows = array();
	foreach( $files as $f ) {
		$rows[] = '<div class="file-entry">'
			.'<span class="file-date">'.date("Y-m-d H:i:s",$f[0]).'</span>'
			.'<span class="file-path">'.htmlspecialchars($f[1],ENT_QUOTES,"UTF-8").'</span>'
			.'</div>';
	}
	echo '<div class="file-list">'."\n".implode("\n",$rows)."\n".'</div>';
}

?>
</div>
</body>
</html>
