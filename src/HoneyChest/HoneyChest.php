<?php
namespace HoneyChest;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\Permission;
use pocketmine\inventory\ChestInventory;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\ConsoleCommandSender;

class HoneyChest extends PluginBase implements Listener{
	public function onEnable(){
		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder(), 0744, true);//プラグインフォルダがない場合にフォルダを作ります
		}
		if(!file_exists($this->getDataFolder() . "Config.yml")){
			$this->settings = new Config($this->getDataFolder() . "Config.yml", Config::YAML, array(//Configに書き込まれるデフォルトの値です
				"Configversion" => "0.4.2",
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
		$GLOBALS['TouchHoney'] = false;
		$GLOBALS['RemoveHoney'] = false;
	}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if(isset($args[0])){
			$param = strtolower($args[0]);
    			switch($param){
    				case "info":
	    				if($sender->hasPermission("honeychest.*","honeychest.info")){
	    					$sender->sendMessage(TextFormat::AQUA."Plugin Developer : rain318");
						$sender->sendMessage(TextFormat::AQUA."Plugin Version   : 0.4.2");
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
	   					break;
	    				}else{
	    					$sender->sendMessage(TextFormat::RED."このコマンドを使用する権限がありません。");
	    				}
					return true;
    					break;
    				case "set":
    					if($sender->hasPermission("honeychest.*","honeychest.set") and $sender instanceof Player){
						$sender->sendMessage(TextFormat::BLUE."ハニーチェスト化したいチェストをタップしてください。");
						$GLOBALS['TouchHoney'] = true;
					}else{
	    					$sender->sendMessage(TextFormat::RED."このコマンドを使用する権限がありません。");
	    				}
					return true;
	    				break;
				case "remove":
	    				if($sender->hasPermission("honeychest.*","honeychest.remove") and $sender instanceof Player){
						$sender->sendMessage(TextFormat::BLUE."削除したいハニーチェストをタップしてください。");
	    					$GLOBALS['RemoveHoney'] = true;
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
					$this->getServer()->broadcastMessage($player->getName() . $this->settings->get("BroadCaster"));
				}
			}
		}
	}
	
	public function onTouch(PlayerInteractEvent $event){
		if($GLOBALS['TouchHoney']){
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
					$this->chest->set($n + 1,$pos);
					$this->chest->set('num', $n + 1);
					$this->chest->save();
					$event->getPlayer()->sendMessage("ハニーチェスト化が完了しました。");
				}
				$GLOBALS['TouchHoney'] = false;
				$event->setCancelled();
			}else{
				$event->getPlayer()->sendMessage("チェストをタップしてください。");
			}
		}elseif($GLOBALS['RemoveHoney']){
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
				$GLOBALS['RemoveHoney'] = false;
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
