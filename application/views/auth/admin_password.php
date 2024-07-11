<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <a class="btn btn-default btn-back pull-left top15" href="/auth/manage">
            <span><img src="../../../../images/back.png"> Quay Lại</span>
        </a>
    </div>
</div>

<div style="clear: both;"></div>

<div id="content">
	<div class="col-md-3"></div>
    <div class="col-md-6 password-box">
    	<h2 align="center">MẬT KHẨU QUẢN LÝ</h2>
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
        		<button class="btn btn-default btn-save">Lưu</button>
        	</div>
    	</form>
    </div>
</div>