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
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script src="../../../js/bootstrap.min.js"></script>
</body>
</html>
