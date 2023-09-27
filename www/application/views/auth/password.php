<?php $this->load->view('layout/partials/admin_menu'); ?>

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