import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';
import os from 'os';

// Función para obtener la IP local
function getLocalIP() {
	const interfaces = os.networkInterfaces();
	for (const name of Object.keys(interfaces)) {
		for (const iface of interfaces[name]) {
			// Saltamos las interfaces que no son IPv4 o son localhost
			if (iface.family === 'IPv4' && !iface.internal) {
				return iface.address;
			}
		}
	}
	return 'localhost'; // Fallback a localhost si no encontramos IP
}

// Usar BACKEND_URL de las variables de entorno o la IP local
const backendUrl = process.env.BACKEND_URL || `http://${getLocalIP()}:8000`;

export default defineConfig({
	plugins: [sveltekit()],
	server: {
		proxy: {
			'/api': {
				target: backendUrl,
				changeOrigin: true
			}
		}
	}
});
