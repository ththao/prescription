<!-- Main component for a primary marketing message or call to action -->
<form method="get" id="patient-form" action="<?php echo site_url('patient/search'); ?>">
    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">Danh sách bệnh nhân</div>

        <!-- Table -->
        <table class="table table-striped table-bordered" style="margin-bottom: 0px;">
            <tr>
                <th style="width: 180px">Ngày</th>
                <th>Tên</th>
                <th style="width: 100px">Năm sinh</th>
                <th style="width: 100px">Giới tính</th>
                <th>Chẩn đoán</th>
                <th style="width: 100px">Chi tiết</th>
            </tr>

            <tr>
                <td><input type="text" class="form-control" placeholder="Tìm theo ngày khám" name="date" id="date" value="<?php echo isset($param[1]) ? $param[1] : '' ?>"></td>
                <td><input onblur="search()" type="text" class="form-control" placeholder="Tìm theo tên" id="patient-search" value="<?php echo isset($param[0]) ? $param[0] : '' ?>" name="patient-search"></td>
                <td></td><td></td><td></td><td></td>
            </tr>

            <?php foreach ($models['patients'] as $item) { ?>
                <tr class="patient-item patient-item-display">
                    <td><?php echo date("d-m-Y", strtotime($item->date_created)); ?></td>
                    <td><label class="display"><?php echo $item->name ?></label></td>
                    <td><label class="display"><?php echo $item->dob ?></label></td>
                    <td><label class="display"><?php echo $item->gender ?></label></td>
                    <td><label class="display"><?php echo $item->diagnostic ?></label></td>
                    <td style="text-align: center; width: 100px">
                        <a href="/patient/view/<?php echo $item->id ?>" title="Xem chi tiết bệnh nhân">
                            <span title="Xem chi tiết bệnh nhân" class="glyphicon glyphicon-search" style="color: green; margin-right: 5px;"></span>
                        </a>
                        <a href="/prescription/index?patient_id=<?php echo $item->id ?>" title="Chỉnh sửa">
                            <span title="Tạo đơn thuốc mới từ đơn thuốc hiện tại" class="glyphicon glyphicon-edit" style="color: blue; margin-right: 5px;"></span>
                        </a>
                        <a href="/patient/delete/<?php echo $item->id ?>" title="Xóa bệnh nhân">
                            <span title="Xóa bệnh nhân" class="glyphicon glyphicon glyphicon-remove" style="color: red"></span>
                        </a>
                    </td>
                </tr>
            <?php } ?>

        </table>

        <div class="navbar-form" role="search" style="margin-right: 0px" >
            <nav class="pull-left">
                <?php echo $models['pagination']; ?>
            </nav>
        </div>
    </div>
</form>

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