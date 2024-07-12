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
    <div class="btn-group">
    	<a href="#" class="btn btn-default btn-show-prescription active">Đơn thuốc</a>
    	<a href="#" class="btn btn-default btn-show-doctor-orders">Chỉ định</a>
    	<a href="#" class="btn btn-default btn-show-packages hide">Gói</a>
    </div>
    <?php endif; ?>
    
    <?php $this->load->view('prescription/index_services'); ?>
	<?php $this->load->view('prescription/index_drugs'); ?>
	<?php $this->load->view('prescription/index_packages'); ?>

    <a href="/prescription/index?patient_id=<?php echo isset($patient) && $patient ? $patient->id : ''; ?>" class="btn btn-success pull-left add-new-prescription <?php echo (isset($patient) && $patient && isset($diagnostic) && $diagnostic) ? '' : 'hide'; ?>">Tạo đơn thuốc mới</a>
    <button type="submit" class="btn btn-success save-item pull-right" style="margin-left: 10px" dianostic_id="<?php echo (isset($diagnostic) && $diagnostic) ? $diagnostic->id : ''; ?>">Lưu đơn thuốc</button>

    <button type="button" class="btn btn-primary pull-right btn-print-bill <?php echo (isset($diagnostic) && $diagnostic) ? '' : 'hide'; ?>" id="print_bill" onclick="printBill()" value="<?php echo (isset($diagnostic) && $diagnostic) ? $diagnostic->id : ''; ?>">In hóa đơn</button>
    <button type="button" class="btn btn-primary pull-right btn-print-prescription <?php echo (isset($diagnostic) && $diagnostic) ? '' : 'hide'; ?>" id="print_prescription" onclick="printPrescription()" value="<?php echo (isset($diagnostic) && $diagnostic) ? $diagnostic->id : ''; ?>">In đơn thuốc</button>
    <br><br><br>
</form>

<?php $this->load->view('prescription/index_modals'); ?>

<script>
    $(document).ready(function() {
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
