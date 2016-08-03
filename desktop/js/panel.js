$(".in_datepicker").datepicker();
initTableSorter();
$('.snapshot').load('index.php?v=d&modal=openalpr.history&plugin=openalpr&type=openalpr');
$('#bt_validChangeDate').on('click', function () {
	getDetectionHistory(1);
});
getDetectionHistory(1);
getInconnue(1);
function getDetectionHistory(_autoUpdate) {       
	$.ajax({
		type: 'POST',
		url: 'plugins/openalpr/core/ajax/openalpr.ajax.php',
		data: {
			action: 'getHistory',
			start:$('#in_startDate').val(),
			end:$('#in_endDate').val(),
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {
			setTimeout(function() {
				getDetectionHistory(_autoUpdate)
			}, 10000);
		},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			if (data.result.length>0){
				$('#table_DetectHistory tbody').html('');
				for (var i in data.result) {
					if(data.result[i]['groupeId']!='inconnu'){
						$('#table_DetectHistory tbody').append($("<tr>")
							.append($("<td>").text(data.result[i]['datetime']))
							.append($("<td>").text(data.result[i]['groupe']))
							.append($("<td>").text(data.result[i]['name']))
							.append($("<td>").text(data.result[i]['plate'])));
					}
				}
				$('#table_DetectHistory').trigger('update');
				if (init(_autoUpdate, 0) == 1) {
					setTimeout(function() {
						getDetectionHistory(_autoUpdate)
					}, 10000);
				}
			}
		}
	});
}
function getInconnue(_autoUpdate) {       
	$.ajax({
		type: 'POST',
		url: 'plugins/openalpr/core/ajax/openalpr.ajax.php',
		data: {
			action: 'getInconnue',
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {
			setTimeout(function() {
				getInconnue(_autoUpdate)
			}, 1000000);
		},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			if (data.result.length>0){
				$('#table_DetectInconnue tbody').html('');
				for (var i in data.result) {
					if(data.result[i]['logicalId'] != "lastdetect" && data.result[i]['logicalId'] !='*'){
						$('#table_DetectInconnue tbody').append($("<tr>")
							.append($("<td>").text(data.result[i]['logicalId']))
							.append($("<td>").load('index.php?v=d&modal=movePlate.openAlpr&plugin=openalpr&type=openalpr&id='+data.result[i]['id']))
							.append($("<td>")
								.append($('<div class="btn btn-danger bt_removePlate" data-id="'+data.result[i]['id']+'">')
									.append($('<i class="fa fa-minus-circle">'))
									.text('Supprimer'))));
					}
				}
				$('#table_DetectInconnue').trigger('update');
				if (init(_autoUpdate, 0) == 1) {
					setTimeout(function() {
						getInconnue(_autoUpdate)
					}, 1000000);
				}
			}
		}
	});
}
$('body').on('click','.bt_removePlate',function(){
	var _this=this;
	$.ajax({
		type: 'POST',
		url: 'plugins/openalpr/core/ajax/openalpr.ajax.php',
		data: {
			action: 'removeInconnue',
			id: $(_this).data('id'),
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
			$(_this).remove();
		}
	});
	
});