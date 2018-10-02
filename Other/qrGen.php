<?php
$generated = array();
for($i = 0; $i>=1; $i++){
	$num = rand(1, 100);
	if(!array_search($num, $generated)){
		array_push($generated, $num);
		echo "[". $i ."]" .$num . "</br>";
	}else{$i = $i - 1;}
}
echo rand(1, 100);
?>