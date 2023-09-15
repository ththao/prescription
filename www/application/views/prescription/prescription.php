<table width="100%" border="1" style="text-align: center; font-size: 13px;">
    <tr>
        <th width="3%"></th>
        <th width="22%" style="text-align: center;">Tên thuốc</th>
        <th width="16%" style="text-align: center;">Số lượng (viên/gói)</th>
        <th width="10%" style="text-align: center;">Lần/ngày</th>
        <th width="15%" style="text-align: center;">Viên/gói mỗi lần</th>
        <th style="text-align: center;">Ghi chú</th>
    </tr>
    <?php
    $index = 1;
    foreach ($prescription as $item) {
        $drug = $this->drug_model->findOne(array('id' => $item->drug_id));
        if (isset($drug->name) && !empty($drug->name)) { ?>
        <tr>
            <td><?php echo $index; ?></td>
            <td align="left" style="padding: 3px;"><?php echo $drug->name; ?></td>
            <td><?php echo $item->quantity; ?></td>
            <td><?php echo $item->time_in_day; ?></td>
            <td><?php echo $item->unit_in_time; ?></td>
            <td><?php echo $item->notes; ?></td>
        </tr>
    <?php }
        $index++;
    } ?>
</table>
