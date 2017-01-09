<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function openalpr_install() {
	foreach(cmd::byLogicalId('lastdetect','openalpr') as $cmd)
		$cmd->remove();
}

function openalpr_update() {
	foreach(cmd::byLogicalId('lastdetect','openalpr') as $cmd)
		$cmd->remove();
}
function openalpr_remove() {
}

?>
