import { browser } from '$app/environment';
import { redirect } from '@sveltejs/kit';
import type { PageLoad } from './$types';
import { showError } from '$lib/utils/sweetalert';

export const load: PageLoad = async ({ url }) => {
	if (browser) {
		const token = localStorage.getItem('token');
		const userStr = localStorage.getItem('user');

		if (!token || !userStr) {
			// Not logged in go to login
			await showError('Please log in first.');
			window.location.href = '/login';
			return;
		}

		try {
			const user = JSON.parse(userStr);

			// Check if user is admin
			if (user.role !== 'admin') {
				// Not an admin go to home
				throw redirect(307, '/');
			}
		} catch (error) {
			// Invalid user data go to login
			localStorage.removeItem('token');
			localStorage.removeItem('user');

			await showError('You are not allowed to this page! Logging you out now!');
			window.location.href = '/login';
			return;
		}
	}

	return {};
};
