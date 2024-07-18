<?php if ($ingredients) { ?>
    <?php foreach ($ingredients as $item) { ?>
        <tr>
            <td align="left" style="padding: 5px;"><?php echo $item->ingredient_name; ?></td>
        	<td align="center" style="padding: 5px;">
        	<span class="glyphicon glyphicon-remove remove-drug-ingredient" title="Xóa thành phần" drug_id="<?php echo $item->drug_id; ?>" ingredient_id="<?php echo $item->ingredient_id; ?>" style="color: red; cursor: pointer; "></span>
        	</td>
        </tr>
    <?php } ?>
<?php } ?>