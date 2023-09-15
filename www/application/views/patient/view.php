<?php $this->load->view('patient/info'); ?>

<h2 style="text-align: center">Đơn thuốc</h2>

<div>Ngày: <?php echo date("d/m/Y", strtotime($diagnostic->date_created)); ?>
    <span style="margin-left: 50px">Chẩn đoán: <?php echo $diagnostic->diagnostic; ?></span>
</div>
<br>
<?php $this->load->view('prescription/prescription'); ?>
<br><br>

<a href="/patient/index" class="btn btn-warning" id="back">Quay về danh sách</a>
<button type="button" class="btn btn-primary pull-right btn-print-bill-view" id="print_bill" onclick="printBill()">In hóa đơn</button>
<button type="button" class="btn btn-primary pull-right" id="print_prescription" onclick="printPrescription()">In đơn thuốc</button>

<script>
    function printPrescription() {
        window.open("/prescription/printPrescription/" + <?php echo $diagnostic->id; ?>);
    }

    function printBill() {
        window.open("/prescription/bill/" + <?php echo $diagnostic->id; ?>);
    }
</script>