<?php
try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
	if (init('action') == 'UpdateStatut') {
		$Commande=cmd::byId(init('id'));
		if(is_object($Commande)){
			$Commande->event();
		}
		ajax::success(true);
	}
	if (init('action') == 'ConfigOpenAlpr') {
		$AlprdConfig['local'] =openalpr::ConfigOpenAlpr();
		if (config::byKey('jeeNetwork::mode') == 'master') {
			foreach (jeeNetwork::byPlugin('openalpr') as $jeeNetwork) {
				$jsonrpc = $jeeNetwork->getJsonRpc();
				if (!$jsonrpc->sendRequest('ConfigOpenAlpr', array('plugin' => 'openalpr'))) {
					throw new Exception($jsonrpc->getError(), $jsonrpc->getErrorCode());
				}
				$AlprdConfig[$jeeNetwork->getName()] = $jsonrpc->getResult();
			}
		}
		ajax::success($AlprdConfig);
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
		exec('sudo rm ' . $record_dir . $file);
		ajax::success();
	}
	if (init('action') == 'getHistory') {
		$eqLogics = eqLogic::byType('openalpr');
		$result=array();
		foreach ($eqLogics as $eqLogic) {
			foreach ($eqLogic->getCmd() as $cmd) {
				if ($cmd->getLogicalId()!='*' && $cmd->getLogicalId()!='lastdetect'){
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
		ajax::success(cmd::byId(init('id'))->remove());
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
