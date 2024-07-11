<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title">
        <div class="col-md-2 dropdown-select" style="float: left;">
            <a class="btn-default form-control btn-dropdown btn-back" href="/auth/manage">
                <span><img src="../../../../images/back.png"> Quay Lại</span>
            </a>
        </div>
        <div class="col-md-8 page-name" style="float: left;">
            <h1>QUẢN LÝ ACCOUNTS</h1>
        </div>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div id="room-setting">
            <div class="manage-users">
                <table id="users-table" class="table hotel-table">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Expires At</th>
                        <th></th>
                    </tr>
                    <tr>
                    	<form method="get">
                        <th></th>
                        <th><input type="text" class="form-control" name="t" value="<?php echo $t; ?>" placeholder="name or phone"/></th>
                        <th></th>
                        <th></th>
                        <th><button class="btn btn-default btn-success" type="submit">Tìm Kiếm</button></th>
                        </form>
                    </tr>
					<?php foreach ($users as $user): ?>
                    <tr>
                        <td><p class="pad10px"><?php echo $user->id; ?></p></td>
                        <td><p class="pad10px"><?php echo $user->name; ?></p></td>
                        <td><p class="pad10px"><?php echo $user->address; ?></p></td>
                        <td><p class="pad10px"><?php echo $user->expired_at ? date('Y-m-d', $user->expired_at) : ''; ?></p></td>
                        <td align="right">
                        	<a class="btn btn-success btn-edit" href="/user/update/<?php echo $user->id; ?>">Cập nhật</a>
                        	<a class="btn btn-success btn-report" href="/user/enable_report/<?php echo $user->id; ?>"><?php echo $user->report_enable ? 'Tắt báo cáo' : 'Bật báo cáo'; ?></a>
                            <a class="btn btn-danger btn-remove" href="/user/delete/<?php echo $user->id; ?>"><?php echo $user->deleted_at ? 'Khôi phục' : 'Xóa'; ?></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <div class="add-floor-button pull-right">
                	<a href="/user/create">
                        <img src="../../../../images/add.png" id="add-user" class="pointer">
                        <p align="center"><strong>ADD</strong></p>
                    </a>
                </div>
            </div>
            <div class="clear-fix"></div>
        </div>
    </div>
</div>
<!--End content-->

<script type="text/javascript">
$(document).ready(function() {
	$(document).on('click', '.btn-remove', function(e) {
		e.preventDefault();

		if (confirm('Bạn có chắc chắn muốn ' + $(this).html() + ' dữ liệu này?')) {
			window.location = $(this).attr('href');
		}
	});
	
	$(document).on('click', '.btn-report', function(e) {
		e.preventDefault();

		var selected = $(this);
		if (!confirm('Bạn muốn ' + $(selected).html() + ' user đã chọn?')) {
			return false;
		}

		$.ajax({
            url: $(selected).attr("href"),
            type: 'POST',
            dataType: 'json',
            success: function (response) {
            	if (response.status == 1) {
                	if (response.report_enable) {
                		$(selected).html('Tắt báo cáo');
                	} else {
                		$(selected).html('Bật báo cáo');
                	}
            	} else {
            		window.location.reload();
            	}
            }
        });
	});
});
</script>