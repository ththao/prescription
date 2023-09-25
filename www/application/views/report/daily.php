<div class="btn-group">
	<a href="/drug/index" class="btn btn-default">Danh sách thuốc</a>
	<?php if (SERVICES == 'ON'): ?><a href="/service/index" class="btn btn-default">Kỹ thuật</a><?php endif; ?>
	<a href="/report/daily" class="btn btn-success">Báo cáo ngày</a>
	<a href="/report/monthly" class="btn btn-default">Báo cáo tháng</a>
	<a href="/auth/password" class="btn btn-default">Đổi mật khẩu</a>
</div>

<!-- Main component for a primary marketing message or call to action -->
<div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading"><h3>Tổng kết ngày <?php echo date("d", strtotime($date)); ?> tháng <?php echo date("m", strtotime($date)); ?> năm <?php echo date("Y", strtotime($date)); ?></h3></div>
	
	<form method="get" class="daily-report-form">
        <!-- Table -->
        <table class="table table-striped table-bordered" style="margin-bottom: 0px;">
            <tr>
                <th>Tên thuốc/dịch vụ</th>
                <th style="width: 120px;">Ngày</th>
                <th>Số lượng</th>
                <th>Nhập (VNĐ)</th>
                <th>Xuất (VNĐ)</th>
            </tr>
    
            <tr class="drug-item-new">
                <td><input type="text" class="form-control search-drug" placeholder="Tìm theo tên" value="<?php echo $drug_name ?>" name="drug_name"></td>
                <td><input type="text" class="form-control search-date" name="date" value="<?php echo $date; ?>" style="text-align: center;"></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    		
    		<?php $in_total = 0; ?>
    		<?php $total = 0; ?>
            <?php foreach ($data as $item) { ?>
                <tr class="drug-item drug-item-display">
                    <td><label class="display"><?php echo $item->name; ?></label></td>
                    <td align="center"><label class="display"><?php echo date("d-m-Y", strtotime($date)); ?></label></td>
                    <td align="right"><label class="display"><?php echo $item->quantity; ?></label></td>
                    <td align="right"><label class="display"><?php echo number_format($item->in_price, 0); ?></label></td>
                    <td align="right"><label class="display"><?php echo number_format($item->price, 0); ?></label></td>
                </tr>
                
                <?php $in_total+= $item->in_price; ?>
                <?php $total += $item->price; ?>
            <?php } ?>
            
            <tr>
            	<td colspan="3"><h3>Tổng cộng:</h3></td>
            	<td align="right"><h3><?php echo number_format($in_total, 0); ?></h3></td>
            	<td align="right"><h3><?php echo number_format($total, 0); ?></h3></td>
            </tr>
        </table>
    </form>
</div>

<script>
$(document).ready(function() {
	$('.search-date').datepicker({
        dateFormat:'dd-mm-yy',
        onSelect: function () {
            $(".daily-report-form").submit();
        }
    });

    $('.search-drug').blur(function() {
    	$(".daily-report-form").submit();
    });
});
</script>