<?php

namespace Crayder\Core\ui\classes;

use Crayder\Core\classes\TankClass;
use Crayder\Core\configs\ClassConfig;
use Crayder\Core\Main;
use Crayder\Core\Provider;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\entity\Attribute;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use Crayder\Core\classes\AssassinClass;
use Crayder\Core\classes\MedicClass;
use Crayder\Core\classes\ParadoxClass;

class ClassUI{

	public static function send(Player $player, int $type){
		switch($type){
			case 0:
				$form = new MenuForm(
					"Previewing | §6Tank Class",
					implode("\n", ClassConfig::$ui_tank),
					[
						new MenuOption("§cSelect Class")
					],
					function(Player $submitter2, int $selected2) use ($player) : void{
						if($selected2 == 0){
							$oldClass = Provider::getCustomPlayer($player)->getClass();

							if($oldClass instanceof TankClass){
								$player->sendMessage("§7[§c!§7] §cYou cannot select this class because you are already in this class.");
								return;
							}

							$class = new TankClass(0);
							Provider::getCustomPlayer($player)->setClass($class);

							$player->sendMessage(Main::$prefix . "You have successfully §cselected §rthe §6Tank §rclass!");
							$player->sendTitle("§6§lTank", "§cClass successfully selected!", 5, 40, 5);

							$sneaking = $player->isSneaking();
							$sprinting = $player->isSprinting();

							$player->setSneaking(false);
							$player->setSprinting(false);
							
							$player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->setValue($player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getDefaultValue() * $class::$movement_multiplier);

							$player->setSneaking($sneaking);
							$player->setSprinting($sprinting);
							
							if($oldClass instanceof ParadoxClass){
								$item = $player->getInventory()->getItem(7);

								$player->getInventory()->setItem(8, $item);
								$player->getInventory()->setItem(7, ItemFactory::air());
							}

							if($oldClass instanceof MedicClass){
								$item = $player->getInventory()->getItem(6);

								$player->getInventory()->setItem(8, $item);
								$player->getInventory()->setItem(7, ItemFactory::air());
								$player->getInventory()->setItem(6, ItemFactory::air());
							}

						}
					}
				);

				$player->sendForm($form);
				break;
			case 1:
				$form = new MenuForm(
					"Previewing | §3Paradox Class",
					implode("\n", ClassConfig::$ui_paradox),
					[
						new MenuOption("§cSelect Class")
					],
					function(Player $submitter2, int $selected2) use ($player) : void{
						if($selected2 == 0){
							$oldClass = Provider::getCustomPlayer($player)->getClass();

							if($oldClass instanceof ParadoxClass){
								$player->sendMessage("§7[§c!§7] §cYou cannot select this class because you are already in this class.");
								return;
							}

							$class = new ParadoxClass(1);
							Provider::getCustomPlayer($player)->setClass($class);

							$player->sendMessage(Main::$prefix . "You have successfully §cselected §rthe §3Paradox §rclass!");
							$player->sendTitle("§3§lParadox", "§cClass successfully selected!", 5, 40, 5);

							$sneaking = $player->isSneaking();
							$sprinting = $player->isSprinting();

							$player->setSneaking(false);
							$player->setSprinting(false);

							$player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->setValue($player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getDefaultValue() * $class::$movement_multiplier);

							$player->setSneaking($sneaking);
							$player->setSprinting($sprinting);

							$kit = Provider::getCustomPlayer($player)->getKit();

							if($oldClass instanceof MedicClass){
								if($kit != -1 && $kit != 0){
									$item = $player->getInventory()->getItem(6);

									$player->getInventory()->setItem(7, $item);
									$player->getInventory()->setItem(8, $class::$ender_pearls);

									$player->getInventory()->setItem(6, ItemFactory::air());
								}else{
									$player->getInventory()->setItem(7, ItemFactory::air());
									$player->getInventory()->setItem(8, $class::$ender_pearls);
								}
							}else{
								$item = $player->getInventory()->getItem(8);
								$item2 = $player->getInventory()->getItem(7);

								$player->getInventory()->setItem(7, $item);
								$player->getInventory()->setItem(8, $class::$ender_pearls);

								$player->getInventory()->addItem($item2);
							}
						}
					}
				);

				$player->sendForm($form);
				break;
			case 2:
				$form = new MenuForm(
					"Previewing | §5Medic Class",
					implode("\n", ClassConfig::$ui_medic),
					[
						new MenuOption("§cSelect Class")
					],
					function(Player $submitter2, int $selected2) use ($player) : void{
						if($selected2 == 0){
							$oldClass = Provider::getCustomPlayer($player)->getClass();

							if($oldClass instanceof MedicClass){
								$player->sendMessage("§7[§c!§7] §cYou cannot select this class because you are already in this class.");
								return;
							}

							$playerData = \iRainDrop\Clans\Main::getPlayerData($submitter2);
							if(!\iRainDrop\Clans\Main::clanExists($playerData->getClan())){
								$player->sendMessage("§7[§c!§7] §cThis class requires you to be in a Clan.");
								return;
							}

							$class = new MedicClass(2);
							Provider::getCustomPlayer($player)->setClass($class);

							$player->sendMessage(Main::$prefix . "You have successfully §cselected §rthe §5Medic §rclass!");
							$player->sendTitle("§5§lMedic", "§cClass successfully selected!", 5, 40, 5);

							$sneaking = $player->isSneaking();
							$sprinting = $player->isSprinting();

							$player->setSneaking(false);
							$player->setSprinting(false);

							$player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->setValue($player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getDefaultValue() * $class::$movement_multiplier);

							$player->setSneaking($sneaking);
							$player->setSprinting($sprinting);

							$kit = Provider::getCustomPlayer($player)->getKit();

							if($oldClass instanceof ParadoxClass){
								if($kit != -1 && $kit != 0){
									$item = $player->getInventory()->getItem(7);
									$item2 = $player->getInventory()->getItem(6);

									$player->getInventory()->setItem(6, $item);
									$player->getInventory()->setItem(7, $class::$ironIngot);
									$player->getInventory()->setItem(8, $class::$netherStar);

									$player->getInventory()->addItem($item2);
								}else{
									$item = $player->getInventory()->getItem(7);

									$player->getInventory()->setItem(7, $class::$ironIngot);
									$player->getInventory()->setItem(8, $class::$netherStar);

									$player->getInventory()->addItem($item);
								}
							}else{
								$item = $player->getInventory()->getItem(8);
								$item2 = $player->getInventory()->getItem(7);
								$item3 = $player->getInventory()->getItem(6);

								$player->getInventory()->setItem(6, $item);
								$player->getInventory()->setItem(7, $class::$ironIngot);
								$player->getInventory()->setItem(8, $class::$netherStar);

								$player->getInventory()->addItem($item2);
								$player->getInventory()->addItem($item3);
							}
						}
					}
				);

				$player->sendForm($form);
				break;
			case 3:
				$form = new MenuForm(
					"Previewing | §cAssassin Class",
					implode("\n", ClassConfig::$ui_assassin),
					[
						new MenuOption("§cSelect Class")
					],
					function(Player $submitter2, int $selected2) use ($player) : void{
						if($selected2 == 0){
							$oldClass = Provider::getCustomPlayer($player)->getClass();

							if($oldClass instanceof AssassinClass){
								$player->sendMessage("§7[§c!§7] §cYou cannot select this class because you are already in this class.");
								return;
							}

							Provider::getCustomPlayer($player)->setClass(new AssassinClass(3));

							$player->sendMessage(Main::$prefix . "You have successfully §cselected §rthe §cAssassin §rclass!");
							$player->sendTitle("§c§lAssassin", "§cClass successfully selected!", 5, 40, 5);

							$sneaking = $player->isSneaking();
							$sprinting = $player->isSprinting();

							$player->setSneaking(false);
							$player->setSprinting(false);

							$player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->setValue($player->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getDefaultValue());

							$player->setSneaking($sneaking);
							$player->setSprinting($sprinting);

							if($oldClass instanceof ParadoxClass){
								$item = $player->getInventory()->getItem(7);

								$player->getInventory()->setItem(8, $item);
								$player->getInventory()->setItem(7, ItemFactory::air());
							}

							if($oldClass instanceof MedicClass){
								$item = $player->getInventory()->getItem(6);

								$player->getInventory()->setItem(8, $item);
								$player->getInventory()->setItem(7, ItemFactory::air());
								$player->getInventory()->setItem(6, ItemFactory::air());
							}
						}
					}
				);

				$player->sendForm($form);
				break;
		}
	}

}