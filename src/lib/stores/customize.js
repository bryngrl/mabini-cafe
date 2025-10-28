import { writable, derived } from 'svelte/store';
import {
	getAllCustomizations,
	getCustomizationById,
	createCustomization,
	updateCustomizationName,
	updateCustomizationImage,
	deleteCustomization
} from '$lib/utils/fetcher';

function createCustomizeStore() {
	const { subscribe, set, update } = writable({
		customizations: [],
		selectedCustomization: null,
		loading: false,
		error: null
	});

	const store = {
		subscribe,

		fetchAll: async () => {
			update((state) => ({ ...state, loading: true, error: null }));
			try {
				const customizations = await getAllCustomizations();
				update((state) => ({ ...state, customizations, loading: false }));
				return customizations;
			} catch (error) {
				update((state) => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		fetchById: async (customId) => {
			update((state) => ({ ...state, loading: true, error: null }));
			try {
				const customization = await getCustomizationById(customId);
				update((state) => ({ ...state, selectedCustomization: customization, loading: false }));
				return customization;
			} catch (error) {
				update((state) => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		create: async (customData, imageFile = null) => {
			update((state) => ({ ...state, loading: true, error: null }));
			try {
				const result = await createCustomization(customData, imageFile);
				await store.fetchAll();
				return result;
			} catch (error) {
				update((state) => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		updateName: async (customId, customData) => {
			update((state) => ({ ...state, loading: true, error: null }));
			try {
				const result = await updateCustomizationName(customId, customData);
				await store.fetchAll();
				return result;
			} catch (error) {
				update((state) => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		updateImage: async (customId, imageFile) => {
			update((state) => ({ ...state, loading: true, error: null }));
			try {
				const result = await updateCustomizationImage(customId, imageFile);
				await store.fetchAll();
				return result;
			} catch (error) {
				update((state) => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		delete: async (customId) => {
			update((state) => ({ ...state, loading: true, error: null }));
			try {
				const result = await deleteCustomization(customId);
				update((state) => ({
					...state,
					customizations: state.customizations.filter((c) => c.id !== customId),
					loading: false
				}));
				return result;
			} catch (error) {
				update((state) => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		reset: () => {
			set({
				customizations: [],
				selectedCustomization: null,
				loading: false,
				error: null
			});
		}
	};

	return store;
}

export const customizeStore = createCustomizeStore();
export const customizations = derived(customizeStore, ($store) => $store.customizations);
export const customizeLoading = derived(customizeStore, ($store) => $store.loading);
export const customizeError = derived(customizeStore, ($store) => $store.error);
