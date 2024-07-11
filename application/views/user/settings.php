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
            <h1>THIẾT LẬP TÀI KHOẢN</h1>
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
	                	<div class="row">
		                	<div class="col-md-6">Số phút bắt đầu tính giờ kế tiếp (nửa giờ)</div>
		                	<div class="col-md-6"><input class="form-control" type="text" name="half_hour_minutes" value="<?php echo $settings ? $settings->half_hour_minutes : '10'; ?>" placeholder="10"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-6">Số phút bắt đầu tính giờ kế tiếp (đủ giờ)</div>
		                	<div class="col-md-6"><input class="form-control" type="text" name="full_hour_minutes" value="<?php echo $settings ? $settings->full_hour_minutes : '30'; ?>" placeholder="30"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-6">Số giờ tối đa tính theo giờ</div>
		                	<div class="col-md-6"><input class="form-control" type="text" name="hourly_hours" value="<?php echo $settings ? $settings->hourly_hours : '6'; ?>" placeholder="6"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-6">Số giờ bắt đầu tính thuê ngày</div>
		                	<div class="col-md-6"><input class="form-control" type="text" name="full_day_hours" value="<?php echo $settings ? $settings->full_day_hours : '18'; ?>" placeholder="18"></div>
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