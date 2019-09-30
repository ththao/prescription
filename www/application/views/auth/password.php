<div class="btn-group">
	<a href="/drug/index" class="btn btn-default">Danh sách thuốc</a>
	<a href="/report/daily" class="btn btn-default">Tổng kết ngày</a>
	<a href="/report/monthly" class="btn btn-default">Tổng kết tháng</a>
	<a href="/auth/password" class="btn btn-success">Đổi mật khẩu</a>
</div>

<div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading">
        <h3 align="center">Đổi mật khẩu quản lý</h3>
    </div>
    
    <div align="center">
    	<form method="post">
            <?php if ($message) { ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php } ?>
            <?php if ($err_message) { ?>
                <div class="alert alert-danger"><?php echo $err_message; ?></div>
            <?php } ?>
        	<div style="width: 400px; display: flex; padding-bottom: 10px; padding-top: 10px;">
        		<label style="width: 200px; margin-right: 20px; text-align: right;">Mật khẩu cũ:</label>
        		<input type="password" class="form-control txt-old-password" name="old_password" />
        	</div>
        	<div style="width: 400px; display: flex; padding-bottom: 10px;">
        		<label style="width: 200px; margin-right: 20px; text-align: right;">Mật khẩu mới:</label>
        		<input type="password" class="form-control txt-new-password" name="new_password" />
        	</div>
        	<div style="width: 400px; padding-bottom: 10px;" align="center">
        		<button class="btn btn-default btn-save">Lưu</button>
        	</div>
    	</form>
    </div>
</div>