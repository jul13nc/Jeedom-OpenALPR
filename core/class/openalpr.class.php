<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class openalpr extends eqLogic {
	public static function cron() {
		foreach(eqLogic::byType('openalpr') as $Equipement){ 
			$Equipement->checkAndUpdateCmd('*',false);
			switch($Equipement->getConfiguration('UpdateMode')){
				case'toogle':
				break;
				case'vue':
				default:
					foreach($Equipement->getCmd() as $Commande){ 
						if(is_object($Commande) && $Commande->getLogicalId()!='lastPlate' && $Commande->execCmd()){						
							$Commande->updateState(false);	
						}
					}
				break;
			}
		}
    }
    public function postSave() {
		self::addCommande($this,'Etat du groupe','*');
		self::addCommande($this,'Dernier déclencheur','lastPlate','info','string');
		self::addCommande($this,'Détection manuel','manual','action','message');
	}
 	public static function ConfigOpenAlpr() {
		$file='/etc/openalpr/openalpr.conf';
		//if (config::byKey('openParam','openalpr')){
			/*$fp = fopen($file,"w+");
			fputs($fp,'ocr_img_size_percent = '.config::byKey('ocr_img_size_percent','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'state_id_img_size_percent = '.config::byKey('state_id_img_size_percent','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'prewarp ='.config::byKey('prewarp','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'max_plate_width_percent = '.config::byKey('max_plate_width_percent','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'max_plate_height_percent = '.config::byKey('max_plate_height_percent','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'detection_iteration_increase = '.config::byKey('detection_iteration_increase','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'detection_strictness = '.config::byKey('detection_strictness','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'max_detection_input_width = '.config::byKey('max_detection_input_width','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'max_detection_input_height = '.config::byKey('max_detection_input_height','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'detector = '.config::byKey('detector','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'must_match_pattern = '.config::byKey('must_match_pattern','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'skip_detection = '.config::byKey('skip_detection','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'max_plate_angle_degrees = '.config::byKey('max_plate_angle_degrees','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'ocr_min_font_point = '.config::byKey('ocr_min_font_point','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'postprocess_min_confidence = '.config::byKey('postprocess_min_confidence','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'postprocess_confidence_skip_level = '.config::byKey('postprocess_confidence_skip_level','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_general         = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_timing          = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_detector        = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_state_id        = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_plate_lines     = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_plate_corners   = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_char_segment    = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_char_analysis   = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_color_filter    = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_ocr             = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_postprocess     = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_show_images     = '.config::byKey('debug','openalpr'));
			fputs($fp, "\n");
			fputs($fp,'debug_pause_on_frame  = '.config::byKey('debug','openalpr'));
			fclose($fp);*/
		//}else{
			exec('sudo rm '.$file);
			exec('sudo touch '.$file);
			exec('sudo chmod 777 '.$file);
		//}
		$file='/etc/openalpr/alprd.conf';
		$fp = fopen($file,"w+");
		fputs($fp,'[daemon]');
		fputs($fp, "\n");
		fputs($fp,'country = eu');
		fputs($fp, "\n");
		fputs($fp,'site_id = Jeedom');
		fputs($fp, "\n");
		$Cameras=config::byKey('configuration','openalpr');
		if(is_array($Cameras['cameraUrl'])){
			foreach($Cameras['cameraUrl'] as $key => $AlprCamera){
				if($AlprCamera!=''){
					fputs($fp,'stream ='. self::getUrl($key));
					fputs($fp, "\n");
				}
			}
		}else{
			fputs($fp,'stream ='. self::getUrl(""));
			fputs($fp, "\n");
		}
		fputs($fp,'topn = 10');
		fputs($fp, "\n");
		fputs($fp,'store_plates = '.config::byKey('snapshot','openalpr'));
		fputs($fp, "\n");
		if(!file_exists(config::byKey('SnapshotFolder','openalpr'))){
			exec('sudo mkdir -p '.config::byKey('SnapshotFolder','openalpr'));
			exec('sudo chmod 777 -R '.config::byKey('SnapshotFolder','openalpr'));
		}
		fputs($fp,'store_plates_location = '.config::byKey('SnapshotFolder','openalpr'));
		fputs($fp, "\n");
		fputs($fp,'upload_data = 1');
		fputs($fp, "\n");
		fputs($fp,'upload_address = '.network::getNetworkAccess('internal','','',false).'/plugins/openalpr/core/php/Detect.php');
		fclose($fp);
		self::deamon_stop();
		
	}
	public static function deamonRunning() {
		$result = exec("ps aux | grep alprd | grep -v grep | awk '{print $2}'");
		if($result != ""){
			return $result;
		}
       		return false;
    	}
	public static function addEquipement($Name,$_logicalId) 	{
		$Equipement = self::byLogicalId($_logicalId, 'openalpr');
		if (is_object($Equipement)) {
			$Equipement->setIsEnable(1);
			$Equipement->save();
		} else {
			$Equipement = new openalpr();
			$Equipement->setName($Name);
			$Equipement->setLogicalId($_logicalId);
			$Equipement->setObject_id(null);
			$Equipement->setEqType_name('openalpr');
			$Equipement->setIsEnable(1);
			$Equipement->setIsVisible(0);
			$Equipement->save();
		}
		return $Equipement;
	}
	public static function addCommande($eqLogic,$Name,$_logicalId, $type='info', $subtype='binary') {
		$Commande = $eqLogic->getCmd(null,$_logicalId);
		if (!is_object($Commande)){
			$Commande = new openalprCmd();
			$Commande->setId(null);
			$Commande->setName($Name);
			$Commande->setLogicalId($_logicalId);
			$Commande->setEqLogic_id($eqLogic->getId());
			$Commande->setType($type);
			$Commande->setIsHistorized(1);
		}
		$Commande->setSubType($subtype);
		if($_logicalId =='*')
			$Commande->setIsVisible(0);
		if($_logicalId =='*' || $_logicalId == 'lastPlate'){
			$Commande->setTemplate('dashboard','PresenceGarage');
			$Commande->setTemplate('mobile','PresenceGarage');
		}
		$Commande->save();
		return $Commande;
	}
	public static function dependancy_info() {
		$return = array();
		$return['log'] = 'openalpr_update';
		$return['progress_file'] = '/tmp/compilation_openAlpr_in_progress';
		if(file_exists('/etc/openalpr/openalpr_VERSION'))
			$return['state'] = 'ok';
		else
			$return['state'] = 'nok';
		return $return;
	}
	public static function dependancy_install() {
		if (file_exists('/tmp/compilation_openAlpr_in_progress')) 
			return;
		log::remove('openalpr_update');
		$cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install.sh';
		$cmd .= ' >> ' . log::getPathToLog('openalpr_update') . ' 2>&1 &';
		exec($cmd);
	}
	public static function deamon_info() {
		$return = array();
		$return['log'] = 'openalpr';	
		$return['state'] = 'ok';
		if(!self::deamonRunning())
			$return['state'] = 'nok';
		$return['launchable'] = 'nok';
		if(!file_exists('/etc/openalpr/alprd.conf'))
			return $return;
      		$Cameras=config::byKey('configuration','openalpr');
		if($Cameras!=''){
			if(is_array($Cameras['cameraUrl'])){
				foreach($Cameras['cameraUrl'] as $AlprCamera)
				{
					if($AlprCamera!='')
						$return['launchable'] = 'ok';
				}
			}else{
				if($Cameras['cameraUrl']!='')
					$return['launchable'] = 'ok';
			}
		}
		return $return;
	}
	public static function deamon_start($_debug = false) {
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') 
			return;
		log::remove('openalpr');
		self::deamon_stop();
		self::ConfigOpenAlpr();
		$directory=config::byKey('SnapshotFolder','openalpr');
		if(!file_exists($directory)){
			exec('sudo mkdir -p '.$directory);
			exec('sudo chmod 777 -R '.$directory);
		}
		if ($deamon_info['state'] != 'ok') 
			exec('sudo alprd');
	}
	public static function deamon_stop() {
		exec('sudo pkill alprd');
	}
	public function toHtml($_version = 'dashboard') {
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) 
			return $replace;
		$version = jeedom::versionAlias($_version);
		if ($this->getDisplay('hideOn' . $version) == 1)
			return '';
		$Cmds = '';
		foreach ($this->getCmd() as $cmd) {
			if ($cmd->getIsVisible() == 1) {
				if ($cmd->getDisplay('hideOn' . $version) == 1) 
					continue;
				if($cmd->getLogicalId() != 'manual')
				$Cmds .= $cmd->toHtml($_version, $cmdColor);
			}
		}
		$replace['#cmd#'] = $Cmds;
      		return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'eqLogic', 'openalpr')));
  	}
	public static function getUrl($id) {		
		$Cameras=config::byKey('configuration','openalpr');
		if(!is_array($Cameras['cameraUrl'])){
			$adresse =$Cameras['cameraUrl'];
			$username=$Cameras['username'];
			$password=$Cameras['password'];
			
		}else{
			$adresse =$Cameras['cameraUrl'][$id];
			$username=$Cameras['username'][$id];
			$password=$Cameras['password'][$id];
		}
		$adresse = explode("://",$adresse);
		$url=$adresse[0];
		$url .= '://';
		if ($username != '') {
			$url .= urlencode($username) . ':' .urlencode($password) . '@';
		}
		$url.=$adresse[1];
		return $url ;
	}
	public static function GestionDetect($Detect){
		openalpr::SendLastSnap($Detect["uuid"].".jpg");
		//$Detect["site_id"];
		//$Detect["img_width"];
		//$Detect["img_height"];
		//$Detect["epoch_time"];
		//$Detect["processing_time_ms"];
		$camera_id=$Detect["camera_id"];
		foreach($Detect["results"] as $Results){
			if(self::isValideImmat($Results["plate"])){
				$search[]=$Results["plate"];
				if(self::searchValidPlate($camera_id,$search,$Results))
					return;
			}
			foreach($Results["candidates"] as $Plate){
				if(self::isValideImmat(trim($Plate["plate"]))){
					$PlateSplite=preg_split('[a-z]',trim($Plate["plate"]));
					$search[]=trim($Plate["plate"]);
					$search[]=$PlateSplite[0].'****';
					$search[]=$PlateSplite[0].$PlateSplite[1].'**';
					$search[]=$PlateSplite[0].'**'.$PlateSplite[2];
					$search[]='**'.$PlateSplite[1].'**';
					$search[]='**'.$PlateSplite[1].$PlateSplite[2];
					$search[]='****'.$PlateSplite[2];
					if(self::searchValidPlate($camera_id,$search,$Plate))
						return;
				}
			}
			if(self::isValideImmat($Results["plate"]) && config::byKey('inconnue','openalpr')){
				$Equipement = openalpr::addEquipement('Plaques détectées inconnu','inconnu');
				$CmdPlate=openalpr::addCommande($Equipement,$Results["plate"],$Results["plate"]);			
				$CmdPlate->updateState();
			}
		}
		self::CleanFolder();
	}
	public static function searchValidPlate($camera_id,$search,$Plate){
		foreach($search as $plate){
			log::add('openalpr','debug','Recherche de la plaque d\'immatriculation  '.$plate);
			$CmdPlates=cmd::byLogicalId($plate);
			if(is_array($CmdPlates)){
				foreach($CmdPlates as $CmdPlate){
					if (is_object($CmdPlate)){
						log::add('openalpr','debug','La plaque d\'immatriculation  '.$Plate["plate"].' a ete détécté avec la confidence '.$Plate["confidence"]);
						$CameraAutorise=$CmdPlate->getEqLogic()->getConfiguration('AutoriseCamera');
						if($CameraAutorise=='all' || $CameraAutorise==$camera_id){
							log::add('openalpr','debug','La plaque d\'immatriculation a été détecté sur une camera autorisé ('.$camera_id.')');			
							$CmdPlate->updateState();
						}
						return true;
					}
				} 
			}
			if (is_object($CmdPlates)){
				log::add('openalpr','debug','La plaque d\'immatriculation  '.$Plate["plate"].' a ete détécté avec la confidence '.$Plate["confidence"]);
				$CameraAutorise=$CmdPlate->getEqLogic()->getConfiguration('AutoriseCamera');
				if($CameraAutorise=='all' || $CameraAutorise==$camera_id){
					log::add('openalpr','debug','La plaque d\'immatriculation a été détecté sur une camera autorisé ('.$camera_id.')');			
					$CmdPlate->updateState();
				}
				return true;
			}
		}
		return false;
	}
	public static function isValideImmat($plate){
		if (strlen($plate) <= 9 && preg_match("#^[0-9]{1,4}[A-Z]{1,4}[0-9]{1,2}$#", $plate)) {
			return true;
		}elseif  (strlen($plate) <= 7 && preg_match("#^[A-Z]{2,2}[0-9]{2,3}[A-Z]{2,2}$#", $plate))  {
			return true;
		}
		return false;
	}
	public static function SendLastSnap($file){
		if (config::byKey('snapshot','openalpr')) {
			$directory=config::byKey('SnapshotFolder','openalpr');
			if(!file_exists($directory)){
				exec('sudo mkdir -p '.$directory);
				exec('sudo chmod 777 -R '.$directory);
			}
			if(substr($directory,-1)!='/')
				$directory.='/';
			$_options['files'][]=$directory.$file;
			$_options['title'] = '[Jeedom][openAlpr] Détéction d\'une immatriculation';
			$_options['message'] = "Une détéction a ete levée";
			log::add('openalpr','debug','Evoie d\'un message avec les derniere photo:'.json_encode($_options['files']));
			$cmds = explode('&&', config::byKey('alertMessageCommand','openalpr'));
			foreach ($cmds as $id) {
				$cmd = cmd::byId(str_replace('#', '', $id));
				if (is_object($cmd)) {
					log::add('openalpr','debug','Evoie du message avec '.$cmd->getName());
					$cmd->execute($_options);
				}
			}
		}
	}
	public static function getSnapshotDiretory() {
		$directory=config::byKey('SnapshotFolder','openalpr');
		if(!file_exists($directory))
			exec('sudo mkdir -p '.$directory);
		if(substr($directory,-1)!='/')
			$directory.='/';
		$directory = calculPath($directory);
		if (!is_writable($directory)) 
			exec('sudo chmod 777 -R '.$directory);
		return $directory;
	}
	public static function CleanFolder() {
		$directory=self::getSnapshotDiretory();
		$size = 0;
		foreach(scandir($directory, 1) as $file) {
			if(is_file($directory.$file) && $file != '.' && $file != '..' ) {	
				if ($size>= config::byKey('SnapshotFolderSeize', 'openalpr')*1000000)
					self::removeRecord($directory.$file);
				else
					$size += filesize($directory.$file);
			}
		}
		log::add('openalpr','debug','Le dossier '.$directory.' est a '.$size);
	}
	public static function removeRecord($file) {
		exec('sudo rm '. $file);
		log::add('openalpr','debug','Fichiers '.$file.' à été supprimée');
	}
}
class openalprCmd extends cmd {
	public function updateState($value=true){
		if(strtotime($this->getCollectDate()) >= time()+config::byKey('DelaisDetect', 'openalpr'))
			return;
		log::add('openalpr','debug',"Derniere mise détection: ".$this->getCollectDate());
		switch($this->getEqLogic()->getConfiguration('UpdateMode')){
			case'toogle':
				if ($this->execCmd())
					$value=false;
				else
					$value=true;
			break;
		}	
		if ($this->execCmd() != $this->formatValue($value)) {
			$this->event($value);
		}
		$this->getEqLogic()->checkAndUpdateCmd('*',true);
		$this->getEqLogic()->checkAndUpdateCmd('lastPlate',$this->getName());
	}
	public function preSave() {
		$this->setLogicalId(trim(str_replace('-','',$this->getLogicalId())));
		$this->setTemplate('dashboard','PresenceGarage');
		$this->setTemplate('mobile','PresenceGarage');
	}
	public function execute($_options = array()) {
		if($this->getLogicalId()== 'manual'){
			$Results=exec('sudo alpr -j '.$_options['message']);
			log::add('openalpr','debug',$Results);
			foreach($Results["candidates"] as $Plate){
				if(openalpr::isValideImmat(trim($Plate["plate"]))){
					$PlateSplite=preg_split('[a-z]',trim($Plate["plate"]));
					$search[]=trim($Plate["plate"]);
					$search[]=$PlateSplite[0].'****';
					$search[]=$PlateSplite[0].$PlateSplite[1].'**';
					$search[]=$PlateSplite[0].'**'.$PlateSplite[2];
					$search[]='**'.$PlateSplite[1].'**';
					$search[]='**'.$PlateSplite[1].$PlateSplite[2];
					$search[]='****'.$PlateSplite[2];
					if(openalpr::searchValidPlate($camera_id,$search,$Plate))
						return;
				}
			}
		}
	}
}
?>
