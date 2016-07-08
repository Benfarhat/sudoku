<?php
namespace App;

class Sudoku 
{
	public $table;
	public $table_a_resoudre;
	public $zone;
	public $dimension;
	public $stat;
	public $difficulte;
	public $max_iteration;
	
	public function debug($data)
	{
		echo '<pre>' . var_export($data, true) . '</pre>';
	}
	
	public function __construct($dimension=3,$difficulte=1)
	{
		$this->zone = $dimension;
		$this->dimension = $dimension * $dimension;
		if (($difficulte < 1) || ($difficulte > 5)) $difficulte = 2;
		$this->difficulte = (int) ($this->dimension * $this->dimension / 6) * $difficulte;
		$this->table = array();
		$this->table_a_resoudre = array();
		// Tout le script fonctionne à base d'itération aléatoire, si au bout de 10 000 on a toujours rien ...  	
		$this->max_iteration = 10000;
	}
	
	public function display($width = 16)
	{
		$this->generer();
		$this->preparer();
		$str = <<<EOD
<script>
i = 0;
$(document).ready(function(){
    $("input").change(function(){
	val = $(this).attr('data-value');
	if($(this).val() == val) {
        $(this).parent('td').removeClass('invalide');
        $(this).removeClass('invalide');
        $(this).parent('td').addClass('valide');
        $(this).addClass('valide');
	} else {
        $(this).parent('td').removeClass('valide');
        $(this).removeClass('valide');
        $(this).parent('td').addClass('invalide');
        $(this).addClass('invalide');
	}
    });
});
</script>
<style>
		table{
			border:none;    
			border-collapse: collapse;
			border-spacing: 0;
			display: table;
			overflow: hidden;
		}
		input.inp_sudoku{
			border:none;
			width:{$width}px;
			height:{$width}px;
			text-align:center;
		}
		table.tbl_sudoku td{
			border:1px solid #aaa;
			width:{$width}px;
			height:{$width}px;
			line-height:{$width}px;
			text-align:center;
		}
		.valide{
			color:#16a085!important;
		}
		.invalide{
			color:#c0392b!important;
		}
		table.tbl_sudoku td:nth-child({$this->zone}n+0) {
		border-right:4px solid #aaa;
		}
		table.tbl_sudoku td:nth-child(1) {
		border-left:4px solid #aaa;
		}
		table.tbl_sudoku tr:nth-child({$this->zone}n+0) {
		border-bottom:4px solid #aaa;
		}
		table.tbl_sudoku tr:nth-child(1) {
		border-top:4px solid #aaa;
		}
		/* Trick from css trick https://css-tricks.com/simple-css-row-column-highlighting/ */
		table.tbl_sudoku tr:hover {
		  background-color: #f5f5f5;
		}
		table.tbl_sudoku td, th {
		  position: relative;
		}
		table.tbl_sudoku td:hover::after,
		table.tbl_sudoku th:hover::after {
		  content: "";
		  position: absolute;
		  background-color: #f5f5f5;
		  left: 0;
		  top: -5000px;
		  height: 10000px;
		  width: 100%;
		  z-index: -1;
		}
</style>
<small>{$this->stat}</small>
EOD;
		echo $str;
		echo '<table class="tbl_sudoku">';
		for($y = 0; $y <= ($this->dimension-1); $y++) {
			echo '<tr>';
			for($x = 0; $x <= ($this->dimension-1); $x++) {
				if ($this->table_a_resoudre[$x][$y]=='') 
					echo '<td data-x="'.$x.'" data-y="'.$y.'" data-value="'.$this->table[$x][$y].'"><input class="inp_sudoku" type="number" min="1" max="'.$this->dimension.'" data-value="'.$this->table[$x][$y].'"/></td>';
				else
					echo '<td data-x="'.$x.'" data-y="'.$y.'" data-value="'.$this->table[$x][$y].'">'.$this->table_a_resoudre[$x][$y].'</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}
	
	public function remplir()
	{
		$echec=false;
		for ($i = 1; $i < $this->dimension; $i++) {
			
			$temp = range(1, $this->dimension);
			shuffle($temp);
			/*
			echo "<hr>";
			foreach($temp as $v) echo $v." ";
			echo "<br>";
			*/
				$pos = range(0, $this->dimension-1);
				shuffle($pos);
				for ($j = 0; $j < $this->dimension; $j++) {		
					$break = false;
					$rempli = 0;
					$jj = $pos[$j];
					foreach($temp as $k => $v) {
						if ($this->valide($i,$jj,$v)) {
						if ($this->table[$i][$jj] ==0) {
							$this->table[$i][$jj] = $v;
						}
						$rempli++;
						$break = true;
						unset($temp[$k]);
						break;
						//break;
						}
						if($break) break;
					}
					if ($this->table[$i][$jj] == 0) {
						$echec = true;
						break;
					}
				}
		}
		return($echec);
	}
	
	public function valide($x,$y,$n)
	{
		$valide=true;
		// on test la colonne
		for ($i = 0; $i < $x; $i++) {
			if($this->table[$i][$y]==$n) $valide=false;
		}
		// on test la ligne
		for ($i = 0; $i < $y; $i++) {
			if($this->table[$x][$i]==$n) $valide=false;
		}
		// on défini la position dans la zone puis on la test
		$a = (int) ($x / $this->zone);
		$b = (int) ($y / $this->zone);
		//echo "$a ----- $b<br>";
		$aa = $a * $this->zone;
		$bb= $b * $this->zone;
		//echo ">> $aa ----- $bb<br>";
		for($i = 0; $i < $this->zone; $i++)
			for($j = 0; $j < $this->zone; $j++) {
				//echo "test $i - $j - ".$this->zone." $n<br>";
				if($this->table[$aa+$i][$bb+$j]==$n) $valide=false;
			}	
			
		// on retourne la résultat final
		return $valide;
	}
	
	
	public function generer()
	{
		$debut = microtime();

		$essai = 0;
		do {
			$essai++;
			// on génére la première colonne
			$this->table[0] = range(1, $this->dimension);
			shuffle($this->table[0]);
			// on rempli le reste à 0
			for($i = 1; $i <= ($this->dimension-1); $i++) {
				for($j = 0; $j <= ($this->dimension-1); $j++) {
					$this->table[$i][$j]=0;
				}
			}
		} while ($this->remplir() && $essai < $this->max_iteration );
		switch($difficulte){
		
		}
		$fin = microtime();
		$debut = explode(" ",$debut);
		$fin = explode(" ",$fin);
		$temps = round(($fin[1] - $debut[1] + $fin[0] - $debut[0]),2);
		$this->stat = "Généré en $temps secondes via $essai essais.<br>Nombre de cases à trouvées: {$this->difficulte}";	
	}
	
	public function preparer()
	{
		$this->table_a_resoudre = $this ->table;
		$masque = array_fill(0, $this->dimension * $this->dimension, 1);
		$n=0;
		do {
		$hasard = rand(0, ($this->dimension * $this->dimension) - 1);
		if ($masque[$hasard] == 1) {
			$masque[$hasard] = 0;
			$n++;
			$this->table_a_resoudre[(int) $hasard % $this->dimension][(int) floor($hasard / $this->dimension)]='';
		}
		
		} while($n < $this->difficulte);
	}
}