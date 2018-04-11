$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$("#table_condition").sortable({axis: "y", cursor: "move", items: ".ConditionGroup", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$("#table_action").sortable({axis: "y", cursor: "move", items: ".ActionGroup", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$('#tab_zones a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');
});
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
function saveEqLogic(_eqLogic) {
	_eqLogic.configuration.condition=new Object();
	_eqLogic.configuration.action=new Object();
	var ConditionArray= new Array();
	var ActionArray= new Array();
	$('#conditiontab .ConditionGroup').each(function( index ) {
		ConditionArray.push($(this).getValues('.expressionAttr')[0])
	});
	$('#actiontab .ActionGroup').each(function( index ) {
		ActionArray.push($(this).getValues('.expressionAttr')[0])
	});
	_eqLogic.configuration.condition=ConditionArray;
	_eqLogic.configuration.action=ActionArray;
   	return _eqLogic;
}
function printEqLogic(_eqLogic) {
	$('.ConditionGroup').remove();
	$('.ActionGroup').remove();
	$('.eqLogicAttr[data-l1key=configuration][data-l2key=Droite]').val(JSON.stringify(_eqLogic.configuration.Droite));
	$('.eqLogicAttr[data-l1key=configuration][data-l2key=Centre]').val(JSON.stringify(_eqLogic.configuration.Centre));
	$('.eqLogicAttr[data-l1key=configuration][data-l2key=Gauche]').val(JSON.stringify(_eqLogic.configuration.Gauche));
	if (typeof(_eqLogic.configuration.heliotrope) !== 'undefined' && _eqLogic.configuration.heliotrope!='') 
		TraceMapZone(_eqLogic);
	if (typeof(_eqLogic.configuration.condition) !== 'undefined') {
		for(var index in _eqLogic.configuration.condition) { 
			if( (typeof _eqLogic.configuration.condition[index] === "object") && (_eqLogic.configuration.condition[index] !== null) )
				addCondition(_eqLogic.configuration.condition[index],$('#conditiontab').find('table tbody'));
		}
	}
	if (typeof(_eqLogic.configuration.action) !== 'undefined') {
			for(var index in _eqLogic.configuration.action) { 
				if( (typeof _eqLogic.configuration.action[index] === "object") && (_eqLogic.configuration.action[index] !== null) )
					addAction(_eqLogic.configuration.action[index],$('#actiontab').find('table tbody'));
			}
	}	
}
function addCondition(_condition,_el) {
	var tr = $('<tr class="ConditionGroup">')
		.append($('<td>')
			.append($('<input type="checkbox" class="expressionAttr" data-l1key="enable" checked/>')))
		.append($('<td>')
			.append($('<div class="input-group">')
				.append($('<span class="input-group-btn">')
					.append($('<a class="btn btn-default conditionAttr btn-sm" data-action="remove">')
						.append($('<i class="fa fa-minus-circle">'))))
				.append($('<input class="expressionAttr form-control input-sm cmdCondition" data-l1key="expression"/>'))
				.append($('<span class="input-group-btn">')
					.append($('<a class="btn btn-warning btn-sm listCmdCondition">')
						.append($('<i class="fa fa-list-alt">'))))));

        _el.append(tr);
        _el.find('tr:last').setValues(_condition, '.expressionAttr');
	$('.conditionAttr[data-action=remove]').off().on('click',function(){
		$(this).closest('tr').remove();
	});
  
}
function addAction(_action,  _el) {
	var tr = $('<tr class="ActionGroup">');
	tr.append($('<td>')
		.append($('<input type="checkbox" class="expressionAttr" data-l1key="enable" checked/>')));		
	tr.append($('<td>')
		.append($('<div class="input-group">')
			.append($('<span class="input-group-btn">')
				.append($('<a class="btn btn-default ActionAttr btn-sm" data-action="remove">')
					.append($('<i class="fa fa-minus-circle">'))))
			.append($('<input class="expressionAttr form-control input-sm cmdAction" data-l1key="cmd"/>'))
			.append($('<span class="input-group-btn">')
				.append($('<a class="btn btn-success btn-sm listAction" title="Sélectionner un mot-clé">')
					.append($('<i class="fa fa-tasks">')))
				.append($('<a class="btn btn-success btn-sm listCmdAction data-type="action"">')
					.append($('<i class="fa fa-list-alt">')))))
	       .append($(jeedom.cmd.displayActionOption(init(_action.cmd, ''), _action.options))));
        _el.append(tr);
        _el.find('tr:last').setValues(_action, '.expressionAttr');
	$('.ActionAttr[data-action=remove]').off().on('click',function () {
		$(this).closest('.ActionGroup').remove();
	});
	$('.expressionAttr[data-l1key=cmd]').off().on('focusout', function (event) {
	    var expression = $(this).closest('.ActionGroup').getValues('.expressionAttr');
	    var el = $(this);
	    jeedom.cmd.displayActionOption($(this).value(), init(expression[0].options), function (html) {
		el.closest('.ActionGroup').find('.actionOptions').html(html);
	    })
	});
}
$('.conditionAttr[data-action=add]').off().on('click',function(){
	addCondition({},$(this).closest('.tab-pane').find('table'));
});
$('body').on('click','.listCmdCondition',function(){
	var el = $(this).closest('tr').find('.expressionAttr[data-l1key=expression]');	
	jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
		var message = 'Aucun choix possible';
		if(result.cmd.subType == 'numeric'){
			message = '<div class="row">  ' +
			'<div class="col-md-12"> ' +
			'<form class="form-horizontal" onsubmit="return false;"> ' +
			'<div class="form-group"> ' +
			'<label class="col-xs-5 control-label" >'+result.human+' {{est}}</label>' +
			'             <div class="col-xs-3">' +
			'                <select class="conditionAttr form-control" data-l1key="operator">' +
			'                    <option value="==">{{égal}}</option>' +
			'                  <option value=">">{{supérieur}}</option>' +
			'                  <option value="<">{{inférieur}}</option>' +
			'                 <option value="!=">{{différent}}</option>' +
			'            </select>' +
			'       </div>' +
			'      <div class="col-xs-4">' +
			'         <input type="number" class="conditionAttr form-control" data-l1key="operande" />' +
			'    </div>' +
			'</div>' +
			'<div class="form-group"> ' +
			'<label class="col-xs-5 control-label" >{{Ensuite}}</label>' +
			'             <div class="col-xs-3">' +
			'                <select class="conditionAttr form-control" data-l1key="next">' +
			'                    <option value="">rien</option>' +
			'                  <option value="OU">{{ou}}</option>' +
			'            </select>' +
			'       </div>' +
			'</div>' +
			'</div> </div>' +
			'</form> </div>  </div>';
		}
		if(result.cmd.subType == 'string'){
			message = '<div class="row">  ' +
			'<div class="col-md-12"> ' +
			'<form class="form-horizontal" onsubmit="return false;"> ' +
			'<div class="form-group"> ' +
			'<label class="col-xs-5 control-label" >'+result.human+' {{est}}</label>' +
			'             <div class="col-xs-3">' +
			'                <select class="conditionAttr form-control" data-l1key="operator">' +
			'                    <option value="==">{{égale}}</option>' +
			'                  <option value="matches">{{contient}}</option>' +
			'                 <option value="!=">{{différent}}</option>' +
			'            </select>' +
			'       </div>' +
			'      <div class="col-xs-4">' +
			'         <input class="conditionAttr form-control" data-l1key="operande" />' +
			'    </div>' +
			'</div>' +
			'<div class="form-group"> ' +
			'<label class="col-xs-5 control-label" >{{Ensuite}}</label>' +
			'             <div class="col-xs-3">' +
			'                <select class="conditionAttr form-control" data-l1key="next">' +
			'                    <option value="">{{rien}}</option>' +
			'                  <option value="OU">{{ou}}</option>' +
			'            </select>' +
			'       </div>' +
			'</div>' +
			'</div> </div>' +
			'</form> </div>  </div>';
		}
		if(result.cmd.subType == 'binary'){
			message = '<div class="row">  ' +
			'<div class="col-md-12"> ' +
			'<form class="form-horizontal" onsubmit="return false;"> ' +
			'<div class="form-group"> ' +
			'<label class="col-xs-5 control-label" >'+result.human+' {{est}}</label>' +
			'            <div class="col-xs-7">' +
			'                 <input class="conditionAttr" data-l1key="operator" value="==" style="display : none;" />' +
			'                  <select class="conditionAttr form-control" data-l1key="operande">' +
			'                       <option value="1">{{Ouvert}}</option>' +
			'                       <option value="0">{{Fermé}}</option>' +
			'                       <option value="1">{{Allumé}}</option>' +
			'                       <option value="0">{{Éteint}}</option>' +
			'                       <option value="1">{{Déclenché}}</option>' +
			'                       <option value="0">{{Au repos}}</option>' +
			'                       </select>' +
			'                    </div>' +
			'                 </div>' +
			'<div class="form-group"> ' +
			'<label class="col-xs-5 control-label" >{{Ensuite}}</label>' +
			'             <div class="col-xs-3">' +
			'                <select class="conditionAttr form-control" data-l1key="next">' +
			'                  <option value="">{{rien}}</option>' +
			'                  <option value="OU">{{ou}}</option>' +
			'            </select>' +
			'       </div>' +
			'</div>' +
			'</div> </div>' +
			'</form> </div>  </div>';
		}

		bootbox.dialog({
			title: "{{Ajout d'une nouvelle condition}}",
			message: message,
			buttons: {
				"Ne rien mettre": {
					className: "btn-default",
					callback: function () {
						el.atCaret('insert', result.human);
					}
				},
				success: {
					label: "Valider",
					className: "btn-primary",
					callback: function () {
    						var condition = result.human;
						condition += ' ' + $('.conditionAttr[data-l1key=operator]').value();
						if(result.cmd.subType == 'string'){
							if($('.conditionAttr[data-l1key=operator]').value() == 'matches'){
								condition += ' "/' + $('.conditionAttr[data-l1key=operande]').value()+'/"';
							}else{
								condition += ' "' + $('.conditionAttr[data-l1key=operande]').value()+'"';
							}
						}else{
							condition += ' ' + $('.conditionAttr[data-l1key=operande]').value();
						}
						condition += ' ' + $('.conditionAttr[data-l1key=next]').value()+' ';
						el.atCaret('insert', condition);
						if($('.conditionAttr[data-l1key=next]').value() != ''){
							el.click();
						}
					}
				},
			}
		});
	});
});
$('.ActionAttr[data-action=add]').off().on('click',function(){
	addAction({},$(this).closest('.tab-pane').find('table'));
});
$("body").on('click', ".listAction", function() {
	var el = $(this).closest('.input-group').find('input');
	jeedom.getSelectActionModal({}, function (result) {
		el.value(result.human);
		jeedom.cmd.displayActionOption(el.value(), '', function (html) {
			el.closest('.form-group').find('.actionOptions').html(html);
		});
	});
}); 
$("body").on('click', ".listCmdAction", function() {
	var el = $(this).closest('.input-group').find('input');
	var type=$(this).attr('data-type');
	jeedom.cmd.getSelectModal({cmd: {type: type}}, function (result) {
		el.value(result.human);
		jeedom.cmd.displayActionOption(el.value(), '', function (html) {
			el.closest('.form-group').find('.actionOptions').html(html);
		});
	});
});
$("body").on('click', ".present", function() {
	var id = $(this).closest('tr').find('.cmdAttr[data-l1key=id]').val();
	$.ajax({
		type: 'POST',
		async: false,
		url: 'plugins/openalpr/core/ajax/openalpr.ajax.php',
		data: {
			action: 'UpdateStatut',
			id: id,
			value: 1
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {
		},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#div_alert').showAlert({message: "L'etat a été mise a jours", level: 'success'});
		}
	});

});
$("body").on('click', ".absent", function() {
	var id = $(this).closest('tr').find('.cmdAttr[data-l1key=id]').val();
	$.ajax({
		type: 'POST',
		async: false,
		url: 'plugins/openalpr/core/ajax/openalpr.ajax.php',
		data: {
			action: 'UpdateStatut',
			id: id,
			value: 0
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {
		},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#div_alert').showAlert({message: "L'etat a été mise a jours", level: 'success'});
		}
	});

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
			.append($('<input type="checkbox" class="cmdAttr" data-size="mini" data-label-text="{{Afficher}}" data-l1key="isVisible" checked/>')))
		.append($('<a class="btn btn-default btn-xs present">')
			.append($('<i class="fa fa-sign-in">')
				.text('{{Présent}}')))
		  .append($('<a class="btn btn-default btn-xs absent ">')
			.append($('<i class="fa fa-sign-out">')
				.text('{{Absent}}'))));
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
