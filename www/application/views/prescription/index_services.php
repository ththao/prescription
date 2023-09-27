<?php if (SERVICES == 'ON'): ?>
<div class="doctor-orders hide" style="border: solid 1px #ddd;">
    <!-- Table -->
    <table class="table table-striped table-bordered" id="services">
        <tr>
            <th>Kỹ thuật</th>
            <th style="width: 90px">Số lượng</th>
            <th style="width: 350px">Ghi chú</th>
            <th style="width: 15px;"></th>
        </tr>

		<?php if (isset($orders) && $orders) { ?>
        <?php foreach ($orders as $i => $order) { ?>
            <tr class="service-item service-item-display min-row">
                <td>
                	<input type="hidden" value="<?php echo $order->id; ?>" name="order[<?php echo $i + 1; ?>][id]" />
                    <input type="text" value="<?php echo $order->service_name; ?>" class="form-control service-name" name="order[<?php echo $i + 1; ?>][service_name]"/>
                	<input type="hidden" value="<?php echo $order->service_id; ?>" class="service-id" name="order[<?php echo $i + 1; ?>][service_id]" />
                </td>
                <td>
                    <input type="number" min="0" value="<?php echo $order->quantity; ?>" class="form-control service-quantity" name="order[<?php echo $i + 1; ?>][quantity]"/>
                </td>
                <td>
                	<input type="text" class="form-control service-notes min-row" name="service[<?php echo $i + 1; ?>][notes]" value="<?php echo $order->notes; ?>" />
                </td>
                <td><a class="remove-service-item"><span title="Xóa" class="glyphicon glyphicon glyphicon-remove" style="color: red; padding-top: 6px;"></span></a></td>
            </tr>
        <?php } ?>
        <?php } ?>
        <tr class="service-item service-item-display min-row">
            <td>
            	<input type="hidden" value="" name="order[<?php echo (isset($orders) && $orders) ? (count($orders) + 1) : 1; ?>][id]" />
                <input type="text" value="" class="form-control service-name" name="order[<?php echo (isset($orders) && $orders) ? (count($orders) + 1) : 1; ?>][service_name]"/>
            	<input type="hidden" value="" class="service-id" name="order[<?php echo (isset($orders) && $orders) ? (count($orders) + 1) : 1; ?>][service_id]" />
            </td>
            <td>
                <input type="number" min="0" value="1" class="form-control service-quantity" name="order[<?php echo (isset($orders) && $orders) ? (count($orders) + 1) : 1; ?>][quantity]"/>
            </td>
            <td>
            	<input type="text" class="form-control service-notes min-row" name="service[<?php echo (isset($orders) && $orders) ? (count($orders) + 1) : 1; ?>][notes]" value="" />
            </td>
            <td><a class="remove-service-item hide"><span title="Xóa" class="glyphicon glyphicon glyphicon-remove" style="color: red; padding-top: 6px;"></span></a></td>
        </tr>
    </table>
    <input type="hidden" value="<?php echo (isset($orders) && $orders) ? (count($orders)+1) : 2; ?>" name="service_index_row">
</div>

<script>
$(document).ready(function() {
    $(document).on('keydown.autocomplete', ".service-name", function() {
        $(this).autocomplete({
            source: <?php echo $service_names; ?>,
            select: function (event, ui) {
				$(this).parents('.service-item').find('.service-id').val(ui.item.id);
				$(this).parents('.service-item').find('.remove-service-item').removeClass('hide');
				$('.btn-print-bill').addClass('hide');
				$('.btn-print-prescription').addClass('hide');
				
				var need_add = true;
				$('.service-item').each(function() {
					if ($.trim($(this).find('.service-name').val()) == '') {
						need_add = false;
					}
				});
				
				if (need_add) {
					addServiceRow();
				}
			}
        });
    });
    
    $(document).on('blur', ".service-name", function() {
        if ($.trim($(this).val()) == '') {
        	$(this).parents('.service-item').find('.service-id').val('');
        	$(this).parents('.service-item').find('.remove-service-item').addClass('hide');
        	
        	$('.btn-print-bill').addClass('hide');
			$('.btn-print-prescription').addClass('hide');
        }
    });
    
    $(document).on('click', ".btn-show-prescription", function() {
        $('.btn-show-doctor-orders').removeClass('active');
        $(this).addClass('active');
        
        $('.doctor-orders').addClass('hide');
        $('.prescription').removeClass('hide');
    });
    
    $(document).on('click', ".btn-show-doctor-orders", function() {
        $('.btn-show-prescription').removeClass('active');
        $(this).addClass('active');
        
        $('.doctor-orders').removeClass('hide');
        $('.prescription').addClass('hide');
    });
    
    $(document).on('click', '.remove-service-item', function(event) {
    	event.preventDefault();
    	
    	$(this).parents('.service-item').remove();
    });
});

function addServiceRow() {
	var i = $('input[name=service_index_row]').val();

    var html = '<tr class="service-item service-item-display min-row">';
    html += '<td>';
    html += '<input type="hidden" value="" name="order[' + i + '][id]" />';
    html += '<input type="text" value="" class="form-control service-name" name="order[' + i + '][service_name]"/>';
    html += '<input type="hidden" value="" class="service-id" name="order[' + i + '][service_id]" />';
    html += '</td>';
    html += '<td><input type="number" min="0" value="1" class="form-control service-quantity" name="order[' + i + '][quantity]"/></td>';
    html += '<td><input type="text" class="form-control service-notes min-row" name="order[' + i + '][notes]" value="" /></td>';
    html += '<td><a class="remove-service-item hide"><span title="Xóa" class="glyphicon glyphicon glyphicon-remove" style="color: red; padding-top: 6px;"></span></a></td>';
    html += '</tr>';
    
    $("#services").append(html);
    i++;
    $('input[name=service_index_row]').attr('value', i);
}
</script>
<?php endif; ?>