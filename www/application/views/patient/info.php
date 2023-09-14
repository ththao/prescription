<!-- Table -->
<table width="100%" border="0" style="font-size: 13px;">
    <tr>
        <td width="50%">Họ tên: <?php echo isset($patient->name) ? $patient->name : ''; ?></td>
        <td>Năm sinh: <?php echo isset($patient->dob) ? $patient->dob : ''; ?></td>
        <td>Giới tính: <?php echo isset($patient->gender) ? $patient->gender : ''; ?></td>
    </tr>
    <tr>
        <td colspan="2">Địa chỉ: <?php echo isset($patient->address) ? $patient->address : ''; ?></td>
        <td>Điện thoại: <?php echo isset($patient->phone) ? $patient->phone : ''; ?></td>
    </tr>
    <tr>
        <td colspan="3">Chẩn đoán: <?php echo isset($diagnostic->diagnostic) ? $diagnostic->diagnostic : ''; ?></td>
    </tr>
    <tr>
        <td colspan="3">Ghi chú: <?php echo isset($diagnostic->note) ? $diagnostic->note : ''; ?></td>
    </tr>
    <tr style="height: 30px">
        <td colspan="3"></td>
    </tr>
</table>
