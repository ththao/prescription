<style>
<!--
.package-item-display .display {
    display: block;
}
.package-item-display .edit {
    display: none;
}
.package-item-edit .display {
    display: none;
}
.package-item-edit .edit {
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
        <a class="btn btn-default pull-right" href="/package/update" title="Tạo gói" style="margin-bottom: 10px;">Tạo gói mới</a>
    </div>

    <!-- Table -->
    <table class="table table-striped table-bordered" style="margin-bottom: 0px;">
        <tr>
            <th>Tên gói</th>
            <th>Đơn giá</th>
            <th>Ghi chú</th>
            <th>Sửa / Xóa</th>
        </tr>

        <tr class="package-item-search">
            <td>
                <form method="get" action="<?php echo site_url('package/search'); ?>" id="package-search-form">
                    <input type="text" class="form-control package-search" placeholder="Tìm kiếm" name="search" value="<?php echo $search; ?>">
                </form>
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <?php foreach ($models['packages'] as $item) { ?>
        	<?php
                $title = '';
                $price = 0;
                if ($item->orders) {
                    $title = 'Chỉ định:&#013;';
                    foreach ($item->orders as $order) {
                        $title .= ' - ' . $order->service_name . '&#013;';
                        $price += $order->price;
                    }
                }
                if ($item->prescriptions) {
                    $title .= 'Thuốc:&#013;';
                    foreach ($item->prescriptions as $prescription) {
                        $price += $order->price;
                        $title .= ' - ' . $prescription->drug_name . '&#013;';
                    }
                }
        	?>
            <tr class="package-item package-item-display">
                <td><label class="display"><?php echo $item->package_name; ?></label></td>
                <td><label class="display" title="<?php echo $title; ?>"><?php echo number_format($price, 0, ',', '.'); ?></label></td>
                <td><label class="display"><?php echo $item->notes; ?></label></td>
                <td style="text-align: center; width: 100px">
                    <a class="glyphicon glyphicon-edit package-item-edit" href="/package/update/<?php echo $item->id ?>" title="Cập nhật gói" style="color: blue; cursor: pointer; "></a>&nbsp;
                    <a href="/package/delete/<?php echo $item->id ?>" title="Xóa gói"><span title="Xóa thuốc" class="glyphicon glyphicon-remove" style="color: red"></span></a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<script>
    $(document).ready(function() {
    	
        $('.glyphicon-remove').click(function () {
            if (confirm("Bạn có muốn xóa gói này?")){
                return true;
            } else {
                return false;
            }
        });

        $(".package-search").blur(function() {
        	$('#package-search-form').submit();
        });

        $(".package-search").keypress(function(e) {
            if (e.which == 13) {
            	$('#package-search-form').submit();
            }
        });
    });
</script>