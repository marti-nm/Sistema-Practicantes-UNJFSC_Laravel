<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido al Sistema de Prácticas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .credentials {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bienvenido al Sistema de Prácticas Pre Profesionales</h2>
        <p>Hola <strong>{{ $data['nombre'] }}</strong>,</p>
        
        <p>Su cuenta ha sido creada exitosamente y ha sido asignado al semestre académico <strong>{{ $data['semestre'] }}</strong>.</p>
        
        <p>A continuación se detallan sus credenciales de acceso:</p>
        
        <div class="credentials">
            <p><strong>Usuario:</strong> {{ $data['usuario'] }} <br> (o su correo institucional)</p>
            <p><strong>Contraseña:</strong> {{ $data['password'] }}</p>
        </div>

        <p>Por favor, ingrese al sistema y verifique sus datos.</p>

        @if(isset($data['mensaje_extra']))
            <p><em>{{ $data['mensaje_extra'] }}</em></p>
        @endif

        <div class="footer">
            <p>Si usted no lleva el curso o tiene problemas para acceder, por favor contacte al soporte técnico al correo: <strong>soporte@unjfsc.edu.pe</strong></p>
            <p>Saludos cordiales,<br>Equipo de Prácticas Pre Profesionales - UNJFSC</p>
        </div>
    </div>
</body>
</html>
