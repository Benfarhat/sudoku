<!DOCTYPE html>
<html lang="fr">
<head>
	<title>SUDOKU</title>
	<style>
		table{
			border:none;    
			border-collapse: collapse;
			border-spacing: 0;
			display: table;
		}
		td{
			border:1px solid #aaa;
			padding:8px;
		}
	</style>
</head>
<body><center>
<h1>Jeux du sudoku</h1>
<?php

global $table;
$table= array();

for($i = 1; $i <= 8; $i++) {
	for($j = 1; $j <= 8; $j++) {
		$table[$i][$j]=0;
	}
}
function valide($x,$y,$n){
	$valide=true;
	for($i = 0; $i < $x; $i++)
		if($table[$i][$y]==$n) $valide=false;
	for($i = 0; $i < $y; $i++)
		if($table[$x][$i]==$n) $valide=false;
	$a = $x % 3;
	$b = $y % 3;
	for($i = 0; $i <3; $i++)
		for($j = 0; $j <3; $j++)
			if($table[$a+$i][$b+$j]==$n) $valide=false;
	return $valide;
}

$table[0] = range(1, 9);
for($x = 1; $x <= 8; $x++) {
	for($y = 0; $y <= 8; $y++) {
		// Ces deux lignes permettent d'éviter un rand qui pourrait rester infini
		$valeur = range(1,9);
		shuffle($valeur);
		$i=0;
		do{
		$nbre=$valeur[$i++];
		} while (valide($x, $y,$nbre));
		$table[$x][$y]=$nbre;

	}
}
shuffle($table[1]);
print_r($table);
echo '<table>';
foreach(range('0', '8') as $y) {
    echo '<tr>';
    for($x = 0; $x <= 8; $x++) {
        echo '<td>'.$table[$x][$y].'</td>';
    }
    echo '</tr>';
}
echo '</table>';
?>
</center>
</body>
</html>