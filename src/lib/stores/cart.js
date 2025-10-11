import { writable, derived, get } from 'svelte/store';
import { 
	getAllCarts,
	getCartById,
	getCartByCustomerId,
	addToCart,
	updateCartItem,
	removeFromCart
} from '$lib/utils/fetcher';
import { currentUser } from './auth';

function createCartStore() {
	const { subscribe, set, update } = writable({
		items: [],
		loading: false,
		error: null
	});

	return {
		subscribe,

		fetchByCustomerId: async (customerId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const items = await getCartByCustomerId(customerId);
				update(state => ({ ...state, items: Array.isArray(items) ? items : [], loading: false }));
				return items;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		fetchById: async (cartId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const item = await getCartById(cartId);
				return item;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		add: async (cartItem) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await addToCart(cartItem);
				if (cartItem.user_id) {
					const items = await getCartByCustomerId(cartItem.user_id);
					update(state => ({ ...state, items: Array.isArray(items) ? items : [], loading: false }));
				} else {
					update(state => ({ ...state, loading: false }));
				}
				return result;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		updateItem: async (cartId, quantity, subtotal, userId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await updateCartItem(cartId, quantity, subtotal);
				if (userId) {
					const items = await getCartByCustomerId(userId);
					update(state => ({ ...state, items: Array.isArray(items) ? items : [], loading: false }));
				} else {
					update(state => ({ ...state, loading: false }));
				}
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		remove: async (cartId, userId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await removeFromCart(cartId);
				if (userId) {
					const items = await getCartByCustomerId(userId);
					update(state => ({ ...state, items: Array.isArray(items) ? items : [], loading: false }));
				} else {
					update(state => ({ ...state, loading: false }));
				}
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		clear: async (userId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const items = get({ subscribe }).items;
				for (const item of items) {
					await removeFromCart(item.id);
				}
				update(state => ({ ...state, items: [], loading: false }));
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

export const cartStore = createCartStore();

export const cartItems = derived(cartStore, $cart => $cart.items);
export const cartLoading = derived(cartStore, $cart => $cart.loading);
export const cartTotal = derived(cartStore, $cart => 
	$cart.items.reduce((sum, item) => sum + parseFloat(item.subtotal || 0), 0)
);
export const cartCount = derived(cartStore, $cart => 
	$cart.items.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0)
);
