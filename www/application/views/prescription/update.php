<style>
    .min-row td {
        padding: 2px !important;
    }
</style>

<!-- Main component for a primary marketing message or call to action -->
<form id="pres" method="post" style="margin-right: 0px" >
    <div style="text-align: center; font-size: 28px">Thông tin bệnh nhân
        <button type="button" class="btn btn-success pull-right btn-view-history <?php echo (isset($patient) && $patient) ? '' : 'hide'; ?>" style="margin-right: 10px;" patient_id="<?php echo (isset($patient) && $patient) ? $patient->id : ''; ?>">Xem lịch sử</button>
    </div>
    <!-- Table -->
    <table class="table table-bordered" width="100%" border="0">
        <tr class="min-row">
            <td width="50%">
            	<input type="text" class="form-control patient-name" placeholder="Tên" name="patient[name]" value="<?php echo (isset($patient) && $patient) ? $patient->name : ''; ?>" />
            	<input type="hidden" class="patient_id" name="patient[id]" value="<?php echo (isset($patient) && $patient) ? $patient->id : ''; ?>" />
            </td>
            <td><input type="text" class="form-control dob" placeholder="Năm sinh" name="patient[dob]" value="<?php echo (isset($patient) && $patient) ? $patient->dob : ''; ?>"></td>
            <td>
                <select class="form-control gender" name="patient[gender]">
                    <option value="Nam" <?php echo (((!isset($patient) || !$patient) && DEFAULT_GENDER == 'Nam') || ($patient && $patient->gender == 'Nam')) ? 'selected' : ''; ?>>Nam</option>
                    <option value="Nữ" <?php echo (((!isset($patient) || !$patient) && DEFAULT_GENDER == 'Nữ') || ($patient && $patient->gender == 'Nữ')) ? 'selected' : ''; ?>>Nữ</option>
                </select>
            </td>
        </tr>
        <tr class="min-row">
            <td colspan="2"><input type="text" class="form-control address" placeholder="Địa chỉ" name="patient[address]" value="<?php echo (isset($patient) && $patient) ? $patient->address : ''; ?>"></td>
            <td><input type="text" class="form-control phone" placeholder="Điện thoại" name="patient[phone]" value="<?php echo (isset($patient) && $patient) ? $patient->phone : ''; ?>"></td>
        </tr>
        <tr class="min-row">
            <td colspan="3">
            	<input type="text" class="form-control diagnostic" placeholder="Chẩn đoán" name="diagnostic[diagnostic]" value="<?php echo (isset($diagnostic) && $diagnostic) ? $diagnostic->diagnostic : ''; ?>">
            	<input type="hidden" class="diagnostic_template_id" name="diagnostic[diagnostic_template_id]" value="<?php echo (isset($diagnostic) && $diagnostic) ? $diagnostic->diagnostic_template_id : ''; ?>" />
            </td>
        </tr>
        <tr class="min-row">
            <td colspan="3"><input type="text" class="form-control diagnostic-note" placeholder="Ghi chú" name="diagnostic[note]" value="<?php echo (isset($diagnostic) && $diagnostic) ? $diagnostic->note : ''; ?>"></td>
        </tr>
    </table>
    
    <?php if (SERVICES == 'ON'): ?>
	<div style="text-align: center; font-size: 28px">Chỉ định <button type="button" class="btn btn-success pull-right add-service-item">Thêm chỉ định</button></div>
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
    </table>
    <input type="hidden" value="<?php echo (isset($orders) && $orders) ? (count($orders)+1) : 1; ?>" name="service_index_row">
	<?php endif; ?>

    <div style="text-align: center; font-size: 28px">Đơn thuốc 
        <button type="button" class="btn btn-success pull-right add-drug-item">Thêm thuốc</button>
    </div>
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
        <?php } else { ?>
            <?php for ($i=1; $i <= 5; $i++) { ?>
                <tr class="drug-item drug-item-display min-row">
                    <td>
                		<input type="hidden" value="" name="prescription[<?php echo $i; ?>][id]" />
                        <input type="text" value="" class="form-control drug-name" name="prescription[<?php echo $i; ?>][drug_name]"/>
                    </td>
                    <td style="display: flex;">
                        <input type="number" min="0" value="" class="form-control drug-quantity" style="width: 70px;" name="prescription[<?php echo $i; ?>][quantity]"/>
            			<label class="drug-unit" style="padding: 5px;"></label>
                    </td>
                    <td>
                    	<input type="number" min="0" value="1" class="form-control drug-time" name="prescription[<?php echo $i; ?>][time_in_day]"/>
                    </td>
                    <td>
                        <input type="number" min="0" value="1" class="form-control drug-unit-in-time min-row" name="prescription[<?php echo $i; ?>][unit_in_time]" />
                    </td>
                    <td>
                        <input type="text" class="form-control drug-note min-row" name="prescription[<?php echo $i; ?>][notes]" />
                    </td>
                    <td><a class="remove-drug-item"><span title="Xóa" class="glyphicon glyphicon glyphicon-remove" style="color: red; padding-top: 6px;"></span></a></td>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>
    <input type="hidden" value="<?php echo (isset($prescriptions) && $prescriptions) ? (count($prescriptions)+1) : $i; ?>" name="index_row">

    <a href="/prescription/index?patient_id=<?php echo $patient->id; ?>" class="btn btn-success pull-left add-new-prescription <?php echo (isset($patient) && $patient && isset($diagnostic) && $diagnostic) ? '' : 'hide'; ?>">Tạo đơn thuốc mới</a>
    <button type="submit" class="btn btn-success save-item pull-right" style="margin-left: 10px" dianostic_id="<?php echo (isset($diagnostic) && $diagnostic) ? $diagnostic->id : ''; ?>">Lưu đơn thuốc</button>

    <button type="button" class="btn btn-primary pull-right btn-print-bill <?php echo (isset($diagnostic) && $diagnostic) ? '' : 'hide'; ?>" id="print_bill" onclick="printBill()" value="<?php echo (isset($diagnostic) && $diagnostic) ? $diagnostic->id : ''; ?>">In hóa đơn</button>
    <button type="button" class="btn btn-primary pull-right btn-print-prescription <?php echo (isset($diagnostic) && $diagnostic) ? '' : 'hide'; ?>" id="print_prescription" onclick="printPrescription()" value="<?php echo (isset($diagnostic) && $diagnostic) ? $diagnostic->id : ''; ?>">In đơn thuốc</button>
    <br><br><br>
</form>

<div class="modal fade" id="suggest-drugs">
    <div class="modal-dialog modal-dialog-centered asb-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Đề nghị dùng thuốc</h4>
                <button type="button" class="close asb-btn-icon" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                
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
                <button type="button" class="close asb-btn-icon" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary pull-right" href="#" data-dismiss="modal">Tắt</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('keydown.autocomplete', ".drug-name", function() {
            $(this).autocomplete({
                source: <?php echo $drug_names; ?>,
                select: function (event, ui) {
    				$(this).parents('.drug-item').find('.drug-unit').html(ui.item.unit);
    				$('.btn-print-bill').addClass('hide');
    				$('.btn-print-prescription').addClass('hide');
    			}
            });
        });
        
        <?php if (SERVICES == 'ON'): ?>
        $(document).on('keydown.autocomplete', ".service-name", function() {
            $(this).autocomplete({
                source: <?php echo $service_names; ?>,
                select: function (event, ui) {
    				$(this).parents('.service-item').find('.service-id').val(ui.item.id);
    				$('.btn-print-bill').addClass('hide');
    				$('.btn-print-prescription').addClass('hide');
    			}
            });
        });
        <?php endif; ?>
        
        $(document).on("change", ".drug-name, .patient-name, .phone, .diagnostic, .diagnostic-note, .drug-item input", function() {
			$('.btn-print-bill').addClass('hide');
			$('.btn-print-prescription').addClass('hide');
        });
        
        var patient_names = <?php echo $patient_names; ?>;
        $(".patient-name").autocomplete({
            source: patient_names,
            select: function (event, ui) {
    			$('.patient_id').val(ui.item.id);
    			$('.dob').val(ui.item.dob);
    			$('.gender').val(ui.item.gender);
    			$('.address').val(ui.item.address);
    			$('.phone').val(ui.item.phone);
    			
    			$('.btn-view-history').attr('patient_id', ui.item.id).removeClass('hide');
				$('.btn-print-bill').addClass('hide');
				$('.btn-print-prescription').addClass('hide');
			}
        });
        
        var patient_phones = <?php echo $patient_phones; ?>;
        $(".phone").autocomplete({
            source: patient_phones,
            select: function (event, ui) {
    			$('.patient_id').val(ui.item.id);
    			$('.dob').val(ui.item.dob);
    			$('.gender').val(ui.item.gender);
    			$('.address').val(ui.item.address);
    			$('.patient-name').val(ui.item.name);
    			
    			$('.btn-view-history').attr('patient_id', ui.item.id).removeClass('hide');
				$('.btn-print-bill').addClass('hide');
				$('.btn-print-prescription').addClass('hide');
			}
        });
        
        var template_names = <?php echo $template_names; ?>;
        $(".diagnostic").autocomplete({
            source: template_names,
            select: function (event, ui) {
            
            	if (ui.item.id) {
        			$('.diagnostic_template_id').val(ui.item.id);
        			
        			$.ajax({
                        url:"/prescription/suggest",
                        data: {
                        	diagnostic_template_id: $('.diagnostic_template_id').val()
                        },
                        type: "POST",
                        dataType: 'json',
                        success:function(data) {
                            if (data.success) {
                            	$('#suggest-drugs').find('.modal-body').html(data.html);
                                $('#suggest-drugs').modal('show');
                				$('.btn-print-bill').addClass('hide');
                				$('.btn-print-prescription').addClass('hide');
                            }
                        }
                    });
                }
			}
        });
        
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
        	
        	$('.suggested-drug-id:checked').each(function() {
        		var drug_id = $(this).val();
        		var drug_name = $(this).attr('drug_name');
        		
        		var added = false;
        		$('.drug-item').each(function() {
        			if (added == false && $.trim($(this).find('.drug-name').val()) == '') {
        				$(this).find('.drug-name').val(drug_name);
        				added = true;
        			}
        		});
        		
        		if (!added) {
        			$(".add-column").trigger('click');
        			$('.drug-item').last().find('.drug-name').val(drug_name);
        		}
        	});
			$('.btn-print-bill').addClass('hide');
			$('.btn-print-prescription').addClass('hide');
        	$('#suggest-drugs').modal('hide');
        });
        
        $(document).on('click', '.add-service-item', function() {
        	var i = $('input[name=service_index_row]').val();

            var html = '<tr class="service-item service-item-display min-row">';
            html += '<td>';
            html += '<input type="hidden" value="" name="order[' + i + '][id]" />';
            html += '<input type="text" value="" class="form-control service-name" name="order[' + i + '][service_name]"/>';
            html += '<input type="hidden" value="" class="service-id" name="order[' + i + '][service_id]" />';
            html += '</td>';
            html += '<td><input type="number" min="0" value="1" class="form-control service-quantity" name="order[' + i + '][quantity]"/></td>';
            html += '<td><input type="text" class="form-control service-notes min-row" name="order[' + i + '][notes]" value="" /></td>';
            html += '<td><a class="remove-service-item"><span title="Xóa" class="glyphicon glyphicon glyphicon-remove" style="color: red; padding-top: 6px;"></span></a></td>';
            html += '</tr>';
            
            $("#services").append(html);
            i++;
            $('input[name=service_index_row]').attr('value', i);
        });
        
        $(document).on('click', '.remove-service-item', function(event) {
        	event.preventDefault();
        	
        	$(this).parents('.service-item').remove();
        });

        $(".add-drug-item").click(function() {
            var i = $('input[name=index_row]').val();

            var html = '<tr class="drug-item drug-item-display min-row">' +
                '<td>' +
                '<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>' +
                '<input type="text" value="" class="form-control drug-name ui-autocomplete-input" autocomplete="off" name="prescription['+ i +'][drug_name]"/></td>' +
                '<td style="display: flex;"><input type="number" min="0" value="" style="width: 70px;" class="form-control drug-quantity" name="prescription['+ i +'][quantity]"/><label class="drug-unit" style="padding: 5px;"></label></td>' +
                '<td><input type="number" min="0" value="1" class="form-control drug-time" name="prescription['+ i +'][time_in_day]"/></td>' +
                '<td><input type="number" min="0" value="1" class="form-control drug-unit-in-time min-row" name="prescription['+ i +'][unit_in_time]" /></td>' +
                '<td><input type="text" class="form-control drug-note min-row" name="prescription['+ i +'][notes]" /></td>' +
                '<td><a class="remove-drug-item"><span title="Xóa" class="glyphicon glyphicon glyphicon-remove" style="color: red; padding-top: 6px;"></span></a></td>' +
                '</tr>';
            $("#prescription").append(html);
            i++;
            $('input[name=index_row]').attr('value', i);
        });
        
        $(document).on('click', '.remove-drug-item', function(event) {
        	event.preventDefault();
        	
        	$(this).parents('.drug-item').remove();
        });

        $('.save-item').click(function (event) {
            // Prevent default posting of form
            event.preventDefault();

            var selected = $(this);
            if ($(selected).hasClass("disabled")) {
                return false;
            }
            var caption = $(selected).html();

            $.ajax({
                url:"/prescription/save/" + $(selected).attr('dianostic_id'),
                data: $('#pres').serializeArray(),
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $(selected).html("Đang lưu ...").addClass("disabled");
                },
                success:function(data) {
                    if (data.success) {
                        window.location = data.url;
                    } else {
                        alert(data.error);
                        $(selected).html("Lưu đơn thuốc").removeClass("disabled");
                    }
                },
                complete: function() {
                	$(selected).html(caption).removeClass("disabled");
                }
            });
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
            		$('.drug-item').each(function() {
        				$(this).find('.drug-name').val('');
        				$(this).find('.drug-quantity').val('');
        				$(this).find('.drug-unit').html('');
        				$(this).find('.drug-time').val(1);
        				$(this).find('.drug-unit-in-time').val(1);
        				$(this).find('.drug-note').val('');
            		});
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
                        		var added = false;
                        		$('.drug-item').each(function() {
                        			if (added == false && $.trim($(this).find('.drug-name').val()) == '') {
                        				$(this).find('.drug-name').val(drug.drug_name);
                        				$(this).find('.drug-quantity').val(drug.quantity);
                        				$(this).find('.drug-unit').html(drug.unit);
                        				$(this).find('.drug-time').val(drug.time_in_day);
                        				$(this).find('.drug-unit-in-time').val(drug.unit_in_time);
                        				$(this).find('.drug-note').val(drug.notes);
                        				added = true;
                        			}
                        		});
                        		
                        		if (!added) {
                        			$(".add-column").trigger('click');
                        			$('.drug-item').last().find('.drug-name').val(drug.drug_name);
                        			$('.drug-item').last().find('.drug-quantity').val(drug.quantity);
                        			$('.drug-item').last().find('.drug-unit').html(drug.unit);
                        			$('.drug-item').last().find('.drug-time').val(drug.time_in_day);
                        			$('.drug-item').last().find('.drug-unit-in-time').val(drug.unit_in_time);
                        			$('.drug-item').last().find('.drug-note').val(drug.notes);
                        		}
                    		}
                    	}
                    	if (data.services) {
                    		for (var i in data.services) {
                        		var service = data.services[i];
                        		var added = false;
                        		$('.service-item').each(function() {
                        			if (added == false && $.trim($(this).find('.service-name').val()) == '') {
                        				$(this).find('.service-name').val(service.service_name);
                        				$(this).find('.service-quantity').val(service.quantity);
                        				$(this).find('.service-id').val(service.service_id);
                        				$(this).find('.service-notes').val(service.notes);
                        				added = true;
                        			}
                        		});
                        		
                        		if (!added) {
                        			$(".add-column").trigger('click');
                        			$('.drug-item').last().find('.service-name').val(drug.service_name);
                        			$('.drug-item').last().find('.service-quantity').val(service.quantity);
                        			$('.drug-item').last().find('.service-id').val(service.service_id);
                        			$('.drug-item').last().find('.service-notes').val(service.notes);
                        		}
                    		}
                    	}
                    }
                },
                complete: function() {
                	$(selected).html(caption).removeClass("disabled");
                }
            });
        });
    });

    function printPrescription() {
        var id = $('#print_prescription').val();
        window.open("/prescription/printPrescription/" + id);
    }

    function printBill() {
        var id = $('#print_bill').val();
        window.open("/prescription/bill/" + id);
    }
</script>
