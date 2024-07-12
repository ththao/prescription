<div class="btn-group">
	<a href="/drug/index" class="btn <?php echo ($this->router->fetch_class() == 'drug' && $this->router->fetch_method() == 'index') ? 'btn-success' : 'btn-default'; ?>">Danh sách thuốc</a>
	<?php if (SERVICES == 'ON'): ?>
		<a href="/service/index" class="btn <?php echo ($this->router->fetch_class() == 'service' && $this->router->fetch_method() == 'index') ? 'btn-success' : 'btn-default'; ?>">Kỹ thuật</a>
		<a href="/package/index" class="hide btn <?php echo ($this->router->fetch_class() == 'package' && in_array($this->router->fetch_method(), ['index', 'update'])) ? 'btn-success' : 'btn-default'; ?>">Gói</a>
	<?php endif; ?>
	<a href="/report/daily" class="btn <?php echo ($this->router->fetch_class() == 'report' && $this->router->fetch_method() == 'daily') ? 'btn-success' : 'btn-default'; ?>">Báo cáo ngày</a>
	<a href="/report/monthly" class="btn <?php echo ($this->router->fetch_class() == 'report' && $this->router->fetch_method() == 'monthly') ? 'btn-success' : 'btn-default'; ?>">Báo cáo tháng</a>
	<a href="/auth/profile" class="btn <?php echo ($this->router->fetch_class() == 'auth' && $this->router->fetch_method() == 'profile') ? 'btn-success' : 'btn-default'; ?>">Thiết lập</a>
	<?php if (ADMIN_PASSWORD == 'ON'): ?>
		<a href="/auth/password" class="btn <?php echo ($this->router->fetch_class() == 'auth' && $this->router->fetch_method() == 'password') ? 'btn-success' : 'btn-default'; ?>">Đổi mật khẩu</a>
		<a href="/auth/admin_password" class="btn <?php echo ($this->router->fetch_class() == 'auth' && $this->router->fetch_method() == 'admin_password') ? 'btn-success' : 'btn-default'; ?>">Đổi mật khẩu quản lý</a>
	<?php endif; ?>
</div>