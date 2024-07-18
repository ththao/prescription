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

<script>
    $(document).ready(function() {
    	$(document).on('click', '#drug-ingredients .add-drug-ingredient', function(e) {
    		e.preventDefault();
    		var selected = $(this);
    		
            if ($('#drug-ingredients').find(".ingredient-name").val() != '') {
                $.ajax({
                    url: '/drug/add_ingredient',
                    data: {
                        drug_id: $(selected).attr('drug_id'),
						ingredient_name: $("#drug-ingredients .ingredient-name").val()
                    },
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if (data.status) {
                    		$('#drug-ingredients').find('.modal-body .table-drug-ingredients tbody').append(data.html);
                        } else {
                            alert(data.error);
                        }
                    }
                });
            }
        });
        
    	$(document).on('click', '#drug-ingredients .remove-drug-ingredient', function(e) {
    		e.preventDefault();
    		var selected = $(this);
    		
            $.ajax({
                url: '/drug/remove_ingredient',
                data: {
                    drug_id: $(selected).attr('drug_id'),
					ingredient_id: $(selected).attr('ingredient_id')
                },
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    if (data.status) {
                		$(selected).parents('tr').remove();
                    } else {
                        alert(data.error);
                    }
                }
            });
        });
    });
</script>