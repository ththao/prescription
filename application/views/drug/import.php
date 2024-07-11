<?php $this->load->view('layout/partials/admin_menu'); ?>

<!-- Main component for a primary marketing message or call to action -->
<div class="panel panel-default">
    <!-- Default panel contents -->

    <form method="post" action="<?php echo site_url('drug/import'); ?>" style="display: flex;" enctype="multipart/form-data">
        <input type="file" class="form-control" name="drugs">
    	<button type="submit" name="submit">Import</button>
    </form>
</div>