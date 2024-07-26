<style>
<!--
.drug-item-display .display {
    display: block;
}
.drug-item-display .edit {
    display: none;
}
.drug-item-edit .display {
    display: none;
}
.drug-item-edit .edit {
    display: block;
}
-->

.select2-container {
    width: 100% !important;
}
.container {
    width: 90% !important;
}
</style>

<?php $this->load->view('layout/partials/admin_menu'); ?>

<!-- Main component for a primary marketing message or call to action -->
<div class="panel panel-default">
    <!-- Default panel contents -->
    
    <div class="navbar-form" role="search">
        <nav class="pull-left" style="width: 60%;">
            <?php echo $models['pagination']; ?>
        </nav>
        <div class="pull-right" style="width: 40%;">
            <form method="post" action="<?php echo site_url('drug/import'); ?>" style="margin: 20px 0;" enctype="multipart/form-data">
                <input type="file" class="form-control" name="drugs">
            	<button type="submit" name="submit">Import</button>
            
            	<a href="#" title="Thêm thuốc" class="btn btn-success pull-right btn-add-drug"><span title="Thêm thuốc" class="glyphicon glyphicon-add"></span>Thêm Thuốc</a>
            </form>
        </div>
    </div>

    <!-- Table -->
    <table class="table table-striped table-bordered" style="margin-bottom: 0px;">
        <tr>
            <th style="width: 20%">Tên thuốc</th>
            <th style="width: 8%">Đơn vị</th>
            <th style="width: 11%">Loại</th>
            <th style="width: 8%">Giá Nhập (VNĐ)</th>
            <th style="width: 8%">Giá Bán (VNĐ)</th>
            <th style="width: 15%">Thành phần</th>
            <th style="width: 20%;">Ghi chú</th>
            <th style="width: 10%;">Sửa / Xóa</th>
        </tr>

        <tr class="drug-item-search">
            <td>
                <form method="get" action="<?php echo site_url('drug/search'); ?>" id="drug-search-form">
                    <input type="text" class="form-control drug-search" placeholder="Tìm kiếm" name="search" value="<?php echo $search; ?>">
                </form>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <?php foreach ($models['drugs'] as $item) { ?>
            <tr class="drug-item drug-item-display drug-item-<?php echo $item->id;?>">
                <td>
                	<input type="hidden" class="drug_id" value="<?php echo $item->id;?>" />
                    <label class="display" title="<?php echo $item->template_name; ?>"><?php echo $item->name; ?></label>
                    <input type="text" value="<?php echo $item->name ?>" class="form-control edit drug-name" name="name"/>
                </td>
                <td>
                    <label class="display"><?php echo $item->unit ?></label>
                    <select class="form-control edit drug-unit" name="unit">
                        <option value="Viên" <?php echo $item->unit == 'Viên' ? 'selected' : '' ?>>Viên</option>
                        <option value="Chai" <?php echo $item->unit == 'Chai' ? 'selected' : '' ?>>Chai</option>
                        <option value="Gói" <?php echo $item->unit == 'Gói' ? 'selected' : '' ?>>Gói</option>
                        <option value="Ống" <?php echo $item->unit == 'Ống' ? 'selected' : '' ?>>Ống</option>
                        <option value="Tuýp" <?php echo $item->unit == 'Tuýp' ? 'selected' : '' ?>>Tuýp</option>
                    </select>
                </td>
                <td>
                    <label class="display"><?php echo $item->category_name; ?></label>
                    <input type="text" value="<?php echo $item->category_name?>" class="form-control edit drug-category_name" name="category_name"/>
                </td>
                <td>
                    <label class="display"><?php echo number_format($item->in_price, 0, ',', '.') ?></label>
                    <input type="text" value="<?php echo $item->in_price?>" class="form-control edit drug-in_price" name="in_price"/>
                </td>
                <td>
                    <label class="display"><?php echo number_format($item->price, 0, ',', '.') ?></label>
                    <input type="text" value="<?php echo $item->price ?>" class="form-control edit drug-price" name="price"/>
                </td>
                <td>
                    <label class=""><?php echo $item->ingredients; ?></label>
                </td>
                <td>
                    <label class="display"><?php echo $item->note ?></label>
                    <input type="text" value="<?php echo $item->note ?>" class="form-control edit drug-note" name="note"/>
                </td>
                <td style="text-align: center; width: 100px">
                    <span class="glyphicon glyphicon-edit drug-item-save" drug-url="/drug/update/<?php echo $item->id ?>" title="Cập nhật thuốc" style="color: blue; cursor: pointer; "></span>&nbsp;
                    <span class="glyphicon glyphicon-search drug-item-ingredients" drug_id="<?php echo $item->id ?>" title="Thành phần thuốc" style="color: blue; cursor: pointer; "></span>&nbsp;
                    <?php if (!$item->template_name): ?>
                    <span class="glyphicon glyphicon-link" drug_id="<?php echo $item->id ?>" title="Tham khảo" style="color: blue; cursor: pointer; "></span>&nbsp;
                    <?php endif; ?>
                    <a href="/drug/delete/<?php echo $item->id ?>" title="Xóa thuốc"><span title="Xóa thuốc" class="glyphicon glyphicon-remove" style="color: red"></span></a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<div class="modal fade" id="add-drug">
    <div class="modal-dialog modal-dialog-centered asb-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thêm thuốc</h4>
                <button type="button" class="close asb-btn-icon" data-dismiss="modal" aria-label="Close" style="margin-top: -25px;">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow: scroll;">
            	<div class="form-group">
            		<input type="text" class="form-control add-drug-name" placeholder="Tên thuốc" name="name">
        		</div>
            	<div class="form-group">
            		<select class="form-control edit drug-unit" name="unit">
                        <option value="Viên" selected>Viên</option>
                        <option value="Chai">Chai</option>
                        <option value="Gói">Gói</option>
                        <option value="Ống">Ống</option>
                        <option value="Tuýp">Tuýp</option>
                    </select>
				</div>
            	<div class="form-group">
            		<input type="text" class="form-control" placeholder="Loại" name="category_name">
        		</div>
            	<div class="form-group">
            		<input type="number" class="form-control" placeholder="Giá Nhập" name="in_price">
        		</div>
            	<div class="form-group">
            		<input type="number" class="form-control" placeholder="Giá Bán" name="price">
        		</div>
            	<div class="form-group">
            		<input type="text" class="form-control ingredients" placeholder="Thành Phần" name="ingredients">
        		</div>
            	<div class="form-group">
            		<textarea class="form-control" rows="3" name="note" placeholder="Ghi Chú"></textarea>
        		</div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary pull-right btn-save-new-drug" href="#">Lưu</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="drug-ingredients">
    <div class="modal-dialog modal-dialog-centered asb-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thành phần thuốc</h4>
                <button type="button" class="close asb-btn-icon" data-dismiss="modal" aria-label="Close" style="margin-top: -25px;">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow: scroll;">
            	<table class="table-drug-ingredients" width="100%" border="1" style="text-align: center; font-size: 13px;">
            		<thead>
                    	<tr>
                            <th style="padding: 5px; width: 80%">Thành phần</th>
                            <th style="padding: 5px; width: 20%"></th>
                        </tr>
                        <tr>
                            <th style="padding: 5px;"><input type="text" class="form-control ingredient-name" placeholder="Tên thành phần" name="ingredient_name"  /></th>
                            <th style="text-align: center; padding: 5px;"><span class="glyphicon glyphicon-check add-drug-ingredient" title="Thêm thành phần" drug_id="" style="color: blue; cursor: pointer; "></span></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
				</table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="drug-link">
    <div class="modal-dialog modal-dialog-centered asb-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tham khảo</h4>
                <button type="button" class="close asb-btn-icon" data-dismiss="modal" aria-label="Close" style="margin-top: -25px;">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow: scroll;">
            	<input type="hidden" class="drug_id" value=""/>
            	<select class="form-control searchable drug-template">
            	<option value="">Chọn thuốc tham khảo</option>
            	<?php if ($drug_templates): ?>
            		<?php foreach ($drug_templates as $drug_template): ?>
            			<option value="<?php echo $drug_template['id']; ?>"><?php echo $drug_template['value']?></option>
            		<?php endforeach; ?>
            	<?php endif; ?>
            	</select>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary pull-right btn-save-drug-link" href="#" data-dismiss="modal">Lưu</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
    	
        $(document).on('keydown.autocomplete', ".drug-search", function() {
            $(this).autocomplete({
                source: <?php echo $my_drug_names; ?>
            });
        });
        
        $(document).on('keydown.autocomplete', "#add-drug .add-drug-name", function() {
            $(this).autocomplete({
                source: <?php echo $drug_names; ?>,
                appendTo: "#add-drug",
                select: function( event, ui ) {
                    $.ajax({
                        url: '/drug/get_template',
                        data: {
                            id: ui.item.id
                        },
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            if (data.status) {
                            	$('#add-drug').find('select[name="unit"]').val(data.unit);
                            	$('#add-drug').find('input[name="category_name"]').val(data.category_name);
                            	$('#add-drug').find('input[name="ingredients"]').val(data.ingredients);
                            	$('#add-drug').find('textarea[name="note"]').val(data.description);
                            }
                        }
                    });
                }
            });
        });
        
        $(document).on('keydown.autocomplete', "#drug-ingredients .ingredient-name", function() {
            $(this).autocomplete({
                source: <?php echo $ingredient_names; ?>,
                appendTo: "#drug-ingredients"
            });
        });
        
        var ingredient_names = <?php echo $ingredient_names; ?>;
        function split( val ) {
        	return val.split( /\,s*/ );
        }
        function extractLast( term ) {
        	return split( term ).pop();
        }
        $("#add-drug .ingredients").autocomplete({
            minLength: 0,
            source: function( request, response ) {
            	response($.ui.autocomplete.filter(ingredient_names, extractLast( request.term)));
            },
            appendTo: "#add-drug",
            focus: function() {
            	return false;
            },
            select: function(event, ui) {
                var terms = split( this.value );
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push(ui.item.value);
                // add placeholder to get the comma-and-space at the end
                terms.push("");
                this.value = terms.join(", ");
                return false;
            }
        });
        
    	$(document).on("click", "#add-drug .btn-save-new-drug", function(e) {
    		e.preventDefault();
    		
    		var drug_name = $('#add-drug').find('input[name="name"]').val();
    		if (!$.trim(drug_name)) {
    			alert('Vui lòng nhập tên thuốc.');
    			return false;
    		}
    		
            $.ajax({
                url: "/drug/create",
                data: {
                    name: drug_name,
                    unit: $('#add-drug').find('select[name="unit"]').val(),
                    category_name: $('#add-drug').find('input[name="category_name"]').val(),
                    in_price: $('#add-drug').find('input[name="in_price"]').val(),
                    ingredients: $('#add-drug').find('input[name="ingredients"]').val(),
                    price: $('#add-drug').find('input[name="price"]').val(),
                    note: $('#add-drug').find('textarea[name="note"]').val()
                },
                type: "POST",
                dataType: 'json',
                success:function(data) {
                    if (data.success) {
                    	$('#add-drug').find('input[name="name"]').val("");
                    	$('#add-drug').find('select[name="unit"]').val("");
                    	$('#add-drug').find('input[name="category_name"]').val("");
                    	$('#add-drug').find('input[name="in_price"]').val("");
                    	$('#add-drug').find('input[name="ingredients"]').val("");
                    	$('#add-drug').find('input[name="price"]').val("");
                    	$('#add-drug').find('textarea[name="note"]').val("");
                        window.location.href = '/drug/search?search=' + drug_name;
                    } else {
                        alert(data.error);
                    }
                }
            });
        });
        
        $(document).on("click", ".btn-add-drug", function(e) {
        	e.preventDefault();
        	
        	$('#add-drug').find('input[name="name"]').val("");
        	$('#add-drug').find('select[name="unit"]').val("");
        	$('#add-drug').find('input[name="category_name"]').val("");
        	$('#add-drug').find('input[name="in_price"]').val("");
        	$('#add-drug').find('input[name="ingredients"]').val("");
        	$('#add-drug').find('input[name="price"]').val("");
        	$('#add-drug').find('textarea[name="note"]').val("");
            $('#add-drug').modal('show');
        });
        
        $(document).on("click", ".drug-item-save", function(e) {
        	e.preventDefault();
        	
        	var selected = $(this);
        	if ($(selected).hasClass('editing')) {
                $.ajax({
                    url: $(selected).attr("drug-url"),
                    data: {
                        'name': $(selected).parents('.drug-item-edit').find('input[name="name"]').val(),
                        'unit': $(selected).parents('.drug-item-edit').find('select[name="unit"]').val(),
                        'in_price': $(selected).parents('.drug-item-edit').find('input[name="in_price"]').val(),
                        'price': $(selected).parents('.drug-item-edit').find('input[name="price"]').val(),
                        'note': $(selected).parents('.drug-item-edit').find('input[name="note"]').val()
                    },
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                        	$(selected).removeClass('editing');
                            location.reload();
                        } else {
                            alert(data.error);
                        }
                    }
                });
            } else {
                $(selected).parents(".drug-item").removeClass("drug-item-display").addClass("drug-item-edit");
                $(selected).removeClass("glyphicon-edit").addClass("glyphicon-ok");
                $(selected).addClass('editing');
            }
        });
        
        $(document).on("click", ".glyphicon-link", function(e) {
    		$('.drug-template').select2();
    		$('#drug-link').find('.drug_id').val($(this).parents('.drug-item').find('.drug_id').val());
            $('#drug-link').modal('show');
        });
        
        $(document).on("click", "#drug-link .btn-save-drug-link", function(e) {
    		e.preventDefault();
			
			var drug_id = $('#drug-link').find('.drug_id').val();
			var drug_item = $('.drug-item-' + drug_id);
			
            $.ajax({
                url:"/drug/link_with_template",
                data: {
                    drug_id: drug_id,
                    drug_template_id: $('#drug-link').find('.drug-template').val(),
                },
                type: "POST",
                dataType: 'json',
                success:function(data) {
                    if (data.status) {
                    	$('#drug-link').find('.drug-template').val("");
                    	location.reload();
                    } else {
                        alert(data.error);
                    }
                }
            });
        });
        
    	$(document).on('click', '.drug-item-ingredients', function(e) {
        	e.preventDefault();
        	
        	var selected = $(this);
        	
        	$.ajax({
                url:"/drug/ingredients",
                data: {
                	drug_id: $(selected).attr('drug_id')
                },
                type: "POST",
                dataType: 'json',
                success:function(data) {
                    if (data.status) {
                    	$('#drug-ingredients').find('.add-drug-ingredient').attr('drug_id', data.drug_id);
                    	$('#drug-ingredients').find('.modal-body .table-drug-ingredients tbody').html(data.html);
                        $('#drug-ingredients').modal('show');
                    }
                }
            });
        });
        
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
                    		$('#drug-ingredients').find(".ingredient-name").val("");
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

        $(document).on("keypress", ".drug-item-edit .form-control", function(e) {
        	if (e.which == 13) {
                $(this).parents(".drug-item-edit").find(".drug-item-save").trigger("click");
            }
        });

        $('.glyphicon-remove').click(function () {
            if (confirm("Bạn có muốn xóa loại thuốc này?")){
                return true;
            } else {
                return false;
            }
        });

        $(".drug-search").blur(function() {
        	$('#drug-search-form').submit();
        });

        $(".drug-search").keypress(function(e) {
            if (e.which == 13) {
            	$('#drug-search-form').submit();
            }
        });
    });
</script>