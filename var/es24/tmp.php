<?php
$stats = array();
foreach (range(1,375) as $q) {
	$stats[$q] = array();
	foreach (range(1,350) as $c) {
		$stats[$q][$c] = array(
			'lu' => [], 
			'lt' => 0, 
			'mu' => [], 
			'mt' => 0, 
			'hu' => [], 
			'ht' => 0, 
		);
	}
}

for ($i = 0;$i < 100000;$i++) {
	$u = mt_rand(10000,99999);
	$q = mt_rand(1,375);
	
	$c = mt_rand(1,350);
	$stats[$q][$c]['lu'][$u] = 1;
	$stats[$q][$c]['lt']++;
	
	$c = mt_rand(1,350);
	$stats[$q][$c]['mu'][$u] = 1;
	$stats[$q][$c]['mt']++;
	
	$c = mt_rand(1,350);
	$stats[$q][$c]['mu'][$u] = 1;
	$stats[$q][$c]['mt']++;
	
	$c = mt_rand(1,350);
	$stats[$q][$c]['mu'][$u] = 1;
	$stats[$q][$c]['mt']++;
	
	$c = mt_rand(1,350);
	$stats[$q][$c]['mu'][$u] = 1;
	$stats[$q][$c]['mt']++;
	
	$c = mt_rand(1,350);
	$stats[$q][$c]['mu'][$u] = 1;
	$stats[$q][$c]['mt']++;
	
	$c = mt_rand(1,350);
	$stats[$q][$c]['hu'][$u] = 1;
	$stats[$q][$c]['ht']++;
}


