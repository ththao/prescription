<div class="btn-group">
	<a href="/drug/index" class="btn btn-default">Danh sách thuốc</a>
	<a href="/report/daily" class="btn btn-default">Tổng kết ngày</a>
	<a href="/report/monthly" class="btn btn-success">Tổng kết tháng</a>
	<a href="/auth/password" class="btn btn-default">Đổi mật khẩu</a>
</div>

<!-- Main component for a primary marketing message or call to action -->
<div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading"><h3>Tổng kết tháng <?php echo $month; ?> năm <?php echo $year; ?></h3></div>
	
	<form method="get" class="monthly-report-form">
        <!-- Table -->
        <table class="table table-striped table-bordered" style="margin-bottom: 0px;">
            <tr>
                <th>Tên thuốc</th>
                <th style="width: 200px;">Tháng</th>
                <th>Số lượng</th>
                <th>Nhập (VNĐ)</th>
                <th>Xuất (VNĐ)</th>
            </tr>
    
            <tr class="drug-item-new">
                <td><input type="text" class="form-control search-drug" placeholder="Tìm theo tên" value="<?php echo $drug_name ?>" name="drug_name"></td>
                <td>
                	<select name="month" class="search-month form-control pull-left" style="width: 40%;">
    					<?php for ($m=1; $m<=12; $m++): ?>
    						<option value="<?php echo $m; ?>" <?php echo $m==$month ? 'selected' : ''; ?>><?php echo $m; ?></option>
    					<?php endfor; ?>
    				</select>
    				
    				<select name="year" class="search-year form-control pull-left" style="width: 60%;">
    					<?php $cy = date('Y'); ?>
    					<?php for ($y=$cy-5; $y<=$cy; $y++): ?>
    						<option value="<?php echo $y; ?>" <?php echo $y==$year ? 'selected' : ''; ?>><?php echo $y; ?></option>
    					<?php endfor; ?>
    				</select>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    		
    		<?php $in_total = 0; ?>
    		<?php $total = 0; ?>
            <?php foreach ($data as $item) { ?>
                <tr class="drug-item drug-item-display">
                    <td><label class="display"><?php echo $item->drug_name; ?></label></td>
                    <td align="center"><label class="display"><?php echo ($month < 10 ? '0' . intval($month) : $month) . '-' . $year; ?></label></td>
                    <td align="right"><label class="display"><?php echo $item->drug_quantity; ?></label></td>
                    <td align="right"><label class="display"><?php echo number_format($item->in_price, 0); ?></label></td>
                    <td align="right"><label class="display"><?php echo number_format($item->price, 0); ?></label></td>
                </tr>
                <?php $in_total += $item->in_price; ?>
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
	$('.search-month, .search-year').change(function() {
    	$(".monthly-report-form").submit();
    });
    $('.search-drug').blur(function() {
    	$(".monthly-report-form").submit();
    });
});
</script>