<?php
try {
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
	include_file('core', 'authentification', 'php');
	if (!isConnect('admin')) {
		throw new Exception(__('401 - Accès non autorisé', __FILE__));
	}
	if (init('action') == 'addFileMask') {
		if(isset($_FILES['FileMask'])){ 
			log::add('openalpr','debug',  "Le ficher mask a ete recu =>" . basename($_FILES["FileMask"]["name"]));
			$directory = dirname(__FILE__) . '/../../mask/';
			if(!file_exists($directory)){
				
				log::add('openalpr','debug',  "Creation d'un dossier mask =>" . $directory);
				exec('sudo mkdir -p '.$directory);
				exec('sudo chmod 777 -R '.$directory);
			}
			$target_file = $directory . basename($_FILES["FileMask"]["name"]);
			if(move_uploaded_file($_FILES['FileMask']['tmp_name'],$target_file)){
				log::add('openalpr','debug',  "Le fichier a ete placer et configurer dans detection_mask_image =>" . $target_file);
				config::save('detection_mask_image', $target_file,'openalpr');
				ajax::success($target_file);
			}else{
				log::add('openalpr','debug',  "Le fichier n'a pas pue etre déplacé");
				ajax::error("Le fichier n'a pas pue etre déplacé");
			}
		}
		ajax::error("Le fichier n'a pas été recu");
	}
	if (init('action') == 'UpdateStatut') {
		$Commande=cmd::byId(init('id'));
		if(is_object($Commande)){
			$Commande->getEqLogic()->checkAndUpdateCmd($Commande->getLogicalId(),init('value'));
			ajax::success(true);
		}
		ajax::error("Commande introuvable");
	}
	if (init('action') == 'ConfigOpenAlpr') {
		ajax::success(openalpr::ConfigOpenAlpr());
	}
   	if (init('action') == 'movePlate') {
		$Commande=cmd::byId(init('id'));
		if(is_object($Commande)){
			$Commande->setEqLogic_id(init('destination'));
			$Commande->save();
		}
		ajax::success(true);
	}
	if (init('action') == 'removeRecord') {
		$file = init('file');
		$record_dir =  config::byKey('SnapshotFolder','openalpr');
		if(substr($record_dir, -1) !='/')
			$record_dir.='/';
		log::add('openalpr','debug',  $record_dir . $file);
		$result=openalpr::removeRecord($record_dir . $file);
		ajax::success($result);
	}
	if (init('action') == 'getHistory') {
		$eqLogics = eqLogic::byType('openalpr');
		$result=array();
		foreach ($eqLogics as $eqLogic) {
			foreach ($eqLogic->getCmd() as $cmd) {
				if ($cmd->getLogicalId()!='*' && $cmd->getLogicalId()!='lastPlate'){
					foreach($cmd->getHistory(init('start'),init('end')) as $History)
					{
						//if ($History->getValue()!=0){
							$result[]=array(
								'id'=>$eqLogic->getId(),
								'groupeId'=>$eqLogic->getLogicalId(),
								'groupe'=>$eqLogic->getName(),
								'name'=>$cmd->getName(),
								'plate'=>$cmd->getLogicalId(),
								'datetime'=>$History->getDatetime(),
								//'value'=>$History->getValue(),
							);
					//	}
					}
				}
			}
		}
		ajax::success($result);
	}
	if (init('action') == 'getInconnue') {
		$eqLogics = eqLogic::byLogicalId('inconnu','openalpr');
      	if(is_object($eqLogics))
			ajax::success(jeedom::toHumanReadable(utils::o2a($eqLogics->getCmd())));
		ajax::success(false);
	}
	if (init('action') == 'removeInconnue') {
		$cmd=cmd::byId(init('id'));
		if(is_object($cmd))
			ajax::success($cmd->remove());
	}
	if (init('action') == 'getAlprdLog') {
		ajax::success("<pre>".file_get_contents('/var/log/alprd.log')."</pre>");
	}
	if (init('action') == 'removeAlprdLog') {
		exec('sudo rm /var/log/alprd.log');
		exec('sudo touch /var/log/alprd.log');
		ajax::success("Suppression faite");
	}
 	if (init('action') == 'getWidget') {
		$Widget=eqLogic::byId(init('id'));
		if (is_object($Widget))
		{
			ajax::success($Widget->toHtml());
		}
		ajax::success(false);
    }
	throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
