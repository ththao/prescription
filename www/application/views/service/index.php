<style>
<!--
.service-item-display .display {
    display: block;
}
.service-item-display .edit {
    display: none;
}
.service-item-edit .display {
    display: none;
}
.service-item-edit .edit {
    display: block;
}
-->
</style>

<?php $this->load->view('layout/partials/admin_menu'); ?>

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
            <th>Kỹ thuật</th>
            <th style="width: 150px">Đơn giá (VNĐ)</th>
            <th>Ghi chú</th>
            <th>Sửa / Xóa</th>
        </tr>

        <tr class="service-item-search">
            <td>
                <form method="get" action="<?php echo site_url('service/search'); ?>" id="service-search-form">
                    <input type="text" class="form-control service-search" placeholder="Tìm kiếm" name="search" value="<?php echo $search; ?>">
                </form>
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <?php foreach ($models['services'] as $item) { ?>
            <tr class="service-item service-item-display">
                <td>
                    <label class="display"><?php echo $item->service_name ?></label>
                    <input type="text" value="<?php echo $item->service_name ?>" class="form-control edit service-name" name="service_name"/>
                </td>
                <td>
                    <label class="display"><?php echo number_format($item->price, 0, ',', '.') ?></label>
                    <input type="text" value="<?php echo $item->price ?>" class="form-control edit service-price" name="price"/>
                </td>
                <td>
                    <label class="display"><?php echo $item->notes ?></label>
                    <input type="text" value="<?php echo $item->notes ?>" class="form-control edit service-note" name="notes"/>
                </td>
                <td style="text-align: center; width: 100px">
                    <span class="glyphicon glyphicon-edit service-item-save" service-url="/service/update/<?php echo $item->id ?>" title="Cập nhật" style="color: blue; cursor: pointer; "></span>&nbsp;
                    <a href="/service/delete/<?php echo $item->id ?>" title="Xóa"><span title="Xóa thuốc" class="glyphicon glyphicon-remove" style="color: red"></span></a>
                </td>
            </tr>
        <?php } ?>
		<tr><td colspan="5"></td></tr>
        <tr class="service-item-new">
            <td><input type="text" class="form-control" placeholder="Kỹ Thuật" name="service_name"></td>
            <td><input type="number" class="form-control" placeholder="Đơn Giá" name="price"></td>
            <td><input type="text" class="form-control" placeholder="Ghi chú" name="notes"></td>
            <td style="text-align: center"><input type="submit" class="btn btn-success" id="add_service" value="Thêm"></td>
        </tr>
    </table>
</div>

<script>
    $(document).ready(function() {
    	$('.service-item-save').click(function (event) {
            if ($('#service_row').length) {
                // Prevent default posting of form
                event.preventDefault();

                var selected = $(this);
                $.ajax({
                    url: $(selected).attr("service-url"),
                    data: {
                        'service_name': $(selected).parents('.service-item-edit').find('input[name="service_name"]').val(),
                        'price': $(selected).parents('.service-item-edit').find('input[name="price"]').val(),
                        'notes': $(selected).parents('.service-item-edit').find('input[name="notes"]').val()
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
            $(this).parents(".service-item").removeClass("service-item-display").addClass("service-item-edit");
            $(this).removeClass("glyphicon-edit").addClass("glyphicon-ok");
            $(this).attr('id', 'service_row');
        });

        $('#add_service').click(function (event) {
            // Prevent default posting of form
            event.preventDefault();

            var service_item = $(this).parents('.service-item-new');
            $.ajax({
                url:"/service/create",
                data: {
                    'service_name': $(service_item).find('input[name="service_name"]').val(),
                    'price': $(service_item).find('input[name="price"]').val(),
                    'notes': $(service_item).find('input[name="notes"]').val()
                },
                type: "POST",
                dataType: 'json',
                success:function(data) {
                    if (data.success) {
                    	$(service_item).find('input[name="name"]').val("");
                    	$(service_item).find('input[name="price"]').val("");
                    	$(service_item).find('input[name="note"]').val();
                        location.reload();
                    } else {
                        alert(data.error);
                    }
                }
            });
        });

        $(document).on("keypress", ".service-item-new .form-control", function(e) {
        	if (e.which == 13) {
                $(this).parents(".service-item-new").find("#add_service").trigger("click");
            }
        });

        $(document).on("keypress", ".service-item-edit .form-control", function(e) {
        	if (e.which == 13) {
                $(this).parents(".service-item-edit").find(".service-item-save").trigger("click");
            }
        });

        $('.glyphicon-remove').click(function () {
            if (confirm("Bạn có muốn xóa?")){
                return true;
            } else {
                return false;
            }
        });

        $(".service-search").blur(function() {
        	$('#service-search-form').submit();
        });

        $(".service-search").keypress(function(e) {
            if (e.which == 13) {
            	$('#service-search-form').submit();
            }
        });
    });
</script>