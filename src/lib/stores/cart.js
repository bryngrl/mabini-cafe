import { writable, get } from 'svelte/store';
import { browser } from '$app/environment';
import { auth } from './auth';

function getInitialCart() {
	return { items: [], loading: false, error: '' };
}

export const cart = writable(getInitialCart());

export async function fetchCart() {
	cart.update((c) => ({ ...c, loading: true, error: '' }));
	const { token, user } = browser ? get(auth) : { token: null, user: null };
	let url = 'http://localhost/mabini-cafe/phpbackend/routes/cart';
	if (user && user.id) {
		url += `?customer_id=${user.id}`;
	}
	try {
		const response = await fetch(url, {
			headers: token ? { 'Authorization': `Bearer ${token}` } : {}
		});
		const data = await response.json();
		if (response.ok && Array.isArray(data)) {
			const mapped = data.map(item => {
				let image = item.menu_item_image || '';
				if (image) {
					image = image.replace(/^\/?/, '');
					image = `http://localhost/mabini-cafe/phpbackend/${image}`;
				}
				return {
					id: item.id,
					name: item.menu_item_name || item.name || '',
					price: Number(item.menu_item_price ?? item.price ?? 0),
					quantity: Number(item.quantity ?? 1),
					subtotal: Number(item.subtotal ?? 0),
					image,
					description: item.menu_item_description || '',
					category: item.category_name || ''
				};
			});
			cart.set({ items: mapped, loading: false, error: '' });
		} else {
			cart.set({ items: [], loading: false, error: data.error || 'Failed to fetch cart.' });
		}
	} catch (err) {
		cart.set({ items: [], loading: false, error: 'Network error.' });
	}
}

export async function addToCart(item) {
	cart.update((c) => ({ ...c, loading: true, error: '' }));
	const { token, user } = browser ? get(auth) : { token: null, user: null };
	if (!token || !user) {
		cart.update((c) => ({ ...c, loading: false, error: 'You must be logged in.' }));
		return;
	}
	try {
		const response = await fetch('http://localhost/mabini-cafe/phpbackend/routes/cart', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Authorization': `Bearer ${token}`
			},
			body: JSON.stringify({
				user_id: user.id,
				menu_item_id: item.id,
				quantity: item.quantity || 1,
				subtotal: item.price * (item.quantity || 1)
			})
		});
		const data = await response.json();
		if (response.ok) {
			await fetchCart();
		} else {
			cart.update((c) => ({ ...c, loading: false, error: data.error || 'Failed to add to cart.' }));
		}
	} catch (err) {
		cart.update((c) => ({ ...c, loading: false, error: 'Network error.' }));
	}
}

export function clearCart() {
	cart.set(getInitialCart());
}
