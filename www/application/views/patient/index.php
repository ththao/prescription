<!-- Main component for a primary marketing message or call to action -->
<form method="get" id="patient-form" action="<?php echo site_url('patient/index'); ?>">
    <div class="panel panel-default">
        <!-- Default panel contents -->
        <?php if ($history): ?>
        <?php else: ?>
        <div class="panel-heading">Danh sách bệnh nhân</div>
        <?php endif; ?>

        <!-- Table -->
        <table class="table table-striped table-bordered" style="margin-bottom: 0px;">
            <tr>
                <th style="width: 100px">Ngày</th>
                <th style="width: 250px">Tên</th>
                <?php if ($history): ?>
                    <th>Chẩn đoán</th>
                    <th>Ghi chú</th>
                <?php else: ?>
                    <th style="width: 81px">Năm sinh</th>
                    <th style="width: 70px">Nam/Nữ</th>
                    <th>Chẩn đoán</th>
                    <th style="width: 100px">Chi tiết</th>
                <?php endif; ?>
            </tr>

            <tr>
                <td><input type="text" class="form-control" placeholder="Tìm ngày" name="date" id="date" value="<?php echo isset($param) && isset($param['date']) ? $param['date'] : '' ?>"></td>
                <?php if ($history): ?>
                	<td></td><td></td><td></td>
                <?php else: ?>
                    <td><input onblur="search()" type="text" class="form-control" placeholder="Tìm tên" id="patient-search" value="<?php echo isset($param) && isset($param['name']) ? $param['name'] : '' ?>" name="patient-search"></td>
                    <td></td><td></td><td></td><td></td>
                <?php endif; ?>
            </tr>

            <?php foreach ($models['patients'] as $item) { ?>
                <tr class="patient-item patient-item-display">
                    <td><?php echo date("d-m-Y", strtotime($item->date_created)); ?></td>
                    <?php if ($history): ?>
                    	<td title="<?php echo $item->address; ?>"><label class="display"><?php echo $item->name ?></label></td>
                        <td title="<?php echo $item->prescription; ?>"><label class="display"><?php echo $item->diagnostic ?></label></td>
                        <td><label class="display"><?php echo $item->note ?></label></td>
                    <?php else: ?>
                    	<td title="<?php echo $item->address; ?>"><label class="display"><a href="/patient/index?patient_id=<?php echo $item->id; ?>"><?php echo $item->name ?></a></label></td>
                        <td title="<?php echo $item->prescription; ?>"><label class="display"><?php echo $item->dob ?></label></td>
                        <td><label class="display"><?php echo $item->gender ?></label></td>
                        <td><label class="display"><?php echo $item->diagnostic ?></label></td>
                        <td style="text-align: center; width: 100px">
                            <a href="/patient/view/<?php echo $item->diagnostic_id; ?>" title="Xem chi tiết">
                                <span title="Xem chi tiết" class="glyphicon glyphicon-search" style="color: green; margin-right: 5px;"></span>
                            </a>
                            <a href="/prescription/index?diagnostic_id=<?php echo $item->diagnostic_id; ?>" title="Chỉnh sửa">
                                <span title="Chỉnh sửa" class="glyphicon glyphicon-edit" style="color: blue; margin-right: 5px;"></span>
                            </a>
                            <a href="/patient/delete/<?php echo $item->diagnostic_id; ?>" title="Xóa">
                                <span title="Xóa" class="glyphicon glyphicon glyphicon-remove" style="color: red"></span>
                            </a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php } ?>

        </table>
		
		<?php if (isset($models['pagination']) && $models['pagination']): ?>
            <div class="navbar-form" role="search" style="margin-right: 0px" >
                <nav class="pull-left">
                    <?php echo $models['pagination']; ?>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</form>
        
<?php if (isset($param) && isset($param['patient_id']) && $param['patient_id']): ?>
<div style="clear: both;">
	<a href="/prescription/index?patient_id=<?php echo $param['patient_id']; ?>" class="btn btn-success add-new-prescription">Tạo đơn thuốc mới</a>
	<a href="/patient/index" class="btn btn-warning" id="back">Quay về danh sách</a>
</div>
<?php endif; ?>

<script>
    $(document).ready(function() {
        $('#date').datepicker({
            dateFormat:'dd-mm-yy',
            onSelect: function (date) {
                search();
            }
        });

        $('#date, #patient-search').keypress(function (e) {
			if (e.which == 13) {
				search();
			}
		});

        $('.glyphicon-remove').click(function () {
            if (confirm("Bạn có muốn xóa bệnh nhân này?")){
                return true;
            } else {
                return false;
            }
        });
    });

    function search() {
        $('#patient-form').submit();
    }

</script>