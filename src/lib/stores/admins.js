import { writable, derived } from 'svelte/store';
import { 
	getAllAdmins,
	getAdminById,
	createAdmin,
	updateAdmin,
	deleteAdmin,
	logoutAdmin
} from '$lib/utils/fetcher';

// Admins store
function createAdminsStore() {
	const { subscribe, set, update } = writable({
		admins: [],
		selectedAdmin: null,
		loading: false,
		error: null
	});

	return {
		subscribe,

		fetchAll: async () => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const admins = await getAllAdmins();
				update(state => ({ ...state, admins, loading: false }));
				return admins;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		fetchById: async (adminId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const admin = await getAdminById(adminId);
				update(state => ({ ...state, selectedAdmin: admin, loading: false }));
				return admin;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

	
		create: async (adminData) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await createAdmin(adminData);
				const admins = await getAllAdmins();
				update(state => ({ ...state, admins, loading: false }));
				return result;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		update: async (adminId, adminData) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await updateAdmin(adminId, adminData);
				const admins = await getAllAdmins();
				update(state => ({ ...state, admins, loading: false }));
				return result;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		delete: async (adminId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await deleteAdmin(adminId);
				const admins = await getAllAdmins();
				update(state => ({ ...state, admins, loading: false }));
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		logout: async () => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await logoutAdmin();
				update(state => ({ ...state, loading: false }));
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

export const adminsStore = createAdminsStore();

export const admins = derived(adminsStore, $admins => $admins.admins);
export const selectedAdmin = derived(adminsStore, $admins => $admins.selectedAdmin);
export const adminsLoading = derived(adminsStore, $admins => $admins.loading);
