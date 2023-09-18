<style>
<!--
.drug-item-display .display {
    display: block;
}
.drug-item-display .edit {
    display: none;
}
.drug-item-edit .display {
    display: none;
}
.drug-item-edit .edit {
    display: block;
}
-->
</style>

<div class="btn-group">
	<a href="/drug/index" class="btn btn-success">Danh sách thuốc</a>
	<a href="/drug/import" class="btn btn-default">Thêm thuốc từ file</a>
	<a href="/report/daily" class="btn btn-default">Báo cáo ngày</a>
	<a href="/report/monthly" class="btn btn-default">Báo cáo tháng</a>
	<a href="/auth/password" class="btn btn-default">Đổi mật khẩu</a>
</div>

<!-- Main component for a primary marketing message or call to action -->
<div class="panel panel-default">
    <!-- Default panel contents -->

    <div class="navbar-form" role="search">
        <nav class="pull-left">
            <?php echo $models['pagination']; ?>
        </nav>
    </div>

    <!-- Table -->
    <table class="table table-striped table-bordered" style="margin-bottom: 0px;">
        <tr>
            <th>Tên thuốc</th>
            <th style="width: 100px">Đơn vị</th>
            <th style="width: 150px">Giá Nhập (VNĐ)</th>
            <th style="width: 150px">Giá Bán (VNĐ)</th>
            <th>Ghi chú</th>
            <th>Sửa / Xóa</th>
        </tr>

        <tr class="drug-item-search">
            <td>
                <form method="get" action="<?php echo site_url('drug/search'); ?>" id="drug-search-form">
                    <input type="text" class="form-control drug-search" placeholder="Tìm kiếm" name="search" value="<?php echo $search; ?>">
                </form>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <?php foreach ($models['drugs'] as $item) { ?>
            <tr class="drug-item drug-item-display">
                <td>
                    <label class="display"><?php echo $item->name ?></label>
                    <input type="text" value="<?php echo $item->name ?>" class="form-control edit drug-name" name="name"/>
                </td>
                <td>
                    <label class="display"><?php echo $item->unit ?></label>
                    <select class="form-control edit drug-unit" name="unit">
                        <option value="Viên" <?php echo $item->unit == 'Viên' ? 'selected' : '' ?>>Viên</option>
                        <option value="Ống" <?php echo $item->unit == 'Ống' ? 'selected' : '' ?>>Ống</option>
                        <option value="Gói" <?php echo $item->unit == 'Gói' ? 'selected' : '' ?>>Gói</option>
                    </select>
                </td>
                <td>
                    <label class="display"><?php echo number_format($item->in_price, 0, ',', '.') ?></label>
                    <input type="text" value="<?php echo $item->in_price?>" class="form-control edit drug-in_price" name="in_price"/>
                </td>
                <td>
                    <label class="display"><?php echo number_format($item->price, 0, ',', '.') ?></label>
                    <input type="text" value="<?php echo $item->price ?>" class="form-control edit drug-price" name="price"/>
                </td>
                <td>
                    <label class="display"><?php echo $item->note ?></label>
                    <input type="text" value="<?php echo $item->note ?>" class="form-control edit drug-note" name="note"/>
                </td>
                <td style="text-align: center; width: 100px">
                    <span class="glyphicon glyphicon-edit drug-item-save" drug-url="/drug/update/<?php echo $item->id ?>" title="Cập nhật thuốc" style="color: blue; cursor: pointer; "></span>&nbsp;
                    <a href="/drug/delete/<?php echo $item->id ?>" title="Xóa thuốc"><span title="Xóa thuốc" class="glyphicon glyphicon-remove" style="color: red"></span></a>
                </td>
            </tr>
        <?php } ?>
		<tr><td colspan="5"></td></tr>
        <tr class="drug-item-new">
            <td><input type="text" class="form-control" placeholder="Tên thuốc" name="name"></td>
            <td>
                <select class="form-control edit drug-unit" name="unit">
                    <option value="Viên" selected>Viên</option>
                    <option value="Ống">Ống</option>
                    <option value="Gói">Gói</option>
                </select>
            </td>
            <td><input type="number" class="form-control" placeholder="Giá Nhập" name="in_price"></td>
            <td><input type="number" class="form-control" placeholder="Giá Bán" name="price"></td>
            <td><input type="text" class="form-control" placeholder="Ghi chú" name="note"></td>
            <td style="text-align: center"><input type="submit" class="btn btn-success" id="add_drug" value="Thêm"></td>
        </tr>
    </table>
</div>

<script>
    $(document).ready(function() {
    	$('.drug-item-save').click(function (event) {
            if ($('#drug_row').length) {
                // Prevent default posting of form
                event.preventDefault();

                var selected = $(this);
                $.ajax({
                    url: $(selected).attr("drug-url"),
                    data: {
                        'name': $(selected).parents('.drug-item-edit').find('input[name="name"]').val(),
                        'unit': $(selected).parents('.drug-item-edit').find('select[name="unit"]').val(),
                        'in_price': $(selected).parents('.drug-item-edit').find('input[name="in_price"]').val(),
                        'price': $(selected).parents('.drug-item-edit').find('input[name="price"]').val(),
                        'note': $(selected).parents('.drug-item-edit').find('input[name="note"]').val()
                    },
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.error);
                        }
                    }
                });
            }
        });
        
        $(document).on("click", ".glyphicon-edit", function(e) {
            $(this).parents(".drug-item").removeClass("drug-item-display").addClass("drug-item-edit");
            $(this).removeClass("glyphicon-edit").addClass("glyphicon-ok");
            $(this).attr('id', 'drug_row');
        });

        $('#add_drug').click(function (event) {
            // Prevent default posting of form
            event.preventDefault();

            var drug_item = $(this).parents('.drug-item-new');
            $.ajax({
                url:"/drug/create",
                data: {
                    'name': $(drug_item).find('input[name="name"]').val(),
                    'unit': $(drug_item).find('select[name="unit"]').val(),
                    'in_price': $(drug_item).find('input[name="in_price"]').val(),
                    'price': $(drug_item).find('input[name="price"]').val(),
                    'note': $(drug_item).find('input[name="note"]').val()
                },
                type: "POST",
                dataType: 'json',
                success:function(data) {
                    if (data.success) {
                    	$(drug_item).find('input[name="name"]').val("");
                    	$(drug_item).find('select[name="unit"]').val("");
                    	$(drug_item).find('input[name="in_price"]').val("");
                    	$(drug_item).find('input[name="price"]').val("");
                    	$(drug_item).find('input[name="note"]').val();
                        location.reload();
                    } else {
                        alert(data.error);
                    }
                }
            });
        });

        $(document).on("keypress", ".drug-item-new .form-control", function(e) {
        	if (e.which == 13) {
                $(this).parents(".drug-item-new").find("#add_drug").trigger("click");
            }
        });

        $(document).on("keypress", ".drug-item-edit .form-control", function(e) {
        	if (e.which == 13) {
                $(this).parents(".drug-item-edit").find(".drug-item-save").trigger("click");
            }
        });

        $('.glyphicon-remove').click(function () {
            if (confirm("Bạn có muốn xóa loại thuốc này?")){
                return true;
            } else {
                return false;
            }
        });

        $(".drug-search").blur(function() {
        	$('#drug-search-form').submit();
        });

        $(".drug-search").keypress(function(e) {
            if (e.which == 13) {
            	$('#drug-search-form').submit();
            }
        });
    });
</script>