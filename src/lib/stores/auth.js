import { writable } from 'svelte/store';
import { browser } from '$app/environment';

function getInitialAuth() {
	let token = null;
	let user = null;
	let isLoggedIn = false;
	if (browser) {
		token = localStorage.getItem('token');
		isLoggedIn = !!token;
		if (isLoggedIn) {
			try {
				const payload = JSON.parse(atob(token.split('.')[1]));
				user = payload;
			} catch (e) {
				user = null;
			}
		}
	}
	return { token, user, isLoggedIn };
}

export const auth = writable(getInitialAuth());

if (browser) {
	window.addEventListener('storage', () => {
		auth.set(getInitialAuth());
	});
}

export function login(token) {
	if (browser) {
		localStorage.setItem('token', token);
		auth.set(getInitialAuth());
	}
}

export function logout() {
	if (browser) {
		localStorage.removeItem('token');
		auth.set({ token: null, user: null, isLoggedIn: false });
	}
}
export function getAuthUser() {
	return getInitialAuth();
}
