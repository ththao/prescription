<style>
    .min-row td {
        padding: 2px !important;
    }
</style>

<!-- Main component for a primary marketing message or call to action -->
<form id="pres" method="post" style="margin-right: 0px" >
    <div style="text-align: center; font-size: 28px">Thông tin bệnh nhân</div>
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
                    <option value="Nam" <?php echo !isset($patient) || !$patient || $patient->gender == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                    <option value="Nữ" <?php echo (isset($patient) && $patient) && $patient->gender == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
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
            <td colspan="3"><input type="text" class="form-control" placeholder="Ghi chú" name="diagnostic[note]" value="<?php echo (isset($diagnostic) && $diagnostic) ? $diagnostic->note : ''; ?>"></td>
        </tr>
    </table>

    <div style="text-align: center; font-size: 28px">Đơn thuốc <button type="button" class="btn btn-success pull-right add-column">Thêm thuốc</button></div>
    <!-- Table -->
    <table class="table table-striped table-bordered" id="prescription">
        <tr>
            <th>Tên thuốc</th>
            <th style="width: 155px">Số lượng (viên/gói)</th>
            <th style="width: 80px">Lần/ngày</th>
            <th style="width: 128px">Viên/gói mỗi lần</th>
            <th style="width: 350px">Ghi chú</th>
        </tr>

		<?php if (isset($prescriptions) && $prescriptions) { ?>
        <?php foreach ($prescriptions as $i => $prescription) { ?>
            <tr class="drug-item drug-item-display min-row">
                <td>
                	<input type="hidden" value="<?php echo $prescription->id; ?>" name="prescription[<?php echo $i + 1; ?>][id]" />
                    <input type="text" value="<?php echo $prescription->name; ?>" class="form-control drug-name" name="prescription[<?php echo $i + 1; ?>][drug_name]"/>
                </td>
                <td >
                    <input type="number" min="0" value="<?php echo $prescription->quantity; ?>" class="form-control drug-quantity" name="prescription[<?php echo $i + 1; ?>][quantity]"/>
                </td>
                <td>
                	<input type="number" min="0" value="<?php echo $prescription->time_in_day; ?>" class="form-control drug-time" name="prescription[<?php echo $i + 1; ?>][time_in_day]"/>
                </td>
                <td>
                	<input type="number" min="0" class="form-control drug-unit min-row" name="prescription[<?php echo $i + 1; ?>][unit_in_time]" value="<?php echo $prescription->unit_in_time; ?>" />
                </td>
                <td>
                	<input type="text" class="form-control drug-note min-row" name="prescription[<?php echo $i + 1; ?>][notes]" value="<?php echo $prescription->notes; ?>" />
                </td>
            </tr>
        <?php } ?>
        <?php } else { ?>
            <?php for ($i=1; $i <= 5; $i++) { ?>
                <tr class="drug-item drug-item-display min-row">
                    <td>
                		<input type="hidden" value="" name="prescription[<?php echo $i; ?>][id]" />
                        <input type="text" value="" class="form-control drug-name" name="prescription[<?php echo $i; ?>][drug_name]"/>
                    </td>
                    <td >
                        <input type="number" min="0" value="" class="form-control drug-quantity" name="prescription[<?php echo $i; ?>][quantity]"/>
                    </td>
                    <td>
                    	<input type="number" min="0" value="1" class="form-control drug-time" name="prescription[<?php echo $i; ?>][time_in_day]"/>
                    </td>
                    <td>
                        <input type="number" min="0" value="1" class="form-control drug-unit min-row" name="prescription[<?php echo $i; ?>][unit_in_time]" />
                    </td>
                    <td>
                        <input type="text" class="form-control drug-note min-row" name="prescription[<?php echo $i; ?>][notes]" />
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>
    <input type="text" value="<?php echo (isset($prescriptions) && $prescriptions) ? (count($prescriptions)+1) : $i; ?>" style="display: none" name="index_row">

    <a href="/prescription/index?patient_id=<?php echo $patient->id; ?>" class="btn btn-success add-new-prescription <?php echo (isset($patient) && $patient && isset($diagnostic) && $diagnostic) ? '' : 'hide'; ?>">Tạo đơn thuốc mới</a>
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

<script>
    $(document).ready(function() {
        $(document).on('keydown.autocomplete', ".drug-name", function() {
            $(this).autocomplete({
                source: <?php echo $drug_names; ?>,
            });
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
                            }
                        }
                    });
                }
			}
        });
        
        $(document).on('click', '#suggest-drugs .select-all', function() {
        	if ($(this).is(':checked')) {
        		$('.suggested-drug-id').prop('checked', true);
        	} else {
        		$('.suggested-drug-id').prop('checked', false);
        	}
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
        	$('#suggest-drugs').modal('hide');
        });

        $(".add-column").click(function() {
            var i = $('input[name=index_row]').val();

            var html = '' +
                '<tr class="drug-item drug-item-display min-row">' +
                '<td>' +
                '<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>' +
                '<input type="text" value="" class="form-control drug-name ui-autocomplete-input" autocomplete="off" name="prescription['+ i +'][drug-name]"/></td>' +
                '<td><input type="number" value="" class="form-control drug-quantity" name="prescription['+ i +'][quantity]"/></td>' +
                '<td><select class="form-control drug-time" name="prescription['+ i +'][time_in_day]">' +
                '<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option>' +
                '</select></td>' +
                '<td><input type="text" class="form-control drug-unit min-row" name="prescription['+ i +'][unit_in_time]" />' +
                '</select></td></td>' +
                '<td><input type="text" class="form-control drug-note min-row" name="prescription['+ i +'][notes]" />' +
                '</td></tr>';
            $("#prescription").append("" + html);
            i++;
            $('input[name=index_row]').attr('value', i);
        });

        $('.save-item').click(function (event) {
            // Prevent default posting of form
            event.preventDefault();

            var selected = $(this);
            if ($(selected).hasClass("disabled")) {
                return false;
            }

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
