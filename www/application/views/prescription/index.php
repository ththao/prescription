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
            <td width="50%"><input type="text" class="form-control patient-name" placeholder="Tên" name="patient[name]"></td>
            <td><input type="text" class="form-control" placeholder="Năm sinh" name="patient[dob]"></td>
            <td>
                <select class="form-control" name="patient[gender]">
                    <option value="Nam">Nam</option>
                    <option value="Nữ">Nữ</option>
                </select>
            </td>
        </tr>
        <tr class="min-row">
            <td colspan="2"><input type="text" class="form-control" placeholder="Địa chỉ" name="patient[address]"></td>
            <td><input type="text" class="form-control" placeholder="Điện thoại" name="patient[phone]"></td>
        </tr>
        <tr class="min-row">
            <td colspan="3"><input type="text" class="form-control" placeholder="Chẩn đoán" name="diagnostic[diagnostic]"></td>
        </tr>
        <tr class="min-row">
            <td colspan="3"><input type="text" class="form-control" placeholder="Ghi chú" name="patient[note]"></td>
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

        <?php for ($i=1; $i <= 5; $i++) { ?>
            <tr class="drug-item drug-item-display min-row">
                <td>
                    <input type="text" value="" class="form-control drug-name" name="prescription[<?php echo $i; ?>][drug-name]"/>
                </td>
                <td >
                    <input type="number" value="" class="form-control drug-quantity" name="prescription[<?php echo $i; ?>][quantity]"/>
                </td>
                <td>
                    <select class="form-control drug-time" name="prescription[<?php echo $i; ?>][time_in_day]">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control drug-unit min-row" name="prescription[<?php echo $i; ?>][unit_in_time]" />
                </td>
            </tr>
        <?php } ?>
    </table>
    <input type="text" value="<?php echo $i; ?>" style="display: none" name="index_row">

    <button type="submit" class="btn btn-success save-item pull-right" style="margin-left: 10px">Lưu đơn thuốc</button>
    <br><br><br>
</form>

<script>
    $(document).ready(function() {
        var drug_names = <?php echo $drug_names; ?>;
        $(document).on('keydown.autocomplete', ".drug-name", function() {
            $(this).autocomplete({
                source: drug_names
            });
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
                '</td></tr>';
            $("#prescription").append("" + html);
            i++;
            $('input[name=index_row]').attr('value', i);

            $(document).on('keydown.autocomplete', ".drug-name", function() {
                $(this).autocomplete({
                    source: drug_names
                });
            });
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
</script>
