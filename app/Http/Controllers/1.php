<?php
for($i=1; ;$i++) {
	if($i % 5 == 1) {
		//第一次
		$t = $i - round($i/5) - 1;
		if($t % 5 == 1) {
			//第二次
			$r = $t - round($t/5) - 1;
			if($r % 5 == 1) {
				//第三次
				$x = $r - round($r/5) - 1;
				if($x % 5 == 1) {
					//第四次
					$y = $x - round($x/5) - 1;
					if($y % 5 == 1) {
						//第五次
						$s = $y - round($y/5) - 1;
						if($s % 5 == 1) {
						echo $i;
						break;
						}
					}
				}
			}
		}
	}
}
?>