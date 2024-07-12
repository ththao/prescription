<?php $this->load->view('layout/partials/admin_menu'); ?>

<!-- Main component for a primary marketing message or call to action -->
<div class="panel panel-default">
    <div class="password-box" style="padding: 20px;">
    	<h2 align="center">MẬT KHẨU</h2>
    	<form method="post">
            <?php if ($message) { ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php } ?>
            <?php if ($err_message) { ?>
                <div class="alert alert-error"><?php echo $err_message; ?></div>
            <?php } ?>
        	<div class="row" style="padding-bottom: 10px; padding-top: 10px;">
        		<div class="col-md-4">
        			<label style="text-align: right;">Mật khẩu cũ:</label>
        		</div>
        		<div class="col-md-8">
        			<input type="password" class="form-control txt-old-password" name="old_password" />
        		</div>
        	</div>
        	<div class="row" style="padding-bottom: 10px; padding-top: 10px;">
        		<div class="col-md-4">
        			<label style="text-align: right;">Mật khẩu mới:</label>
        		</div>
        		<div class="col-md-8">
        			<input type="password" class="form-control txt-new-password" name="new_password" />
        		</div>
        	</div>
        	<div class="row" style="padding-bottom: 10px; padding-top: 10px;">
        		<div class="col-md-4">
        			<label style="text-align: right;">Mật khẩu mới lần 2:</label>
        		</div>
        		<div class="col-md-8">
        			<input type="password" class="form-control txt-new-password" name="new_re_password" />
        		</div>
        	</div>
        	<div class="row" style="padding-bottom: 10px; padding-top: 10px;" align="center">
        		<button class="btn btn-default  btn-save">Lưu</button>
        	</div>
    	</form>
    </div>
</div>