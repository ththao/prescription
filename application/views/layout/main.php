<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/partials/head');?>
<body>
    <?php $this->load->view('layout/partials/menu');?>
    <div class="container">
        <div class="hidden" id="loadingImage"></div>
        <?php if(isset($content)) $this->load->view($content['link'], $content['data']);?>
    </div>
</body>
</html>
