<?php
echo '{';
$r = 0;
foreach($requests as $rk => $rv){
	if(trim($requests[$rk]) != ''){
		if($r>0) echo ', ';
		echo '"'.$rk.'" : "'.$rv.'"';
		$r++;
	}
}
echo '}';