<?php

namespace Crayder\Core\util;

class TimeUtil{

	public static function formatTime(int $seconds, string $format1, string $format2) :string{
		$dtF = new \DateTime('@0');
		$dtT = new \DateTime("@$seconds");
		return $dtF->diff($dtT)->format($format1 . '%a ' . $format2 . 'days ' . $format1 . '%h ' . $format2 . 'hours ' . $format1 . '%i ' . $format2 . 'minutes ' . $format1 . '%s ' . $format2 . 'seconds');
	}

	public static function formatMS(int $seconds) :string{
		return sprintf('%02d:%02d', ($seconds/ 60), ($seconds % 60));
	}

}