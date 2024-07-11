<div id="content">
    <div class="panel-heading">
		<h3 align="center">Đăng nhập</h3>
	</div>

	<div align="center">
		<form method="post" class="login-form">
            <?php if ($err_message) { ?>
                <div class="alert alert-error"><?php echo $err_message; ?></div>
            <?php } ?>
			<div style="width: 300px; display: flex; padding: 10px;">
				<input type="text" class="form-control txt-username" name="username" />
			</div>
			<div style="width: 300px; display: flex; padding: 10px;">
				<input type="password" class="form-control txt-password" name="password" />
			</div>
			<div><button class="btn btn-default btn-login">OK</button></div>
		</form>
	</div>
</div>

<script>
$(document).ready(function() {
	$('.txt-password, .txt-username').keypress(function(e) {
		if (e.which == 13) {
			$('.btn-login').trigger("click");
		}
	});
	
	$(document).on('click', '.btn-login', function(e) {
		e.preventDefault();

		if ($('.txt-username').val() == '') {
			$('.txt-username').parent().addClass('has-error');
			$('.txt-username').focus();
			return false;
		}

		if ($('.txt-password').val() == '') {
			$('.txt-password').parent().addClass('has-error');
			$('.txt-password').focus();
			return false;
		}

		$('.txt-username').parent().removeClass('has-error');
		$('.txt-password').parent().removeClass('has-error');
		$('.login-form').submit();
	});

	$(document).on('click', '.btn-pay-request', function(e) {
		e.preventDefault();
		var selected = $(this);

		$.ajax({
	        url: '/about/pay_request',
	        type: 'POST',
	        data: {
		        user_id: $(selected).attr('user_id')
	        },
	        dataType: 'json',
	        success: function (response) {
	        	if (response.status) {
	        		$.notify(response.message, {className: 'success', position: "right"});
	        	}
	        }
	    });
	});
});
</script>