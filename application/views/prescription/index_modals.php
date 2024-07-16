<div class="modal fade" id="suggest-drugs">
    <div class="modal-dialog modal-dialog-centered asb-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Đề nghị dùng thuốc</h4>
                <button type="button" class="close asb-btn-icon" data-dismiss="modal" aria-label="Close" style="margin-top: -25px;">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow: scroll;">
                
            </div>
            <div class="modal-footer">
                <a class="btn btn-success pull-right btn-confirm" style="margin-left: 20px;" href="#">Xác nhận</a>
                <a class="btn btn-primary pull-right" href="#" data-dismiss="modal">Hủy</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view-history" >
    <div class="modal-dialog modal-dialog-centered asb-modal-dialog" style="width: 1280px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Lịch sử khám chữa bệnh</h4>
                <button type="button" class="close asb-btn-icon" data-dismiss="modal" aria-label="Close" style="margin-top: -25px;">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow: scroll;">
                
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary pull-right" href="#" data-dismiss="modal">Tắt</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
	$(document).on('click', '.btn-view-history', function(e) {
    	e.preventDefault();
    	
    	var selected = $(this);
    	
    	$.ajax({
            url:"/patient/history",
            data: {
            	patient_id: $(selected).attr('patient_id')
            },
            type: "POST",
            dataType: 'json',
            success:function(data) {
                if (data.success) {
                	$('#view-history').find('.modal-body').html(data.html);
                    $('#view-history').modal('show');
                }
            }
        });
    });
    
    $(document).on('click', '#suggest-drugs .select-all', function() {
    	if ($(this).is(':checked')) {
    		$('.suggested-drug-id').prop('checked', true);
    	} else {
    		$('.suggested-drug-id').prop('checked', false);
    	}
		$('.btn-print-bill').addClass('hide');
		$('.btn-print-prescription').addClass('hide');
    });
    
    $('#suggest-drugs .btn-confirm').click(function(e) {
    	e.preventDefault();
    	
		$('.drug-item').remove();
    	$('.suggested-drug-id:checked').each(function() {
    		addDrugRow();
			$('.drug-item').last().find('.drug-name').val($(this).attr('drug_name'));
			$('.drug-item').last().find('.drug-unit').html($(this).attr('drug_unit'));
			$('.drug-item').last().find('.remove-drug-item').removeClass('hide');
    	});
    	addDrugRow();
    	
		$('.btn-print-bill').addClass('hide');
		$('.btn-print-prescription').addClass('hide');
    	$('#suggest-drugs').modal('hide');
    });
	
    $(document).on('click', '.apply-prescription', function (event) {
        // Prevent default posting of form
        event.preventDefault();

        var selected = $(this);
        if ($(selected).hasClass("disabled")) {
            return false;
        }
        var caption = $(selected).html();
        
        $.ajax({
            url:"/prescription/usePrescription",
            data: {
                'diagnostic_id': $(selected).attr('diagnostic_id')
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
                	$('#view-history').modal('hide');
                	$('.diagnostic').val(data.diagnostic);
                	$('.diagnostic-note').val(data.notes);
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
                }
            },
            complete: function() {
            	$(selected).html(caption).removeClass("disabled");
            }
        });
    });
});
</script>