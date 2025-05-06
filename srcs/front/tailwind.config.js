/** @type {import('tailwindcss').Config} */
export default {
	content: ['./src/**/*.{html,js,svelte,ts}', './src/**/*.svelte'],
	theme: {
		extend: {
			colors: {
				dark: '#121212',
				card: '#212529',
				primary: '#6d28d9',
				'primary-hover': '#5b21b6',
				light: '#f8f9fa'
			}
		}
	},
	plugins: []
};
