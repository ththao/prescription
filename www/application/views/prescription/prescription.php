<table width="100%" border="1" style="text-align: center; font-size: 13px;">
    <tr>
        <th width="3%"></th>
        <th width="40%" style="text-align: center;">Tên thuốc</th>
        <th width="17%" style="text-align: center;">Số lượng (viên/gói)</th>
        <th width="20%" style="text-align: center;">Số lần uống/ngày</th>
        <th width="20%" style="text-align: center;">Số viên (gói)/lần</th>
    </tr>
    <?php
    $index = 1;
    foreach ($prescription as $item) {
        $drug = $this->drug_model->findOne(array('id' => $item->drug_id));
        if (isset($drug->name) && !empty($drug->name)) { ?>
        <tr>
            <td width="3%"><?php echo $index; ?></td>
            <td width="40%" align="left"><?php echo $drug->name; ?></td>
            <td width="17%"><?php echo $item->quantity; ?></td>
            <td width="20%"><?php echo $item->time_in_day; ?></td>
            <td width="20%"><?php echo $item->unit_in_time; ?></td>
        </tr>
    <?php }
        $index++;
    } ?>
</table>
