import { browser } from '$app/environment';
import { redirect } from '@sveltejs/kit';
import type { PageLoad } from './$types';

export const load: PageLoad = async ({ url }) => {
	if (browser) {
		const token = localStorage.getItem('token');
		const userStr = localStorage.getItem('user');
		
		if (!token || !userStr) {
			// Not logged in, redirect to login
			throw redirect(307, '/login');
		}
		
		try {
			const user = JSON.parse(userStr);
			
			// Check if user is admin
			if (user.role !== 'admin') {
				// Not an admin, redirect to home
				throw redirect(307, '/');
			}
		} catch (error) {
			// Invalid user data, redirect to login
			localStorage.removeItem('token');
			localStorage.removeItem('user');
			throw redirect(307, '/login');
		}
	}
	
	return {};
};
