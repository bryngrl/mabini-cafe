import { writable, derived } from 'svelte/store';
import { browser } from '$app/environment';
import { loginUser, loginAdmin } from '$lib/utils/fetcher';


function createAuthStore() {
	const { subscribe, set, update } = writable({
		user: null,
		isAuthenticated: false,
		isAdmin: false,
		loading: false,
		error: null
	});

	return {
		subscribe,
		
	
		init: () => {
			if (!browser) return;
			
			
			const savedUser = localStorage.getItem('user');
			const savedToken = localStorage.getItem('token');
			
			if (savedUser && savedToken) {
				try {
					const user = JSON.parse(savedUser);
					update(state => ({
						...state,
						user: user,
						isAuthenticated: true,
						isAdmin: user.role === 'admin',
						loading: false,
						error: null
					}));
				} catch (error) {
					
					localStorage.removeItem('user');
					localStorage.removeItem('token');
				}
			}
		},
		
	
		loginUser: async (email, password) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await loginUser(email, password);
				if (result.token && result.info) {
					
					localStorage.setItem('token', result.token);
					localStorage.setItem('user', JSON.stringify(result.info));
					
					update(state => ({
						...state,
						user: result.info,
						isAuthenticated: true,
						isAdmin: result.info.role === 'admin',
						loading: false,
						error: null
					}));
					return result;
				}
				throw new Error('Invalid response from server');
			} catch (error) {
				update(state => ({ 
					...state, 
					loading: false, 
					error: error.message 
				}));
				throw error;
			}
		},

		
		loginAdmin: async (email, password) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await loginAdmin(email, password);
				if (result.token && result.info) {
					localStorage.setItem('token', result.token);
					localStorage.setItem('user', JSON.stringify(result.info));
					
					update(state => ({
						...state,
						user: result.info,
						isAuthenticated: true,
						isAdmin: true,
						loading: false,
						error: null
					}));
					return result;
				}
				throw new Error('Invalid response from server');
			} catch (error) {
				update(state => ({ 
					...state, 
					loading: false, 
					error: error.message 
				}));
				throw error;
			}
		},

		logout: () => {
			localStorage.removeItem('user');
			localStorage.removeItem('token');
			
			set({
				user: null,
				isAuthenticated: false,
				isAdmin: false,
				loading: false,
				error: null
			});
		},

		clearError: () => {
			update(state => ({ ...state, error: null }));
		}
	};
}

export const authStore = createAuthStore();

export const isAuthenticated = derived(authStore, $auth => $auth.isAuthenticated);
export const isAdmin = derived(authStore, $auth => $auth.isAdmin);
export const currentUser = derived(authStore, $auth => $auth.user);
