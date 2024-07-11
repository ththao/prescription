<?php if ($drugs) { ?>
<table width="100%" border="1" style="text-align: center; font-size: 13px;">
	<tr>
        <th width="10%" style="text-align: center;"><input type="checkbox" class="select-all" /></th>
        <th>Tên thuốc</th>
        <th width="50px">Đơn vị</th>
    </tr>
    <?php
    foreach ($drugs as $item) {
    ?>
        <tr>
            <td width="10%"><input type="checkbox" class="suggested-drug-id" value="<?php echo $item->id; ?>" drug_name="<?php echo $item->name; ?>" drug_unit="<?php echo $item->unit; ?>" <?php echo $item->most_used ? 'checked' : ''; ?> /></td>
            <td align="left" style="padding: 5px;"><?php echo $item->name; ?></td>
            <td align="left" style="padding: 5px;"><?php echo $item->unit; ?></td>
        </tr>
    <?php } ?>
</table>
<?php } ?>