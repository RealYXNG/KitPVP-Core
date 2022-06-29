<?php

namespace Crayder\Core\util;

class ChanceUtil{

	public static function getEvent(array $events) {
		$random = rand(0, 100);

		$keys = array_keys($events);

		foreach($events as $key => $chance) {
			$i = array_search($key, $keys);

			if($i > 0){
				$c = $i;
				$sum = 0;

				while($c > 0) {
					if($c != $i){
						$sum += $events[$keys[$c]];
					}
					$c--;
				}

				if($random >= $sum && $random <= ($sum + $chance)) {
					return $key;
				}
			}

			else {
				if($random <= $chance) {
					return $key;
				}
			}
		}

		return self::getEvent($events);
	}

}