<style>
    .min-row td {
        padding: 2px !important;
    }
</style>

<?php $this->load->view('layout/partials/admin_menu'); ?>


<div class="panel panel-default">
    <!-- Main component for a primary marketing message or call to action -->
    <form id="package-form" method="post" style="margin-right: 0px" >
        <div style="text-align: center; font-size: 28px">Gói</div>
        <!-- Table -->
        <table class="table table-bordered" width="100%" border="0">
            <tr class="min-row">
                <td>
                	<input type="text" class="form-control package-name" placeholder="Tên" name="package[package_name]" value="<?php echo (isset($package) && $package) ? $package->package_name : ''; ?>" />
                	<input type="hidden" class="package_id" name="package[id]" value="<?php echo (isset($package) && $package) ? $package->id : ''; ?>" />
                </td>
            </tr>
            <tr class="min-row">
                <td colspan="3"><input type="text" class="form-control package-notes" placeholder="Ghi chú" name="package[notes]" value="<?php echo (isset($package) && $package) ? $package->notes : ''; ?>"></td>
            </tr>
        </table>
        
        <?php if (SERVICES == 'ON'): ?>
        <div class="btn-group">
        	<a href="#" class="btn btn-default btn-show-doctor-orders">Chỉ định</a>
        	<a href="#" class="btn btn-default btn-show-prescription">Đơn thuốc</a>
        </div>
        <?php endif; ?>
        
        <?php $this->load->view('prescription/index_services'); ?>
    	
    	<?php $this->load->view('prescription/index_drugs'); ?>
    	<div style="margin-top: 10px;">
    		<a href="/package/index" class="btn btn-warning pull-left">Về Danh Sách</a>
        	<button type="submit" class="btn btn-success save-item pull-right" package_id="<?php echo (isset($package) && $package) ? $package->id : ''; ?>">Lưu gói</button>
        </div>
        <br><br><br>
    </form>
</div>

<script>
    $(document).ready(function() {
    	$('.btn-show-doctor-orders').trigger('click');
    	
        $('.save-item').click(function (event) {
            // Prevent default posting of form
            event.preventDefault();

            var selected = $(this);
            if ($(selected).hasClass("disabled")) {
                return false;
            }
            var caption = $(selected).html();

            $.ajax({
                url:"/package/save/" + $(selected).attr('package_id'),
                data: $('#package-form').serializeArray(),
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $(selected).html("Đang lưu ...").addClass("disabled");
                },
                success:function(data) {
                    if (data.success) {
                        window.location = data.url;
                    } else {
                        alert(data.error);
                        $(selected).html(caption).removeClass("disabled");
                    }
                },
                complete: function() {
                	$(selected).html(caption).removeClass("disabled");
                }
            });
        });
    });
</script>
