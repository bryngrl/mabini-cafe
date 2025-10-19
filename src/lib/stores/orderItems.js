import { writable, derived } from 'svelte/store';
import {
	getAllOrderItems,
	getOrderItemById,
	getOrderItemsByOrderId,
	getOrdersByCustomerId,
	createOrderItem
} from '$lib/utils/fetcher';

function createOrderItemsStore() {
	const { subscribe, set, update } = writable({
		items: [],
		selectedItem: null,
		loading: false,
		error: null
	});

	return {
		subscribe,

		fetchByUserId: async (userId) => {
			update((state) => ({ ...state, loading: true, error: null }));
			try {
				const orders = await getOrdersByCustomerId(userId);
				update((state) => ({ ...state, items: orders, loading: false }));
				return orders;
			} catch (error) {
				update((state) => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},
		fetchAll: async () => {
			update((state) => ({ ...state, loading: true, error: null }));
			try {
				const items = await getAllOrderItems();
				update((state) => ({ ...state, items, loading: false }));
				return items;
			} catch (error) {
				update((state) => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		fetchById: async (itemId) => {
			update((state) => ({ ...state, loading: true, error: null }));
			try {
				const item = await getOrderItemById(itemId);
				update((state) => ({ ...state, selectedItem: item, loading: false }));
				return item;
			} catch (error) {
				update((state) => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		fetchByOrderId: async (orderId) => {
			update((state) => ({ ...state, loading: true, error: null }));
			try {
				const items = await getOrderItemsByOrderId(orderId);
				update((state) => ({ ...state, items, loading: false }));
				return items;
			} catch (error) {
				update((state) => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		create: async (orderItemData) => {
			update((state) => ({ ...state, loading: true, error: null }));
			try {
				const result = await createOrderItem(orderItemData);
				update((state) => ({ ...state, loading: false }));
				return result;
			} catch (error) {
				update((state) => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		clearError: () => {
			update((state) => ({ ...state, error: null }));
		}
	};
}

export const orderItemsStore = createOrderItemsStore();

export const orderItems = derived(orderItemsStore, ($orderItems) => $orderItems.items);
export const selectedOrderItem = derived(
	orderItemsStore,
	($orderItems) => $orderItems.selectedItem
);
export const orderItemsLoading = derived(orderItemsStore, ($orderItems) => $orderItems.loading);
