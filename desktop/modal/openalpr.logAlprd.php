<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

?>
<div class="btn btn-danger LogAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</div>
<div class='LogALPRD' style="width: 100%;height: 75%;  overflow: auto;"></div>
<script>
getAlprdLog();
$('.LogAction[data-action=remove]').on('click', function() {
	$.ajax({
		type: "POST",
		timeout:8000, 
		url: "plugins/openalpr/core/ajax/openalpr.ajax.php",
		data: {
			action: "removeAlprdLog",
		},
		dataType: 'json',
		error: function(request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function(data) { 
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#div_alert').showAlert({message: data.result, level: 'success'});
			getAlprdLog();
		}
	});	
});
function getAlprdLog(){
	$.ajax({
		type: "POST",
		timeout:8000, 
		url: "plugins/openalpr/core/ajax/openalpr.ajax.php",
		data: {
			action: "getAlprdLog",
		},
		dataType: 'json',
		error: function(request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function(data) { 
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('.LogALPRD').html(data.result);
		}
	});	
}
</script>