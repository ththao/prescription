<table width="100%" border="1" style="text-align: center; font-size: 13px;">
    <tr>
        <th width="3%"></th>
        <th width="22%" style="text-align: center;">Tên thuốc</th>
        <th width="16%" style="text-align: center;">Số lượng</th>
        <th width="10%" style="text-align: center;">Lần/ngày</th>
        <th width="15%" style="text-align: center;">SL mỗi lần</th>
        <th style="text-align: center;">Ghi chú</th>
    </tr>
    <?php foreach ($prescription as $ind => $item) { ?>
        <tr>
            <td><?php echo $ind + 1; ?></td>
            <td align="left" style="padding: 3px;"><?php echo $item->drug_name; ?></td>
            <td><?php echo $item->quantity . ' ' . $item->unit; ?></td>
            <td><?php echo $item->time_in_day; ?></td>
            <td><?php echo $item->unit_in_time; ?></td>
            <td><?php echo $item->notes; ?></td>
        </tr>
    <?php } ?>
</table>
