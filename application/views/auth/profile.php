<?php $this->load->view('layout/partials/admin_menu'); ?>

<!-- Main component for a primary marketing message or call to action -->
<div class="panel panel-default">
    <div class="password-box" style="padding: 20px;">
    	<h2 align="center">THIẾT LẬP</h2>
    	<form method="post">
            <?php if ($message) { ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php } ?>
            <?php if ($err_message) { ?>
                <div class="alert alert-error"><?php echo $err_message; ?></div>
            <?php } ?>
        	<div class="row" style="padding-bottom: 10px; padding-top: 10px;">
        		<div class="col-md-4">
        			<label style="text-align: right;">Tên phòng khám:</label>
        		</div>
        		<div class="col-md-8">
        			<input type="text" class="form-control txt-name" name="name" value="<?php echo $user->name; ?>"/>
        		</div>
        	</div>
        	<div class="row" style="padding-bottom: 10px; padding-top: 10px;">
        		<div class="col-md-4">
        			<label style="text-align: right;">Tên bác sĩ:</label>
        		</div>
        		<div class="col-md-8">
        			<input type="text" class="form-control txt-fullname" name="fullname" value="<?php echo $user->fullname; ?>"/>
        		</div>
        	</div>
        	<div class="row" style="padding-bottom: 10px; padding-top: 10px;">
        		<div class="col-md-4">
        			<label style="text-align: right;">Địa chỉ:</label>
        		</div>
        		<div class="col-md-8">
        			<input type="text" class="form-control txt-address" name="address" value="<?php echo $user->address; ?>"/>
        		</div>
        	</div>
        	<div class="row" style="padding-bottom: 10px; padding-top: 10px;">
        		<div class="col-md-4">
        			<label style="text-align: right;">Số điện thoại:</label>
        		</div>
        		<div class="col-md-8">
        			<input type="text" class="form-control txt-phone" name="phone" value="<?php echo $user->phone; ?>"/>
        		</div>
        	</div>
        	<div class="row" style="padding-bottom: 10px; padding-top: 10px;" align="center">
        		<button class="btn btn-default  btn-save">Lưu</button>
        	</div>
    	</form>
    </div>
</div>