<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<select class="groupselect">
	<option value="">Choisir le groupe de destination</option>
	<?php
		foreach(eqLogic::bytype('openalpr') as $Group){
			if ($Group->getId() != openalprCmd::byId(init('id'))->getEqLogic_id())
				echo '<option value="'.$Group->getId().'">'.$Group->getName().'</option>';
		}
	?>
</select>
<script>
var id=<?php echo init('id');?>;
$('.groupselect').on( 'change', function() {
	var Destination=$(this).val();
	if(Destination != '' && Destination != init('id')){
		$.ajax({
			type: 'POST',      
			url: 'plugins/openalpr/core/ajax/openalpr.ajax.php',
			data:{
				action: 'movePlate',
				id:id,
				destination:Destination,
			},
			dataType: 'json',
			error: function(request, status, error) {},
			success: function(data) {
				$('#md_modal').dialog( "close" );
				$('.cmd[data-cmd_id='+id+']').remove();
			}
		});
	}
});
</script>