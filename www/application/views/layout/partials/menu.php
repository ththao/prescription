<nav class="navbar navbar-default">
    <div class="">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div id="navbar" class="navbar-collapse collapse" style="font-size: 18px;">
            <ul class="nav navbar-nav">
                <li <?php echo $this->router->fetch_class() == 'prescription' ? 'class="active"' : ''; ?>><a href="/prescription/index">Đơn thuốc</a></li>
                <li <?php echo $this->router->fetch_class() == 'patient' ? 'class="active"' : ''; ?>><a href="/patient/index">Danh sách bệnh nhân</a></li>
                <li <?php echo in_array($this->router->fetch_class(), ['auth', 'drug', 'service', 'report']) ? 'class="active"' : ''; ?>><a href="/auth/index">Quản lý</a></li>
                <li <?php echo $this->router->fetch_class() == 'about' ? 'class="active"' : ''; ?>><a href="/about">Liên hệ</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div><!--/.container-fluid -->
</nav>