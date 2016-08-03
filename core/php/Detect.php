<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
log::add('openalpr','debug','Lancement du script de détéction');
$JsonDetect=file_get_contents('php://input');
log::add('openalpr','debug',$JsonDetect);
$Detect=json_decode($JsonDetect, true);
//$Detect["uuid"];
//$Detect["camera_id"];
//$Detect["site_id"];
//$Detect["img_width"];
//$Detect["img_height"];
//$Detect["epoch_time"];
//$Detect["processing_time_ms"];
$PlateConfigure=false;
foreach($Detect["results"] as $Results){
	foreach($Results["candidates"] as $Plate){
		$CmdPlates=cmd::byLogicalId(trim($Plate["plate"]));
		if(is_array($CmdPlates)){
			foreach($CmdPlates as $CmdPlate){
				if (is_object($CmdPlate)) {
					log::add('openalpr','debug','La plaque d\'immatriculation  '.$Plate["plate"].' a ete détécté avec la confidence '.$Plate["confidence"]);
					$PlateConfigure=true;
					$CmdPlate->execute($Plate);
				}
			} 
		}
		if(!$PlateConfigure){
			$PlateSplite=preg_split('[a-z]',trim($Plate["plate"]));
			if(count($PlateSplite)== 3){
				$CmdPlates=cmd::byLogicalId($PlateSplite[0].'****');
				if(is_array($CmdPlates)){
					foreach($CmdPlates as $CmdPlate){
						if (is_object($CmdPlate)) {
							log::add('openalpr','debug','La plaque d\'immatriculation  '.$Plate["plate"].' a ete détécté avec la confidence '.$Plate["confidence"]);
							$PlateConfigure=true;
							$CmdPlate->execute($Plate);
						}
					} 
				}
				$CmdPlates=cmd::byLogicalId($PlateSplite[0].$PlateSplite[1].'**');
				if(is_array($CmdPlates)){
					foreach($CmdPlates as $CmdPlate){
						if (is_object($CmdPlate)) {
							log::add('openalpr','debug','La plaque d\'immatriculation  '.$Plate["plate"].' a ete détécté avec la confidence '.$Plate["confidence"]);
							$PlateConfigure=true;
							$CmdPlate->execute($Plate);
						}
					} 
				}
				$CmdPlates=cmd::byLogicalId($PlateSplite[0].'**'.$PlateSplite[2]);
				if(is_array($CmdPlates)){
					foreach($CmdPlates as $CmdPlate){
						if (is_object($CmdPlate)) {
							log::add('openalpr','debug','La plaque d\'immatriculation  '.$Plate["plate"].' a ete détécté avec la confidence '.$Plate["confidence"]);
							$PlateConfigure=true;
							$CmdPlate->execute($Plate);
						}
					} 
				}
				$CmdPlates=cmd::byLogicalId('**'.$PlateSplite[1].'**');
				if(is_array($CmdPlates)){
					foreach($CmdPlates as $CmdPlate){
						if (is_object($CmdPlate)) {
							log::add('openalpr','debug','La plaque d\'immatriculation  '.$Plate["plate"].' a ete détécté avec la confidence '.$Plate["confidence"]);
							$PlateConfigure=true;
							$CmdPlate->execute($Plate);
						}
					} 
				}
				$CmdPlates=cmd::byLogicalId('**'.$PlateSplite[1].$PlateSplite[2]);
				if(is_array($CmdPlates)){
					foreach($CmdPlates as $CmdPlate){
						if (is_object($CmdPlate)) {
							log::add('openalpr','debug','La plaque d\'immatriculation  '.$Plate["plate"].' a ete détécté avec la confidence '.$Plate["confidence"]);
							$PlateConfigure=true;
							$CmdPlate->execute($Plate);
						}
					} 
				}
				$CmdPlates=cmd::byLogicalId('****'.$PlateSplite[2]);
				if(is_array($CmdPlates)){
					foreach($CmdPlates as $CmdPlate){
						if (is_object($CmdPlate)) {
							log::add('openalpr','debug','La plaque d\'immatriculation  '.$Plate["plate"].' a ete détécté avec la confidence '.$Plate["confidence"]);
							$PlateConfigure=true;
							$CmdPlate->execute($Plate);
						}
					} 
				}
			}
		}
	}
	if(!$PlateConfigure && config::byKey('inconnue','openalpr')){
		$Equipement = openalpr::addEquipement('Plaques détectées inconnu','inconnu');
		$CmdPlate=openalpr::addCommande($Equipement,$Results["plate"],$Results["plate"]);
		$CmdPlate->execute($Results);
	}
}
exit;
?>