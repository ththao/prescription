<style>
.has-error input {
    border: solid 1px red;
}
</style>

<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title">
        <div class="col-md-2 dropdown-select" style="float: left;">
            <a class="btn-default form-control btn-dropdown btn-back" href="/user">
                <span><img src="../../../../images/back.png"> Về Danh Sách</span>
            </a>
        </div>
        <div class="col-md-8 page-name" style="float: left;">
            <h1>ACCOUNT <?php echo $user ? $user->name : ' MỚI'; ?></h1>
        </div>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div id="detail">
            <div class="detail-title">
                
            </div>
            <div class="col-md-12">
                
                <div class="row rent-item-detail">
                	<form method="post">
                		<input type="hidden" name="id" value="<?php echo $user ? $user->id : ''; ?>" />
	                	<div class="row">
		                	<div class="col-md-3">Email</div>
		                	<div class="col-md-9"><input class="form-control" type="text" name="email" value="<?php echo $user ? $user->email : ''; ?>"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Username</div>
		                	<div class="col-md-9"><input class="form-control" type="text" name="username" value="<?php echo $user ? $user->username : ''; ?>"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Password</div>
		                	<div class="col-md-9"><input class="form-control" type="password" name="password" value=""></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Confirm Password</div>
		                	<div class="col-md-9"><input class="form-control" type="password" name="re_password" value=""></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Admin Password</div>
		                	<div class="col-md-9"><input class="form-control" type="password" name="admin_password" value=""></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Confirm Admin Password</div>
		                	<div class="col-md-9"><input class="form-control" type="password" name="re_admin_password" value=""></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Phone</div>
		                	<div class="col-md-9"><input class="form-control" type="text" name="phone" value="<?php echo $user ? $user->phone: ''; ?>"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Address</div>
		                	<div class="col-md-9"><input class="form-control" type="text" name="address" value="<?php echo $user ? $user->address : ''; ?>"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Name</div>
		                	<div class="col-md-9"><input class="form-control" type="text" name="name" value="<?php echo $user ? $user->name : ''; ?>"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Fullname</div>
		                	<div class="col-md-9"><input class="form-control" type="text" name="fullname" value="<?php echo $user ? $user->fullname : ''; ?>"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Tax ID</div>
		                	<div class="col-md-9"><input class="form-control" type="text" name="tax_id" value="<?php echo $user ? $user->tax_id: ''; ?>"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Expire</div>
		                	<div class="col-md-9"><input class="form-control" type="text" name="expired_at" value="<?php echo $user ? date('d-m-Y', $user->expired_at) : ''; ?>"></div>
	                	</div>
	                
			        	<div class="row">
			        		<div class="col-md-3"></div>
			        		<div class="col-md-3">
				        		<button class="btn btn-success btn-save">Lưu</button>
			        		</div>
			        	</div>
			        </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$(document).on('click', '.btn-save', function(e) {
		e.preventDefault();

		$('.has-error').removeClass('has-error');

		var has_error = false;
		if ($('input[name="username"]').val() == '') {
			$('input[name="username"]').parent().addClass('has-error');
			has_error = true;
		}
		
		if ($('input[name="id"]').val() == '' && $('input[name="password"]').val() == '') {
			$('input[name="password"]').parent().addClass('has-error');
			has_error = true;
		}
		if ($('input[name="re_password"]').val() != $('input[name="password"]').val()) {
			$('input[name="re_password"]').parent().addClass('has-error');
			has_error = true;
		}
		if ($('input[name="id"]').val() == '' && $('input[name="admin_password"]').val() == '') {
			$('input[name="admin_password"]').parent().addClass('has-error');
			has_error = true;
		}
		if ($('input[name="re_admin_password"]').val() != $('input[name="admin_password"]').val()) {
			$('input[name="re_admin_password"]').parent().addClass('has-error');
			has_error = true;
		}

		if (!has_error) {
			$(this).parents('form').submit();
		}
	});
});
</script>