<div class="btn-group">
	<a href="/drug/index" class="btn btn-default">Danh sách thuốc</a>
	<a href="/drug/import" class="btn btn-success">Thêm thuốc từ file</a>
	<?php if (SERVICES == 'ON'): ?><a href="/service/index" class="btn btn-default">Kỹ thuật</a><?php endif; ?>
	<a href="/report/daily" class="btn btn-default">Báo cáo ngày</a>
	<a href="/report/monthly" class="btn btn-default">Báo cáo tháng</a>
	<a href="/auth/password" class="btn btn-default">Đổi mật khẩu</a>
</div>

<!-- Main component for a primary marketing message or call to action -->
<div class="panel panel-default">
    <!-- Default panel contents -->

    <form method="post" action="<?php echo site_url('drug/import'); ?>" style="display: flex;" enctype="multipart/form-data">
        <input type="file" class="form-control" name="drugs">
    	<button type="submit" name="submit">Import</button>
    </form>
</div>