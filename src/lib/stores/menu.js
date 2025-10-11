import { writable, derived } from 'svelte/store';
import { 
	getAllMenuItems, 
	getMenuItemById,
	getMenuItemsByCategory,
	getMenuItemsByDescription,
	createMenuItem,
	updateMenuItem,
	deleteMenuItem
} from '$lib/utils/fetcher';


function createMenuStore() {
	const { subscribe, set, update } = writable({
		items: [],
		selectedItem: null,
		loading: false,
		error: null
	});

	return {
		subscribe,

		
		fetchAll: async () => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const items = await getAllMenuItems();
				update(state => ({ ...state, items, loading: false }));
				return items;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		
		fetchById: async (itemId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const item = await getMenuItemById(itemId);
				update(state => ({ ...state, selectedItem: item, loading: false }));
				return item;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		
		fetchByCategory: async (categoryId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const items = await getMenuItemsByCategory(categoryId);
				update(state => ({ ...state, items, loading: false }));
				return items;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		
		fetchByDescription: async (description) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const items = await getMenuItemsByDescription(description);
				update(state => ({ ...state, items, loading: false }));
				return items;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		
		create: async (itemData, imageFile) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await createMenuItem(itemData, imageFile);
				
				const items = await getAllMenuItems();
				update(state => ({ ...state, items, loading: false }));
				return result;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		
		update: async (itemId, itemData, imageFile) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await updateMenuItem(itemId, itemData, imageFile);
			
				const items = await getAllMenuItems();
				update(state => ({ ...state, items, loading: false }));
				return result;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		delete: async (itemId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await deleteMenuItem(itemId);
			
				const items = await getAllMenuItems();
				update(state => ({ ...state, items, loading: false }));
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

export const menuStore = createMenuStore();


export const menuItems = derived(menuStore, $menu => $menu.items);
export const selectedMenuItem = derived(menuStore, $menu => $menu.selectedItem);
export const menuLoading = derived(menuStore, $menu => $menu.loading);
