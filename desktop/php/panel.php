<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$date = array(
	'start' => init('startDate', date('Y-m-d', strtotime('-1 week ' . date('Y-m-d')))),
	'end' => init('startDate', date('Y-m-d', strtotime('+1 day ' . date('Y-m-d')))),
);
include_file('3rdparty', 'jquery.tablesorter/theme.bootstrap', 'css');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.min', 'js');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.widgets.min', 'js');
?>
<div class="row row-overflow">
	<div class="col-xs-10">
		<legend style="height: 40px;">
			<span class="objectName"></span> {{du}}
			<input class="form-control input-sm in_datepicker" id='in_startDate' style="display : inline-block; width: 150px;" value='<?php echo $date['start']?>'/> {{au}}
			<input class="form-control input-sm in_datepicker" id='in_endDate' style="display : inline-block; width: 150px;" value='<?php echo $date['end']?>'/>
			<a class="btn btn-success btn-sm tooltips" id='bt_validChangeDate' title="{{Attention une trop grande plage de dates peut mettre très longtemps à être calculée ou même ne pas s'afficher}}">{{Ok}}</a>
		</legend>
		<div class="row">
			<div class="col-lg-6">
				<legend>{{Snapshot}}</legend>
				<div class="cursor snapshot"></div>
			</div>
			<div class="col-lg-6">
				<legend>{{Historique des détections}}</legend>
				<table id="table_DetectHistory" class="table table-bordered table-condensed tablesorter">
					<thead>
						<tr>
							<th>{{Date}}</th>
							<th>{{Groupe}}</th>
							<th>{{Nom}}</th>
							<th>{{Numero de la plaque}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<?php if(config::byKey('inconnue','openalpr')){ ?>
			<div class="col-lg-6">
				<legend>{{Gestion des imatriculations inconnue}}</legend>
				<table id="table_DetectInconnue" class="table table-bordered table-condensed tablesorter">
					<thead>
						<tr>
							<th>{{Numero de la plaque}}</th>
							<th>{{Déplacer vers}}</th>
							<th>{{Action}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
            <?php
				}
              foreach(eqLogic::byType('openalpr') as $Equipement){
                if($Equipement->getLogicalId() != 'inconnu'){
              ?>
			<div class="col-lg-6">
				<legend>{{<?php echo $Equipement->getName();?>}}</legend>
           		<?php echo $Equipement->toHtml();?>
			</div>
            <?php }} ?>
		</div>
	</div>
</div>

<?php include_file('desktop', 'panel', 'js', 'openalpr');?>