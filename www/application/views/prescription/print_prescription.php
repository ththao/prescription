<!-- Main component for a primary marketing message or call to action -->
<button type="submit" class="btn btn-warning pull-right btn-print"><span class="glyphicon glyphicon-print" style="margin-right: 10px"></span>IN</button>

<div class="prescription" style="position: relative; height:210mm; width:148.5mm;">
    <div style="font-size: 13px;">Phòng khám <?php echo PK; ?></div>
    <div style="font-size: 13px;">BS <?php echo DOCTOR_NAME; ?> - <?php echo DOCTOR_MOBILE; ?></div>
    <div style="font-size: 13px;">BS <?php echo DOCTOR_ADDR; ?></div>
    
    <h2 style="text-align: center">ĐƠN THUỐC</h2>
    <?php $this->load->view('patient/info'); ?>
    
    <?php $this->load->view('prescription/prescription'); ?>
    
    <table width="100%" border="0" style="position: absolute; bottom: 10px;">
        <tr>
            <td colspan="2" height="30px"></td>
        </tr>
        <tr>
            <td width="60%" ></td>
            <td style="text-align: center; font-size: 12px;">Ngày <?php echo date('d'); ?> tháng <?php echo date('m'); ?> năm <?php echo date('Y'); ?></td>
        </tr>
        <tr>
            <td colspan="2" height="30px"></td>
        </tr>
        <tr>
            <td style="font-size: 11px;">Đơn thuốc chỉ có giá trị cho mỗi lần khám</td>
            <td style="text-align: center;  font-size: 12px;"><b>Bác sĩ <?php echo DOCTOR_NAME; ?></b></td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 11px;">Tái khám ngày:</td>
        </tr>
        <tr>
        	<td colspan="2" style="font-size: 11px;">(Khi tái khám nhớ mang theo đơn thuốc)</td>
            
        </tr>
    </table>
</div>

<script type="text/javascript" src="../../../js/print.js"></script>
<script>
    $('.btn-print').click(function () {
        Popup($('.prescription').html());
        window.close();
    })
</script>