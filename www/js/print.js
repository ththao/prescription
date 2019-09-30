	function Popup(data)
{
    var mywindow = window.open('', 'Hóa đơn', 'height=210mm,width=148.5mm');
    mywindow.document.write('<html><head><title>Hóa đơn</title>');
    mywindow.document.write('</head><body');
    mywindow.document.write(data);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10

    mywindow.print();
    mywindow.close();

    return true;
}