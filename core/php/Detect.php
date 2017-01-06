<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
log::add('openalpr','debug','Lancement du script de détéction');
$JsonDetect=file_get_contents('php://input');
log::add('openalpr','debug',$JsonDetect);
$Detect=json_decode($JsonDetect, true);
openalpr::SendLastSnap($Detect);
exit;
?>
