<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug - Rutas de Hackathon</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        .highlight {
            background-color: #ffffcc;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Rutas relacionadas con Hackathons</h1>
    
    <p><strong>Ruta problemática:</strong> <code>student.hackathons.show</code> - Buscando en la tabla</p>
    
    <table>
        <thead>
            <tr>
                <th>Método</th>
                <th>URI</th>
                <th>Nombre</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($routes as $route)
            <tr class="{{ $route['name'] == 'student.hackathons.show' ? 'highlight' : '' }}">
                <td>{{ $route['method'] }}</td>
                <td>{{ $route['uri'] }}</td>
                <td>{{ $route['name'] ?? 'Sin nombre' }}</td>
                <td>{{ $route['action'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <h2>Solución para URL específica</h2>
    <p>
        La URL <code>http://localhost:8001/estudiantes/hackathons/team/4#deliverables</code> debería enlazar a:
    </p>
    <ul>
        <li>Ruta <code>student.hackathons.team</code> para ver el equipo</li>
        <li>O ruta <code>student.hackathons.team.deliverables</code> para ver los entregables</li>
    </ul>
    
    <p>
        <strong>Verificar que la ruta funciona:</strong><br>
        @php
            try {
                echo "URL para student.hackathons.team: " . route('student.hackathons.team', ['id' => 4]);
            } catch (\Exception $e) {
                echo "Error generando ruta 'student.hackathons.team': " . $e->getMessage();
            }
            
            echo "<br>";
            
            try {
                echo "URL para student.hackathons.show: " . route('student.hackathons.show', ['id' => 4]);
            } catch (\Exception $e) {
                echo "Error generando ruta 'student.hackathons.show': " . $e->getMessage();
            }
            
            echo "<br>";
            
            try {
                echo "URL para student.hackathons.team.deliverables: " . route('student.hackathons.team.deliverables', ['id' => 4]);
            } catch (\Exception $e) {
                echo "Error generando ruta 'student.hackathons.team.deliverables': " . $e->getMessage();
            }
        @endphp
    </p>
</body>
</html> 