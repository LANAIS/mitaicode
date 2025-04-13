<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de MitaiCode</title>
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
        
        .header img {
            max-width: 150px;
            height: auto;
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
        
        .info-box {
            background-color: #f3f6ff;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            border-left: 4px solid #4e73df;
        }
        
        .welcome {
            background-color: #e8f5e9;
            border-left-color: #1cc88a;
        }
        
        .reminder {
            background-color: #fff8e1;
            border-left-color: #f6c23e;
        }
        
        .inactive {
            background-color: #fce4ec;
            border-left-color: #e74a3b;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 5px;
            color: #4e73df;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="MitaiCode Logo">
        </div>
        
        <div class="content">
            @if($notificationType == 'welcome')
                <div class="info-box welcome">
                    <h3>¡Bienvenido a MitaiCode!</h3>
                </div>
            @elseif($notificationType == 'reminder')
                <div class="info-box reminder">
                    <h3>Recordatorio importante</h3>
                </div>
            @elseif($notificationType == 'inactive')
                <div class="info-box inactive">
                    <h3>¡Te extrañamos!</h3>
                </div>
            @endif
            
            {!! $content !!}
            
            @if(isset($customVariables['cta_url']) && isset($customVariables['cta_text']))
                <div style="text-align: center;">
                    <a href="{{ $customVariables['cta_url'] }}" class="button">{{ $customVariables['cta_text'] }}</a>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <div class="social-links">
                <a href="https://facebook.com/mitaicode">Facebook</a> | 
                <a href="https://twitter.com/mitaicode">Twitter</a> | 
                <a href="https://instagram.com/mitaicode">Instagram</a>
            </div>
            <p>&copy; {{ date('Y') }} MitaiCode. Todos los derechos reservados.</p>
            <p>
                <small>Si no deseas recibir más correos electrónicos como este, puedes <a href="{{ route('profile.notifications') }}">actualizar tus preferencias de notificación</a>.</small>
            </p>
        </div>
    </div>
</body>
</html> 