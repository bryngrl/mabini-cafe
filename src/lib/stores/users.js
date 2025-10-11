import { writable, derived } from 'svelte/store';
import { 
	signup,
	getAllUsers,
	getUserById,
	updateUser,
	deleteUser
} from '$lib/utils/fetcher';

function createUsersStore() {
	const { subscribe, set, update } = writable({
		users: [],
		selectedUser: null,
		loading: false,
		error: null
	});

	return {
		subscribe,

		signup: async (userData) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await signup(userData);
				update(state => ({ ...state, loading: false }));
				return result;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		fetchAll: async () => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const users = await getAllUsers();
				update(state => ({ ...state, users, loading: false }));
				return users;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		fetchById: async (userId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const user = await getUserById(userId);
				update(state => ({ ...state, selectedUser: user, loading: false }));
				return user;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		update: async (userId, userData) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await updateUser(userId, userData);
				update(state => ({ ...state, loading: false }));
				return result;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		delete: async (userId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await deleteUser(userId);
				const users = await getAllUsers();
				update(state => ({ ...state, users, loading: false }));
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		clearError: () => {
			update(state => ({ ...state, error: null }));
		}
	};
}

export const usersStore = createUsersStore();

export const users = derived(usersStore, $users => $users.users);
export const selectedUser = derived(usersStore, $users => $users.selectedUser);
export const usersLoading = derived(usersStore, $users => $users.loading);
