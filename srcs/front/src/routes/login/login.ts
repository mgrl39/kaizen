import { API_URL } from '$lib/config';

type LoginParams = {
	identifier: string;
	password: string;
	setLoading: (v: boolean) => void;
	setShowError: (v: boolean) => void;
	setErrorMessage: (v: string) => void;
};

export async function handleSubmit({
	identifier,
	password,
	setLoading,
	setShowError,
	setErrorMessage
}: LoginParams): Promise<void> {
	setLoading(true);
	setShowError(false);

	try {
		const response = await fetch(`${API_URL}/login`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({ identifier, password })
		});

		const data = await response.json();

		if (response.ok && data.success) {
			localStorage.setItem('token', data.token);
			window.location.href = '/';
		} else {
			setErrorMessage(data.message || 'Credenciales incorrectas');
			setShowError(true);
		}
	} catch (error) {
		setErrorMessage('Error de conexión con el servidor');
		setShowError(true);
	} finally {
		setLoading(false);
	}
}
