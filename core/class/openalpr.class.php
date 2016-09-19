<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class openalpr extends eqLogic {
	public static function cron() {
		foreach(eqLogic::byType('openalpr') as $Equipement){ 
			foreach($Equipement->getCmd(null,'*') as $Commandes){ 
		                foreach($Commandes as $Commande){ 
			                //if($Commande->execCmd()== 1){
						log::add('openalpr','debug','['.$Commande->getEqlogic()->getName().']['.$Commande->getName().'] a False');
			                        $Commande->setCollectDate('');
			                        $Commande->event(0);
			                        $Commande->save();
			                //}
				}
			}
		}
    	}
    	public function postSave() {
		self::addCommande($this,'Etat du groupe','*');
		self::addCommande($this,' Dernière détection','lastdetect','string');
	}
 	public static function ConfigOpenAlpr() {
		$file='/etc/openalpr/openalpr.conf';
		if (config::byKey('openParam','openalpr')){
			$fp = fopen($file,"w+");
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
			fclose($fp);
		}
		else{
			exec('sudo rm '.$file);
			exec('sudo touch '.$file);
			exec('sudo chmod 777 '.$file);
		}
		$file='/etc/openalpr/alprd.conf';
		$fp = fopen($file,"w+");
		fputs($fp,'[daemon]');
		fputs($fp, "\n");
		fputs($fp,'country = eu');
		fputs($fp, "\n");
		fputs($fp,'site_id = Jeedom');
		fputs($fp, "\n");
		if(config::byKey('configuration','openalpr')!=''){
			foreach(config::byKey('configuration','openalpr') as $AlprCamera){
				if($AlprCamera['cameraUrl']!=''){
					fputs($fp,'stream ='. $AlprCamera['cameraUrl']);
					fputs($fp, "\n");
				}
			}
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
	public static function addCommande($eqLogic,$Name,$_logicalId, $subtype='binary') {
		$Commande = $eqLogic->getCmd(null,$_logicalId);
		if (!is_object($Commande))
		{
			$Commande = new openalprCmd();
			$Commande->setId(null);
			$Commande->setName($Name);
			$Commande->setLogicalId($_logicalId);
			$Commande->setEqLogic_id($eqLogic->getId());
			$Commande->setType('info');
			$Commande->setSubType($subtype);
			$Commande->setIsHistorized(1);
		}
		if($_logicalId =='*' || $_logicalId == 'lastdetect')
			$Commande->setIsVisible(0);
		$Commande->setTemplate('dashboard','PresenceGarage');
		$Commande->setTemplate('mobile','PresenceGarage');
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
		if (file_exists('/tmp/compilation_openAlpr_in_progress')) {
			return;
		}
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
		if(config::byKey('configuration','openalpr')!=''){
			foreach(config::byKey('configuration','openalpr') as $AlprCamera)
			{
				if($AlprCamera['cameraUrl']!='')
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
	public static function SendLastSnap($Detect){
		if (config::byKey('snapshot','openalpr')) {
			$directory=config::byKey('SnapshotFolder','openalpr');
			if(!file_exists($directory)){
				exec('sudo mkdir -p '.$directory);
				exec('sudo chmod 777 -R '.$directory);
			}
			if(substr($directory,-1)!='/')
				$directory.='/';
			$lastFiles = array_slice(array_diff(scandir($directory,1), array('..', '.')),0,config::byKey('NbSnap','openalpr'));
			foreach($lastFiles as $file)
				$_options['files'][]=$directory.$file;
			$_options['title'] = '[Jeedom][openAlpr] Détéction d\'une immatriculation';
			$_options['message'] = json_encode($Detect);
			log::add('openalpr','debug','Evoie d\'un message avec les derniere photo:'.json_encode($_options['files']));
			$cmds = explode('&&', config::byKey('alertMessageCommand','openalpr'));
			log::add('openalpr','debug',json_encode($cmds));
			foreach ($cmds as $id) {
				$cmd = cmd::byId(str_replace('#', '', $id));
				if (is_object($cmd)) {
					log::add('openalpr','debug','Evoie du message a '.$cmd->getName());
					$cmd->execute($_options);
				}
			}
		}
	}
}
class openalprCmd extends cmd {
    /*     * *************************Attributs****************************** */
    /*     * ***********************Methode static*************************** */
    /*     * *********************Methode d'instance************************* */
	public function preSave() {
		$this->setLogicalId(str_replace('-','',$this->getLogicalId()));
		$this->setTemplate('dashboard','PresenceGarage');
		$this->setTemplate('mobile','PresenceGarage');
		}
    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */
	public function execute($_options = array()) {
		if($this->getValueDate() < date("Y-m-d H:i:s", strtotime('+5 minutes', date('Y-m-d H:i:s'))){ 
			openalpr::SendLastSnap($_options);
			log::add('openalpr','debug','La plaque d\'immatriculation  '.$this->getLogicalId().' du vehicule '.$this->getName().' a ete détécté');
			if($this->execCmd() == 0)
				$return=1;
			else
				$return=0;
			$this->event($return);
			$this->setCollectDate('');
			$this->save();
			log::add('openalpr','info',$this->getName().' est '.$return);
			if(isset($_options["plate"])){
				$this->setConfiguration('confidence',$_options["confidence"]);
				//$this->setConfiguration('matches_template',$_options["matches_template"]);
				//$this->setConfiguration('region',$_options["region"]);
				//$this->setConfiguration('region_confidence',$_options["region_confidence"]);
				//$this->setConfiguration('coordinates',$_options["coordinates"]);
				$this->setCollectDate('');
				$this->save();
				$CmdGroupe=$this->getEqlogic()->getCmd(null,'*');
				if(is_object($CmdGroupe)){
					log::add('openalpr','debug','Mise a jour de l\'etat Général');
					//if($CmdGroupe->execCmd()== 0){
						log::add('openalpr','info','['.$CmdGroupe->getEqlogic()->getName().']['.$CmdGroupe->getName().'] a true');
						$CmdGroupe->event(1);
						$CmdGroupe->setCollectDate('');
						$CmdGroupe->save();
					//}
				}
				$CmdLast=$this->getEqlogic()->getCmd(null,'lastdetect');
				if(is_object($CmdLast)){
					log::add('openalpr','debug','Mise a jour de l\'objet de derniere detection');
					//if($CmdLast->execCmd()== 0){
						$directory=config::byKey('SnapshotFolder','openalpr');
						if(!file_exists($directory)){
							exec('sudo mkdir -p '.$directory);
							exec('sudo chmod 777 -R '.$directory);
						}
						if(substr($directory,-1)!='/')
							$directory.='/';
						$value= array_slice(scandir($directory,SCANDIR_SORT_DESCENDING),0,1)[0];
						log::add('openalpr','info','Dernier snapshot: '.$value);
						$CmdLast->event($directory.$value);
						$CmdLast->setCollectDate('');
						$CmdLast->save();
					//}
				}
			}
		}
		else
			log::add('openalpr','info','Une détéction sur '.$this->getName().' - '.$this->getLogicalId().' a été detecté mais ignoré');
		return $return;
	}
    /*     * **********************Getteur Setteur*************************** */
}
?>
