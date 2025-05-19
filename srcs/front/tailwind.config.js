/** @type {import('tailwindcss').Config} */
export default {
	content: ['./src/**/*.{html,js,svelte,ts}'],
	theme: {
		extend: {
			colors: {
				dark: '#1a1a1a',
				card: '#212529',
				primary: '#6d28d9',
				'primary-hover': '#5b21b6',
				light: '#f8f9fa'
			}
		}
	},
	plugins: []
};
