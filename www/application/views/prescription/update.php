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
            <td width="50%"><input type="text" class="form-control patient-name" placeholder="Tên" name="patient[name]" value="<?php echo $patient->name; ?>"></td>
            <td><input type="text" class="form-control" placeholder="Năm sinh" name="patient[dob]" value="<?php echo $patient->dob; ?>"></td>
            <td>
                <select class="form-control" name="patient[gender]">
                    <option value="Nam" <?php echo $patient->gender == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                    <option value="Nữ" <?php echo $patient->gender == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                </select>
            </td>
        </tr>
        <tr class="min-row">
            <td colspan="2"><input type="text" class="form-control" placeholder="Địa chỉ" name="patient[address]" value="<?php echo $patient->address; ?>"></td>
            <td><input type="text" class="form-control" placeholder="Điện thoại" name="patient[phone]" value="<?php echo $patient->phone; ?>"></td>
        </tr>
        <tr class="min-row">
            <td colspan="3"><input type="text" class="form-control" placeholder="Chẩn đoán" name="diagnostic[diagnostic]" value="<?php echo $diagnostic->diagnostic; ?>"></td>
        </tr>
        <tr class="min-row">
            <td colspan="3"><input type="text" class="form-control" placeholder="Ghi chú" name="patient[note]" value="<?php echo $diagnostic->note; ?>"></td>
        </tr>
    </table>

    <div style="text-align: center; font-size: 28px">Đơn thuốc <button type="button" class="btn btn-success pull-right add-column">Thêm thuốc</button></div>
    <!-- Table -->
    <table class="table table-striped table-bordered" id="prescription">
        <tr>
            <th>Tên thuốc</th>
            <th style="width: 200px">Số lượng (viên/gói)</th>
            <th style="width: 200px">Số lần trong ngày</th>
            <th style="width: 200px">Số viên (gói) mỗi lần</th>
        </tr>

        <?php foreach ($prescriptions as $i => $prescription) { ?>
            <tr class="drug-item drug-item-display min-row">
                <td>
                    <input type="text" value="<?php echo $prescription->name; ?>" class="form-control drug-name" name="prescription[<?php echo $i + 1; ?>][drug-name]"/>
                </td>
                <td >
                    <input type="number" value="<?php echo $prescription->quantity; ?>" class="form-control drug-quantity" name="prescription[<?php echo $i + 1; ?>][quantity]"/>
                </td>
                <td>
                    <select class="form-control drug-time" name="prescription[<?php echo $i + 1; ?>][time_in_day]">
                        <option value="1" <?php echo $prescription->time_in_day == 1 ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?php echo $prescription->time_in_day == 2 ? 'selected' : ''; ?>>2</option>
                        <option value="3" <?php echo $prescription->time_in_day == 3 ? 'selected' : ''; ?>>3</option>
                        <option value="4" <?php echo $prescription->time_in_day == 4 ? 'selected' : ''; ?>>4</option>
                        <option value="5" <?php echo $prescription->time_in_day == 5 ? 'selected' : ''; ?>>5</option>
                    </select>
                </td>
                <td>
                	<input type="text" class="form-control drug-unit min-row" name="prescription[<?php echo $i + 1; ?>][unit_in_time]" value="<?php echo $prescription->unit_in_time; ?>" />
                    
                </td>
            </tr>
        <?php } ?>
    </table>
    <input type="text" value="<?php echo count($prescriptions)+1; ?>" style="display: none" name="index_row">

    <a href="/prescription/index" class="btn btn-success add-new-prescription">Tạo đơn thuốc mới</a>
    <button type="submit" class="btn btn-success save-item pull-right" style="margin-left: 10px">Lưu đơn thuốc</button>

    <button type="button" class="btn btn-primary pull-right btn-print-bill" id="print_bill" onclick="printBill()" value="<?php echo $patient->id; ?>">In hóa đơn</button>
    <button type="button" class="btn btn-primary pull-right btn-print-prescription" id="print_prescription" onclick="printPrescription()" value="<?php echo $patient->id; ?>">In đơn thuốc</button>
    <br><br><br>
</form>

<script>
    $(document).ready(function() {
        $(document).on('keydown.autocomplete', ".drug-name", function() {
            $(this).autocomplete({
                source: <?php echo $drug_names; ?>,
            });
        });

        $(".add-column").click(function(){
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
                '</select></td></tr>';
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
                url:"/prescription/save",
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
