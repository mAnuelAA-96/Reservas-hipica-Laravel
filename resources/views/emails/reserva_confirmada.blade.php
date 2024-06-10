<!DOCTYPE html>
<html>
<head>
    <title>Confirmación de Reserva "Caballos para disfrutar"</title>
</head>
<body>
    <h1>Gracias por tu reserva</h1>
    <p>Tu reserva ha sido confirmada.</p>
    <p>Detalles de la reserva:</p>
    <ul>
        <li>Nombre: {{ $detallesReserva['nombre'] }}</li>
        <li>Caballo: {{ $detallesReserva['caballo'] }}</li>
        <li>Fecha: {{ $detallesReserva['fecha'] }}</li>
        <li>Hora: {{ $detallesReserva['hora'] }}</li>
    </ul>
    <p>Te esperamos!</p>
</body>
</html>