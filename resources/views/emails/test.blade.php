<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correo de Prueba - MitaiCode</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        
        .content {
            padding: 20px 0;
        }
        
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #eee;
            font-size: 0.8em;
            color: #777;
        }
        
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4e73df;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MitaiCode</h1>
        </div>
        
        <div class="content">
            <h2>Prueba de Correo Electrónico</h2>
            
            <p>Este es un correo de prueba para verificar que el sistema de envío de correos funciona correctamente.</p>
            
            <p>Si estás recibiendo este correo, significa que la configuración de correo electrónico en MitaiCode está correctamente configurada.</p>
            
            <div style="text-align: center;">
                <a href="https://www.mitaicode.com" class="button">Visitar MitaiCode</a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} MitaiCode. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html> 