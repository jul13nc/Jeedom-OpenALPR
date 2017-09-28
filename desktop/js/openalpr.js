$('.snapshot').on('click', function() {
    $('#md_modal').dialog({
		title: "{{Snapshot}}",
		height: 600,
		width: 850});
    $('#md_modal').load('index.php?v=d&modal=openalpr.history&plugin=openalpr&type=openalpr').dialog('open');
});
$('.logAlprd').on('click', function() {
    $('#md_modal').dialog({
		title: "{{log Alprd}}",
		height: 600,
		width: 850});
    $('#md_modal').load('index.php?v=d&modal=openalpr.logAlprd&plugin=openalpr&type=openalpr').dialog('open');
});
$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$('#table_cmd tbody').on( 'click','.bt_movePlate', function() {
    $('#md_modal').dialog({
		title: "{{Déplacer une plaque}}",
		height: 150,
		width: 300});
	var id=$(this).closest('.cmd').find('.cmdAttr[data-l1key=id]').val();
    $('#md_modal').load('index.php?v=d&modal=movePlate.openAlpr&plugin=openalpr&type=openalpr&id='+id).dialog('open');	
});
$('#table_cmd tbody').on( 'change','.cmdAttr[data-l1key=logicalId]', function() {
	switch($(this).val()){
		case '*':
		case 'lastPlate':
			$(this).hide();
			$(this).closest('.cmd').find('.cmdAction[data-action=remove]').hide();
		break;
		default:
			$(this).show();
			$(this).closest('.cmd').find('.cmdAction[data-action=remove]').show();
		break;
	}
});
function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
        var _cmd = {};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
	var tr =$('<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">');
	tr.append($('<td>')
		.append($('<div>')
			.append($('<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">'))
			.append($('<input class="cmdAttr form-control input-sm" data-l1key="name" value="' + init(_cmd.name) + '" placeholder="{{Name}}" title="Name">')))
		/*.append($('<div>')
			.append($('<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon">')
				.append($('<i class="fa fa-flag">')).text('Icone'))
			.append($('<span class="cmdAttr form-control input-sm" data-l1key="display" data-l2key="icon" style="margin-left : 10px;">')))*/);
	tr.append($('<td>')
		.append($('<div>')
			.append($('<input type="text" class="cmdAttr form-control input-sm" data-l1key="logicalId"  placeholder="{{Numero de la plaque}}">'))));
	tr.append($('<td>')
			.append($('<input type="hidden" class="cmdAttr" data-l1key="type" value="info" />'))
			.append($('<input type="hidden" class="cmdAttr" data-l1key="subType" value="binary" />'))
			.append($('<span>')
				.append($('<input type="checkbox" class="cmdAttr" data-size="mini" data-label-text="{{Historiser}}" data-l1key="isHistorized" checked/>')))
			.append($('<span>')
				.append($('<input type="checkbox" class="cmdAttr" data-size="mini" data-label-text="{{Afficher}}" data-l1key="isVisible" checked/>'))));

	var parmetre=$('<td>')
		.append($('<i class="fa fa-arrows-v pull-left cursor bt_sortable" style="margin-top: 9px;">'))
		.append($('<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove">'))
		.append($('<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure">')
		.append($('<i class="fa fa-cogs">')))
		.append($('<a class="btn btn-default btn-xs cmdAction bt_movePlate">')
			.append($('<i class="fa fa-arrows-alt">')
				.text('{{Déplacer}}')));
	tr.append(parmetre);
	$('#table_cmd tbody').append(tr);
	$('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
	}
