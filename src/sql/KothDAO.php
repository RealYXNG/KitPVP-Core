<?php

namespace Crayder\Core\sql;

use Crayder\Core\configs\KothConfig;
use Crayder\Core\Main;
use Crayder\Core\koth\KothArena;
use Crayder\Core\koth\KothManager;

class KothDAO{

	public static function load(){
		Main::getDatabase()->executeInsert("koths.init", []);

		Main::getDatabase()->executeSelect("koths.select", [], function(array $rows) : void{
			foreach($rows as $row){
				$arena = new KothArena($row["x1"], $row["z1"], $row["x2"], $row["z2"], $row["centreY"]);
				KothManager::addArena($arena);
			}

			Main::getInstance()->getLogger()->info("Loaded " . count($rows) . " KoTHs!");

			if(count(KothManager::$koths) == 0){
				KothManager::$kothDetails[1] = -1;
			}else{
				KothManager::$kothDetails[1] = time() + KothConfig::$repeat * 60 * 60;
			}
		});
	}

	public static function save(){
		// Reload the koths into the memory
		$koths = KothManager::$koths;

		$sql = <<<EOF
DELETE FROM KOTHS;
EOF;
		Main::$db->query($sql);

		foreach($koths as $koth){
			$x1 = $koth->getX1();
			$z1 = $koth->getZ1();
			$x2 = $koth->getX2();
			$z2= $koth->getZ2();
			$centreY = $koth->getCentreY();

			$sql = "INSERT INTO KOTHS(x1, z1, x2, z2, centreY) VALUES(:x1, :z2, :x2, :z2, :centreY);";

			$stmt = Main::$db->prepare($sql);
			$stmt->bindValue(":x1", $x1);
			$stmt->bindValue(":z1", $z1);
			$stmt->bindValue(":x2", $x2);
			$stmt->bindValue(":z2", $z2);
			$stmt->bindValue(":centreY", $centreY);

			$stmt->execute();
		}

		Main::getInstance()->getLogger()->info("Saved " . count($koths) . " KoTHs!");
	}

}