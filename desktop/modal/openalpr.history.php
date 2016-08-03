<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$path=dirname(__FILE__);
for($i=1; $i<count(split('/',dirname(__FILE__)));$i++){
	$path.='/..';
}
$path .= config::byKey('SnapshotFolder','openalpr');
if(substr($path, -1) !='/')
	$path.='/';
$files = array();
foreach (ls($path, '*') as $file) {
	$name = explode('-', str_replace('.jpg', '', $file));
	$timestamp=round(trim($name[2])/1000,0);
	$time = date('H:i:s',$timestamp);
	$date = date('Ymd',$timestamp);

	if ($date == '') {
		continue;
	}
	if (!isset($files[$date])) {
		$files[$date] = array();
	}
	$files[$date][$time] = $file;
}
krsort($files);
?>
<div id='div_cameraRecordAlert' style="display: none;"></div>
<?php
echo '<a class="btn btn-danger bt_removeCameraFile pull-right" data-all="1"><i class="fa fa-trash-o"></i> {{Tout supprimer}}</a>';
?>
<?php
foreach ($files as $date => &$file) {
	echo '<div class="div_dayContainer">';
	echo '<legend>';
	echo '<a class="btn btn-xs btn-danger bt_removeCameraFile" data-day="1" data-date="'.$date.'" ><i class="fa fa-trash-o"></i> {{Supprimer}}</a> ';
	echo substr($date,6,2).'/'.substr($date,4,2).'/'.substr($date,0,4);
	echo '</legend>';
	echo '<div class="cameraThumbnailContainer">';
	krsort($file);
	foreach ($file as $time => $filename) {
		echo '<div class="cameraDisplayCard" style="background-color: #e7e7e7;padding:5px;height:167px;">';
		echo '<center>' . $time . '</center>';
		/*$pathfile=dirname(__FILE__);
		for($i=1; $i<count(split('/',dirname(__FILE__)));$i++){
			$pathfile.='/..';
		}
		$pathfile.=$dir . '/' . $filename;*/
		echo '<center><img class="img-responsive cursor displayImage" src="core/php/downloadFile.php?pathfile=' . urlencode($path.$filename) . '" width="150"/></center>';
		echo '<center style="margin-top:5px;"><a href="core/php/downloadFile.php?pathfile=' . urlencode($path.$filename) . '" class="btn btn-success btn-xs" style="color : white"><i class="fa fa-download"></i></a>';
		echo ' <a class="btn btn-danger bt_removeCameraFile btn-xs" style="color : white" data-day="'.$date.'" data-filename="' . $filename . '"><i class="fa fa-trash-o"></i></a></center>';
		echo '</div>';
	}
	echo '</div>';
	echo '</div>';
}
?>
<script>
    $('.cameraThumbnailContainer').packery({gutter : 5});
    $('.displayImage').on('click', function() {
        $('#md_modal2').dialog({title: "Image"});
        $('#md_modal2').load('index.php?v=d&plugin=openalpr&modal=openalpr.displayImage&src='+ $(this).attr('src')).dialog('open');
    });
    $('.bt_removeCameraFile').on('click', function() {
        var card;
        if($(this).attr('data-day') == 1){
            card = $(this).closest('.div_dayContainer');
			var date=$(this).attr('data-date')
			$('.bt_removeCameraFile[data-day='+date+']').each(function() {
				if($(this).attr('data-filename'))
					RemoveFile($(this).attr('data-filename'));
			});
        }
        else if($(this).attr('data-all') == 1){
            card = $('.div_dayContainer');
			$('.bt_removeCameraFile').each(function() {
				if($(this).attr('data-filename'))
					RemoveFile($(this).attr('data-filename'));
			});
        }
		else 
		{
			card = $(this).closest('.cameraDisplayCard')
			RemoveFile($(this).attr('data-filename'));
		}
		card.remove();
    });
	function RemoveFile(filename){	
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/openalpr/core/ajax/openalpr.ajax.php", // url du fichier php
            data: {
                action: "removeRecord",
                file: filename,
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error,$('#div_cameraRecordAlert'));
            },
            success: function(data) { // si l'appel a bien fonctionné
				if (data.state != 'ok') {
					$('#div_cameraRecordAlert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				$('.cameraThumbnailContainer').packery({gutter : 5});
			}
		});
	}
</script>