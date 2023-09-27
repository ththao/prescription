<div class="packages hide" style="border: solid 1px #ddd;">
	<div class="flex-container" style="padding: 20px;">
        <?php if ($packages): ?>
        <?php foreach ($packages as $package): ?>
    		<?php
                $title = '';
                if ($package->orders) {
                    $title = 'Chỉ định:&#013;';
                    foreach ($package->orders as $order) {
                        $title .= ' - ' . $order->service_name . '&#013;';
                    }
                }
                if ($package->prescriptions) {
                    $title .= 'Thuốc:&#013;';
                    foreach ($package->prescriptions as $prescription) {
                        $title .= ' - ' . $prescription->drug_name . '&#013;';
                    }
                }
        	?>
        	<button class="btn btn-success apply-package" package_id="<?php echo $package->id; ?>" title="<?php echo $title; ?>"><?php echo $package->package_name; ?></button>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $(document).on('click', '.apply-package', function (event) {
        // Prevent default posting of form
        event.preventDefault();

        var selected = $(this);
        if ($(selected).hasClass("disabled")) {
            return false;
        }
        var caption = $(selected).html();
        
        $.ajax({
            url:"/prescription/usePackage",
            data: {
                'package_id': $(selected).attr('package_id')
            },
            type: "POST",
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("Xin chờ ...").addClass("disabled");
        		$('.drug-item').remove();
        		$('.service-item').remove();
				$('.btn-print-bill').addClass('hide');
				$('.btn-print-prescription').addClass('hide');
            },
            success:function(data) {
                if (data.success) {
                	if (data.drugs) {
                		for (var i in data.drugs) {
                    		var drug = data.drugs[i];
                    		addDrugRow();
                    		
                			$('.drug-item').last().find('.drug-name').val(drug.drug_name);
                			$('.drug-item').last().find('.drug-quantity').val(drug.quantity);
                			$('.drug-item').last().find('.drug-unit').html(drug.unit);
                			$('.drug-item').last().find('.drug-time').val(drug.time_in_day);
                			$('.drug-item').last().find('.drug-unit-in-time').val(drug.unit_in_time);
                			$('.drug-item').last().find('.drug-note').val(drug.notes);
                			$('.drug-item').last().find('.remove-drug-item').removeClass('hide');
                		}
                		addDrugRow();
                	}
                	if (data.services) {
                		for (var i in data.services) {
                    		var service = data.services[i];
                    		addServiceRow();
                    		
                    		$('.service-item').last().find('.service-name').val(service.service_name);
                			$('.service-item').last().find('.service-quantity').val(service.quantity);
                			$('.service-item').last().find('.service-id').val(service.service_id);
                			$('.service-item').last().find('.service-notes').val(service.notes);
                			$('.service-item').last().find('.remove-service-item').removeClass('hide');
                		}
                		addServiceRow();
                	}
                	
                	$('.btn-show-prescription').trigger('click');
                }
            },
            complete: function() {
            	$(selected).html(caption).removeClass("disabled");
            }
        });
    });
});
</script>