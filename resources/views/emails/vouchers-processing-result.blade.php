<!DOCTYPE html>
<html>
<head>
    <title>Comprobantes Subidos</title>
</head>
<body>
    <h1>Estimado {{ $user->name }},</h1>
    <p>Hemos recibido tus comprobantes con los siguientes detalles:</p>
    @if (!empty($vouchersFailed))
        <p>Comprobantes con errores:</p>
        @foreach ($vouchersFailed as $voucher)
        <ul>
            <li>Nombre del Archivo: {{ $voucher["filename"] }}</li>
            <li>Mensaje de Error: {{ $voucher["reason"] }}</li>
        </ul>
        @endforeach
    @endif
    @if (!empty($vouchersSuccess))
        <p>Comprobantes procesados con éxito:</p>
        @foreach ($vouchersSuccess as $voucher)
        <ul>
            <li>Numero de Comprobante: {{ $voucher->series }}-{{ $voucher->number }}</li>
            <li>Tipo de Comprobante: {{ $voucher->voucher_type }}</li>
            <li>Nombre del Emisor: {{ $voucher->issuer_name }}</li>
            <li>Tipo de Documento del Emisor: {{ $voucher->issuer_document_type }}</li>
            <li>Número de Documento del Emisor: {{ $voucher->issuer_document_number }}</li>
            <li>Nombre del Receptor: {{ $voucher->receiver_name }}</li>
            <li>Tipo de Documento del Receptor: {{ $voucher->receiver_document_type }}</li>
            <li>Número de Documento del Receptor: {{ $voucher->receiver_document_number }}</li>
            <li>Monto Total: {{ $voucher->total_amount }}</li>
            <li>Moneda: {{ $voucher->currency }}</li>
        </ul>
        @endforeach
    @endif
    <p>¡Gracias por usar nuestro servicio!</p>
</body>
</html>
