import { writable, get } from 'svelte/store';
import { browser } from '$app/environment';
import { auth } from './auth';

function getInitialUser() {
	const { user } = browser ? get(auth) : { user: null };
	return { user, loading: false, error: '' };
}

export const userStore = writable(getInitialUser());

export async function fetchUser() {
	userStore.update((u) => ({ ...u, loading: true, error: '' }));
	const { token } = browser ? get(auth) : { token: null };
	if (!token) {
		userStore.set({ user: null, loading: false, error: 'Not logged in.' });
		return;
	}
	try {
		const response = await fetch('http://localhost/mabini-cafe/phpbackend/routes/users/profile', {
			headers: { 'Authorization': `Bearer ${token}` }
		});
		const data = await response.json();
		if (response.ok && data) {
			userStore.set({ user: data, loading: false, error: '' });
		} else {
			userStore.set({ user: null, loading: false, error: data.error || 'Failed to fetch user.' });
		}
	} catch (err) {
		userStore.set({ user: null, loading: false, error: 'Network error.' });
	}
}
