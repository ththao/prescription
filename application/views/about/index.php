<div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading">
        <p>Mọi ý kiến phản hồi về phần mềm xin gửi tới địa chỉ email: thaoth.it@gmail.com</p>
        <form method="post" action="<?php echo site_url('migrate/import'); ?>" style="display: flex; margin: 20px 0;" enctype="multipart/form-data">
            <input type="file" class="form-control" name="db_backup">
            <select class="form-control" name="run">
            	<option value="DRUGS">DRUGS</option>
            	<option value="PATIENTS">PATIENTS</option>
            	<option value="DIAGNOSTICS">DIAGNOSTICS</option>
            	<option value="PRESCRIPTIONS">PRESCRIPTIONS</option>
            </select>
        	<button type="submit" name="submit">Import</button>
        </form>
    </div>
</div>