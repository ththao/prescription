<div class="btn-group">
	<a href="/drug/index" class="btn <?php echo ($this->router->fetch_class() == 'drug' && $this->router->fetch_method() == 'index') ? 'btn-success' : 'btn-default'; ?>">Danh sách thuốc</a>
	<?php if (SERVICES == 'ON'): ?><a href="/service/index" class="btn <?php echo ($this->router->fetch_class() == 'service' && $this->router->fetch_method() == 'index') ? 'btn-success' : 'btn-default'; ?>">Kỹ thuật</a><?php endif; ?>
	<a href="/report/daily" class="btn <?php echo ($this->router->fetch_class() == 'report' && $this->router->fetch_method() == 'daily') ? 'btn-success' : 'btn-default'; ?>">Báo cáo ngày</a>
	<a href="/report/monthly" class="btn <?php echo ($this->router->fetch_class() == 'report' && $this->router->fetch_method() == 'monthly') ? 'btn-success' : 'btn-default'; ?>">Báo cáo tháng</a>
	<?php if (ADMIN_PASSWORD == 'ON'): ?><a href="/auth/password" class="btn <?php echo ($this->router->fetch_class() == 'auth' && $this->router->fetch_method() == 'password') ? 'btn-success' : 'btn-default'; ?>">Đổi mật khẩu</a><?php endif; ?>
</div>