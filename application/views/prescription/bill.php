<!-- Main component for a primary marketing message or call to action -->
<button type="submit" class="btn btn-warning pull-right btn-print"><span class="glyphicon glyphicon-print" style="margin-right: 10px"></span>IN</button>

<div class="bill" style="position: relative; height:148.5mm; width:210mm;">
    <h2 style="text-align: center">HÓA ĐƠN</h2>

    <?php $this->load->view('patient/info'); ?>

    <!-- Table -->
    <table width="100%" border="1" style="text-align: center">
        <tr>
            <th width="40px">STT</th>
            <th width="240px">Tên thuốc</th>
            <th style="text-align: center">Số lượng</th>
            <th style="text-align: center">Đơn giá (VNĐ)</th>
            <th style="text-align: center">Thành tiền (VNĐ)</th>
        </tr>
        <?php 
        $total = 0;
        $index = 1;
        ?>
        
        <?php foreach ($orders as $item) { ?>
                <tr>
                    <td width="40px"><?php echo $index; ?></td>
                    <td style="text-align: left"><?php echo $item->service_name; ?></td>
                    <td><?php echo $item->quantity; ?></td>
                    <td><?php echo number_format($item->price, 0, ',', '.'); ?></td>
                    <td><?php echo number_format($item->quantity * $item->price, 0, ',', '.'); ?></td>
                </tr>
            <?php 
            $total += $item->quantity * $item->price;
            $index++;
            ?>
        <?php } ?>
        <?php foreach ($prescription as $item) { ?>
                <tr>
                    <td width="40px"><?php echo $index; ?></td>
                    <td style="text-align: left"><?php echo $item->drug_name; ?></td>
                    <td><?php echo $item->quantity . ' ' . $item->unit; ?></td>
                    <td><?php echo number_format($item->unit_price, 0, ',', '.'); ?></td>
                    <td><?php echo number_format($item->quantity * $item->unit_price, 0, ',', '.'); ?></td>
                </tr>
            <?php 
            $total += $item->quantity * $item->unit_price;
            $index++;
            ?>
        <?php } ?>
    </table>

    <table width="100%" border="0">
        <tr>
            <td colspan="2" height="20px"></td>
        </tr>
        <tr>
            <td width="78%" style="text-align: right"><b>Tổng cộng</b></td>
            <td style="text-align: center"><b><?php echo number_format($total, 0, ',', '.'); ?> (VNĐ)<b></td>
        </tr>
    </table>

    <table width="100%" border="0" style="text-align: center">
        <tr>
            <td colspan="2" height="50px"></td>
        </tr>
        <tr>
            <td width="30%">Bệnh nhân</td>
            <td width="30%"></td>
            <td width="40%">Người xuất hóa đơn</td>
        </tr>
    </table>
</div>

<script type="text/javascript" src="../../../js/print.js"></script>
<script>
    $('.btn-print').click(function () {
        Popup($('.bill').html());
        window.close();
    })
</script>