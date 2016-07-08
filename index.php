<?php
require("vendor/autoload.php");
use App\Sudoku;

$sudoku = new Sudoku();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	<title>SUDOKU</title>
	<link href='//fonts.googleapis.com/css?family=Archivo+Narrow:400normal,400italic,700normal,700italic|Open+Sans:400normal|Oswald:400normal|Roboto:400normal|Lato:400normal|Merriweather:400normal|Fjalla+One:400normal|Open+Sans+Condensed:300normal|Source+Sans+Pro:400normal|Arimo:400normal|Raleway:400normal&subset=all' rel='stylesheet' type='text/css'><script type='text/javascript' src='https://apis.google.com/js/plusone.js'></script>
	<style>
	body {
		font-size: 13px;
		font-family: "Archivo Narrow";
		font-style: normal;
		font-weight: 400;
	}
	h1{
			text-transform:uppercase;
			text-align:center;
			font-family: "Archivo Narrow";
			font-weight: 700;
		}
	small{
			display:block;
			text-align:center
		}
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</head>
<body><center>
<h1>Jeux du sudoku</h1>
<table>
<tr>
<?php
for ($i=0;$i<3;$i++) {
echo "<td>";
$sudoku->display(6);
echo "<td>";
}
?>
</tr>
<tr>
<?php
for ($i=0;$i<3;$i++) {
echo "<td>";
$sudoku->display(48);
echo "<td>";
}
?>
</tr>
</table>
</center>
</body>
</html>