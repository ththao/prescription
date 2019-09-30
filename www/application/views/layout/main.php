<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/partials/head');?>
<body>
    <?php $this->load->view('layout/partials/menu');?>
    <div class="container">
        <div class="hidden" id="loadingImage"></div>
        <?php if(isset($content)) $this->load->view($content['link'], $content['data']);?>
    </div>

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="../../../js/bootstrap.min.js"></script>
</body>
</html>
