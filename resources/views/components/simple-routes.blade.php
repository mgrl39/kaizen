<div style="font-family: sans-serif; margin: 20px;">
    <h2 style="color: #333;">Todas las Rutas</h2>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Método</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">URL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($routes as $route)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        @if(strpos($route['method'], 'GET') !== false)
                            <span style="background-color: #d4edda; color: #155724; padding: 3px 6px; border-radius: 3px;">{{ $route['method'] }}</span>
                        @elseif(strpos($route['method'], 'POST') !== false)
                            <span style="background-color: #cce5ff; color: #004085; padding: 3px 6px; border-radius: 3px;">{{ $route['method'] }}</span>
                        @elseif(strpos($route['method'], 'PUT') !== false || strpos($route['method'], 'PATCH') !== false)
                            <span style="background-color: #fff3cd; color: #856404; padding: 3px 6px; border-radius: 3px;">{{ $route['method'] }}</span>
                        @elseif(strpos($route['method'], 'DELETE') !== false)
                            <span style="background-color: #f8d7da; color: #721c24; padding: 3px 6px; border-radius: 3px;">{{ $route['method'] }}</span>
                        @else
                            <span style="background-color: #e2e3e5; color: #383d41; padding: 3px 6px; border-radius: 3px;">{{ $route['method'] }}</span>
                        @endif
                    </td>
                    <td style="padding: 10px; font-family: monospace; border: 1px solid #ddd;">{{ $route['url'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> 