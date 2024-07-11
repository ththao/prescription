<div class="prescription" style="border: solid 1px #ddd;">
    <!-- Table -->
    <table class="table table-striped table-bordered" id="prescription">
        <tr>
            <th>Tên thuốc</th>
            <th style="width: 120px">Số lượng</th>
            <th style="width: 80px">Lần/ngày</th>
            <th style="width: 90px">SL mỗi lần</th>
            <th style="width: 350px">Ghi chú</th>
            <th style="width: 15px;"></th>
        </tr>

		<?php if (isset($prescriptions) && $prescriptions) { ?>
        <?php foreach ($prescriptions as $i => $prescription) { ?>
            <tr class="drug-item drug-item-display min-row">
                <td>
                	<input type="hidden" value="<?php echo $prescription->id; ?>" name="prescription[<?php echo $i + 1; ?>][id]" />
                    <input type="text" value="<?php echo $prescription->drug_name; ?>" class="form-control drug-name" name="prescription[<?php echo $i + 1; ?>][drug_name]"/>
                </td>
                <td style="display: flex;">
                    <input type="number" min="0" value="<?php echo $prescription->quantity; ?>" style="width: 70px;" class="form-control drug-quantity" name="prescription[<?php echo $i + 1; ?>][quantity]"/>
            		<label class="drug-unit" style="padding: 5px;"><?php echo $prescription->unit; ?></label>
                </td>
                <td>
                	<input type="number" min="0" value="<?php echo $prescription->time_in_day; ?>" class="form-control drug-time" name="prescription[<?php echo $i + 1; ?>][time_in_day]"/>
                </td>
                <td>
                	<input type="number" min="0" class="form-control drug-unit-in-time min-row" name="prescription[<?php echo $i + 1; ?>][unit_in_time]" value="<?php echo $prescription->unit_in_time; ?>" />
                </td>
                <td>
                	<input type="text" class="form-control drug-note min-row" name="prescription[<?php echo $i + 1; ?>][notes]" value="<?php echo $prescription->notes; ?>" />
                </td>
                <td><a class="remove-drug-item"><span title="Xóa" class="glyphicon glyphicon glyphicon-remove" style="color: red; padding-top: 6px;"></span></a></td>
            </tr>
        <?php } ?>
        <?php } ?>
        <tr class="drug-item drug-item-display min-row">
            <td>
        		<input type="hidden" value="" name="prescription[<?php echo (isset($prescriptions) && $prescriptions) ? (count($prescriptions) + 1) : 1; ?>][id]" />
                <input type="text" value="" class="form-control drug-name" name="prescription[<?php echo (isset($prescriptions) && $prescriptions) ? (count($prescriptions) + 1) : 1; ?>][drug_name]"/>
            </td>
            <td style="display: flex;">
                <input type="number" min="0" value="" class="form-control drug-quantity" style="width: 70px;" name="prescription[<?php echo (isset($prescriptions) && $prescriptions) ? (count($prescriptions) + 1) : 1; ?>][quantity]"/>
    			<label class="drug-unit" style="padding: 5px;"></label>
            </td>
            <td>
            	<input type="number" min="0" value="1" class="form-control drug-time" name="prescription[<?php echo (isset($prescriptions) && $prescriptions) ? (count($prescriptions) + 1) : 1; ?>][time_in_day]"/>
            </td>
            <td>
                <input type="number" min="0" value="1" class="form-control drug-unit-in-time min-row" name="prescription[<?php echo (isset($prescriptions) && $prescriptions) ? (count($prescriptions) + 1) : 1; ?>][unit_in_time]" />
            </td>
            <td>
                <input type="text" class="form-control drug-note min-row" name="prescription[<?php echo (isset($prescriptions) && $prescriptions) ? (count($prescriptions) + 1) : 1; ?>][notes]" />
            </td>
            <td><a class="remove-drug-item hide"><span title="Xóa" class="glyphicon glyphicon glyphicon-remove" style="color: red; padding-top: 6px;"></span></a></td>
        </tr>
    </table>
    <input type="hidden" value="<?php echo (isset($prescriptions) && $prescriptions) ? (count($prescriptions)+2) : 2; ?>" name="index_row">
</div>

<script>
$(document).ready(function() {
    $(document).on('keydown.autocomplete', ".drug-name", function() {
        $(this).autocomplete({
            source: <?php echo $drug_names; ?>,
            select: function (event, ui) {
				$(this).parents('.drug-item').find('.drug-unit').html(ui.item.unit);
        		$(this).parents('.drug-item').find('.remove-drug-item').removeClass('hide');
				$('.btn-print-bill').addClass('hide');
				$('.btn-print-prescription').addClass('hide');
				
				var need_add = true;
				$('.drug-item').each(function() {
					if ($.trim($(this).find('.drug-name').val()) == '') {
						need_add = false;
					}
				});
				
				if (need_add) {
					addDrugRow();
				}
			}
        });
    });
    
    $(document).on('blur', ".drug-name", function() {
        if ($.trim($(this).val()) == '') {
        	$(this).parents('.drug-item').find('.remove-drug-item').addClass('hide');
        	
        	$('.btn-print-bill').addClass('hide');
			$('.btn-print-prescription').addClass('hide');
        }
    });
    
    $(document).on('click', '.remove-drug-item', function(event) {
    	event.preventDefault();
    	
    	$(this).parents('.drug-item').remove();
    });
});

function addDrugRow() {
    var i = $('input[name=index_row]').val();

    var html = '<tr class="drug-item drug-item-display min-row">' +
        '<td>' +
        '<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>' +
        '<input type="text" value="" class="form-control drug-name ui-autocomplete-input" autocomplete="off" name="prescription['+ i +'][drug_name]"/></td>' +
        '<td style="display: flex;"><input type="number" min="0" value="" style="width: 70px;" class="form-control drug-quantity" name="prescription['+ i +'][quantity]"/><label class="drug-unit" style="padding: 5px;"></label></td>' +
        '<td><input type="number" min="0" value="1" class="form-control drug-time" name="prescription['+ i +'][time_in_day]"/></td>' +
        '<td><input type="number" min="0" value="1" class="form-control drug-unit-in-time min-row" name="prescription['+ i +'][unit_in_time]" /></td>' +
        '<td><input type="text" class="form-control drug-note min-row" name="prescription['+ i +'][notes]" /></td>' +
        '<td><a class="remove-drug-item hide"><span title="Xóa" class="glyphicon glyphicon glyphicon-remove" style="color: red; padding-top: 6px;"></span></a></td>' +
        '</tr>';
    $("#prescription").append(html);
    i++;
    $('input[name=index_row]').attr('value', i);
}

</script>