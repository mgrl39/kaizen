<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Películas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: sans-serif; padding: 2rem; background: #f9f9f9; }
        .movie { background: white; padding: 1rem; margin-bottom: 1rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .movie img { max-width: 100%; height: auto; }
        .empty { color: gray; font-style: italic; }
    </style>
</head>
<body>
    <h1>🎬 Lista de Películas</h1>
    <div id="movies"></div>

    <script>
        fetch('/api/movies')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('movies');

                if (!data.success || data.count === 0) {
                    container.innerHTML = `<p class="empty">No hay películas registradas.</p>`;
                    return;
                }

                data.data.forEach(movie => {
                    const div = document.createElement('div');
                    div.classList.add('movie');
                    div.innerHTML = `
                        <h2>${movie.title}</h2>
                        <p><strong>Duración:</strong> ${movie.duration} min</p>
                        <p><strong>Fecha de estreno:</strong> ${movie.release_date}</p>
                        <p>${movie.synopsis}</p>
                        <img src="${movie.photo_url}" alt="${movie.title}">
                    `;
                    container.appendChild(div);
                });
            })
            .catch(error => {
                document.getElementById('movies').innerHTML = `<p class="empty">Error cargando películas.</p>`;
                console.error('Error al obtener películas:', error);
            });
    </script>
</body>
</html>

