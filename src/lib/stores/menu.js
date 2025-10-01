import { writable } from 'svelte/store';

/** @type {import('./$types').PageLoad} */
export function load({ fetch }) {
	return fetch('http://localhost/mabini-cafe/phpbackend/routes/menu')
		.then((response) => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		})
		.then((data) => {
			if (!Array.isArray(data)) {
				throw new Error('Invalid data format');
			}
			return { menuItems: data };
		})
		.catch((error) => {
			console.error('Error fetching menu:', error);
			return { menuItems: [] };
		});
}

function getInitialMenu() {
	return { items: /** @type {any[]} */ ([]), loading: false, error: '' };
}

export const menu = writable(getInitialMenu());

export async function fetchMenu() {
	menu.update((m) => ({ ...m, loading: true, error: '' }));
	try {
		const response = await fetch('http://localhost/mabini-cafe/phpbackend/routes/menu');
		const data = await response.json();
		if (response.ok && Array.isArray(data)) {
			menu.set({ items: data, loading: false, error: '' });
		} else {
			menu.set({ items: [], loading: false, error: data.error || 'Failed to fetch menu.' });
		}
	} catch (err) {
		menu.set({ items: [], loading: false, error: 'Network error.' });
	}
}
