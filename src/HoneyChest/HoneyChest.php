<?php

namespace HoneyChest;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\inventory\ChestInventory;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\inventory\InventoryOpenEvent;

class HoneyChest extends PluginBase implements Listener{

	public function onEnable(){
		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder(), 0744, true);//プラグインフォルダがない場合にフォルダを作ります
		}
		if(!file_exists($this->getDataFolder() . "Config.yml")){
			$this->settings = new Config($this->getDataFolder() . "Config.yml", Config::YAML, array(//Configに書き込まれるデフォルトの値です
				"Configversion" => "0.4.3",
				"BroadCaster" => "さんがハニーチェストを開きました。",
				"Action" => "kick",
				"Command" => null,
				"License" => "false",
			));
		}else{
			$this->settings = new Config($this->getDataFolder() . "Config.yml", Config::YAML, array());
		}
		if(!file_exists($this->getDataFolder() . "Chests.yml")){
			$this->chest = new Config($this->getDataFolder() . "Chests.yml", Config::YAML, array('num' => 0));
		}else{
			$this->chest = new Config($this->getDataFolder() . "Chests.yml", Config::YAML, array());
		}
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->GLOBALS['TouchHoney'] = [];
		$this->GLOBALS['RemoveHoney'] = [];
		$this->getLogger()->info(TextFormat::AQUA."HoneyChestPluginがロードされました。");
		if($this->settings->get('License') != true){
			$this->getLogger()->info(TextFormat::RED."Config.ymlのLicenseをtrueにして下さい。");
			$this->getLogger()->info(TextFormat::RED."プラグインを無効化します…");
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if(isset($args[0])){
			$param = strtolower($args[0]);
    			switch($param){
    				case "info":
	    				if($sender->hasPermission("honeychest.*","honeychest.info")){
	    					$sender->sendMessage(TextFormat::AQUA."Plugin Developer : mcpekatsuo(Hmy2001)");
						$sender->sendMessage(TextFormat::AQUA."Plugin Version   : 0.4.3");
	    				}else{
	    					$sender->sendMessage(TextFormat::RED."このコマンドを使用する権限がありません。");
	 				}
					return true;
	    				break;
    				case "help":
	    				if($sender->hasPermission("honeychest.*","honeychest.help")){
						$sender->sendMessage(TextFormat::GREEN."/hc info    >>>  Pluginの情報を表示します。");
	    					$sender->sendMessage(TextFormat::GREEN."/hc help    >>>  Pluginのコマンド一覧を表示します。");
	    					$sender->sendMessage(TextFormat::GREEN."/hc set     >>>  HoneyChestの登録に追加するチェストを選択します。");
	    					$sender->sendMessage(TextFormat::GREEN."/hc remove  >>>  HoneyChestの登録を解除するチェストを選択します。");
	    					$sender->sendMessage(TextFormat::GREEN."/hc reload  >>>  HoneyChest PluginのConfig.ymlを再読み込みします。");
	    				}else{
	    					$sender->sendMessage(TextFormat::RED."このコマンドを使用する権限がありません。");
	    				}
					return true;
    					break;
    				case "set":
    					if($sender->hasPermission("honeychest.*","honeychest.set")){
    						if($sender instanceof Player){
							$sender->sendMessage(TextFormat::BLUE."ハニーチェスト化したいチェストをタップしてください。");
							$this->GLOBALS['TouchHoney'][$sender->getName()] = true;
    						}else{
    							$sender->sendMessage(TextFormat::RED."このコマンドはゲーム内でのみ実行できます。");
    						}
					}else{
	    					$sender->sendMessage(TextFormat::RED."このコマンドを使用する権限がありません。");
	    				}
					return true;
	    				break;
				case "remove":
	    				if($sender->hasPermission("honeychest.*","honeychest.remove")){
	    					if($sender instanceof Player){
							$sender->sendMessage(TextFormat::BLUE."削除したいハニーチェストをタップしてください。");
	    						$this->GLOBALS['RemoveHoney'][$sender->getName()] = true;
	    					}else{
	    						$sender->sendMessage(TextFormat::RED."このコマンドはゲーム内でのみ実行できます。");
	    					}
					}else{
    						$sender->sendMessage(TextFormat::RED."このコマンドを使用する権限がありません。");	    						break;
	    				}
					return true;
	    				break;
				case "reload":
	    				if($sender->hasPermission("honeychest.*","honeychest.reload")){
	    					$this->onEnable();
					}else{
	    					$sender->sendMessage(TextFormat::RED."このコマンドを使用する権限がありません。");
	    				}
					return true;
	    				break;
    				default:
	    				if($sender->hasPermission("honeychest.*","honeychest.info","honeychest.help","honeychest.set","honeychest.remove","honeychest.reload")){
	    					$sender->sendMessage(TextFormat::YELLOW."コマンドが見つかりません。/hc help でコマンドの一覧を表示してください。");
						break;
    					}else{
	    					$sender->sendMessage(TextFormat::RED."このコマンドを使用する権限がありません。");
	    				}
	    			break;
    				}
			}
	}
	
	public function onPickup(InventoryOpenEvent $event){
		if($event->getInventory() instanceof ChestInventory){
			$player = $event->getPlayer();
			$chest = $event->getInventory()->getHolder();
			$x = $chest->getX();
			$y = $chest->getY();
			$z = $chest->getZ();
			for($n = 1;$n <= $this->chest->get('num');++$n){
				$cp = $this->chest->get($n);
				if($x ==$cp[0] && $y ==$cp[1] && $z ==$cp[2] and !$player->hasPermission("honeychest.*","honeychest.exception")){
		 			if(is_null($this->settings->get("Action"))){
						$this->getLogger()->info("ハニーチェスト作動時の動作が設定されていません。");
					}else{
						switch(strtolower($this->settings->get("Action"))){
							case "kick":
								$player->kick("ハニーチェストを開けたため");
								break;
							case "ban":
								$player->setBanned(true);
								break;
							case "cmd":
								$cmd = $this->settings->get("Command");
								if(!isset($cmd)){
									$this->getLogger()->info("ハニーチェスト作動時のコマンドが設定されていません。");
								}else{
									$this->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
								}
						}
					}
					if($this->settings->get("BroadCaster") != "none"){
						$this->getServer()->broadcastMessage($player->getName() . " " . $this->settings->get("BroadCaster"));
					}
				}
			}
		}
	}

	public function onTouch(PlayerInteractEvent $event){
		if($GLOBALS['TouchHoney'][$event->getPlayer()->getName()]){
			if($event->getBlock()->getID() == 54){
				$IsHoney = false;
				$chest = $event->getBlock();
				$x = $chest->getX();
				$y = $chest->getY();
				$z = $chest->getZ();
				$n = $this->chest->get('num');
				for($n = 1;$n <= $this->chest->get('num');++$n){
					$cp = $this->chest->get($n);
					if($x ==$cp[0] && $y ==$cp[1] && $z ==$cp[2]){
						$event->getPlayer()->sendMessage("そのチェストはすでにハニーチェストです。");
						$IsHoney = true;
					}
				}
				if(!$IsHoney){
					$this->chest->set($n + 1,array($x,$y,$z));
					$this->chest->set('num', $n + 1);
					$this->chest->save();
					$event->getPlayer()->sendMessage("ハニーチェスト化が完了しました。");
				}
				$this->GLOBALS['TouchHoney'][$event->getPlayer()->getName()] = false;
				$event->setCancelled();
			}else{
				$event->getPlayer()->sendMessage("チェストをタップしてください。");
			}
		}elseif($this->GLOBALS['RemoveHoney'][$event->getPlayer()->getName()]){
			if($event->getBlock()->getID() == 54){
				$num = $this->chest->get('num');
				$player = $event->getPlayer();
				$chest = $event->getBlock();
				$x = $chest->getX();
				$y = $chest->getY();
				$z = $chest->getZ();
				for($n = 1;$n <= $num;++$n){
					$cp = $this->chest->get($n);
					if($x ==$cp[0] && $y ==$cp[1] && $z ==$cp[2]){
		 				$player->sendMessage($n . "番のハニーチェストを通常チェストに戻します。");
						break;
					}
				}
				
				$this->chest->set('num', $num - 1);
				for($n++;$n <= $num;++$n){
					$this->chest->set($n - 1,$this->chest->get($n));
				}
				$this->chest->remove($num);
				$this->chest->save();
				$this->GLOBALS['RemoveHoney'][$sender->getName()] = false;
				$event->setCancelled();
			}else{
				$event->getPlayer()->sendMessage("チェストをタップしてください。");
			}
		}
	}
	
	public function onBreak(BlockBreakEvent $event){
		$block = $event->getBlock();
		if ($block->getID() == 54){
			$x = $block->getX();
			$y = $block->getY();
			$z = $block->getZ();
			for($n = 1;$n <=  $this->chest->get('num');++$n){
				$cp = $this->chest->get($n);
				if($x ==$cp[0] && $y ==$cp[1] && $z ==$cp[2]){
		 			$event->setCancelled();
					break;
				}
			}
		}
	}
}