import { writable, derived } from 'svelte/store';
import { 
	storeShippingInfo,
	getShippingInfoByUserId,
	getShippingInfoById,
	updateShippingInfo,
	deleteShippingInfo
} from '$lib/utils/fetcher';

function createShippingStore() {
	const { subscribe, set, update } = writable({
		shippingInfo: null,
		loading: false,
		error: null
	});

	return {
		subscribe,

		/**
		 * Store new shipping information
		 * @param {Object} shippingData - The shipping information to store
		 */
		store: async (shippingData) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await storeShippingInfo(shippingData);
				update(state => ({ 
					...state, 
					shippingInfo: result,
					loading: false 
				}));
				return result;
			} catch (error) {
				update(state => ({ 
					...state, 
					loading: false, 
					error: error instanceof Error ? error.message : 'An error occurred'
				}));
				throw error;
			}
		},

		/**
		 * Get shipping information by user ID
		 * @param {number} userId - The user ID to get shipping info for
		 */
		fetchByUserId: async (userId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const shippingInfo = await getShippingInfoByUserId(userId);
				update(state => ({ 
					...state, 
					shippingInfo: shippingInfo,
					loading: false 
				}));
				return shippingInfo;
			} catch (error) {
				update(state => ({ 
					...state, 
					loading: false, 
					error: error instanceof Error ? error.message : 'An error occurred'
				}));
				throw error;
			}
		},

		/**
		 * Get shipping information by shipping info ID
		 * @param {number} shipInfoId - The shipping info ID
		 */
		fetchById: async (shipInfoId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const shippingInfo = await getShippingInfoById(shipInfoId);
				update(state => ({ 
					...state, 
					shippingInfo: shippingInfo,
					loading: false 
				}));
				return shippingInfo;
			} catch (error) {
				update(state => ({ 
					...state, 
					loading: false, 
					error: error instanceof Error ? error.message : 'An error occurred'
				}));
				throw error;
			}
		},

		/**
		 * Update shipping information by user ID
		 * @param {number} userId - The user ID to update shipping info for
		 * @param {Object} shippingData - The updated shipping information
		 */
		update: async (userId, shippingData) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await updateShippingInfo(userId, shippingData);
				update(state => ({ 
					...state, 
					shippingInfo: result,
					loading: false 
				}));
				return result;
			} catch (error) {
				update(state => ({ 
					...state, 
					loading: false, 
					error: error instanceof Error ? error.message : 'An error occurred'
				}));
				throw error;
			}
		},

		/**
		 * Delete shipping information
		 * @param {number} shipInfoId - The shipping info ID to delete
		 */
		delete: async (shipInfoId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await deleteShippingInfo(shipInfoId);
				update(state => ({ 
					...state, 
					shippingInfo: null, // Clear after deletion
					loading: false 
				}));
				return result;
			} catch (error) {
				update(state => ({ 
					...state, 
					loading: false, 
					error: error instanceof Error ? error.message : 'An error occurred'
				}));
				throw error;
			}
		},

		/**
		 * Clear shipping information from store
		 */
		clear: () => {
			set({
				shippingInfo: null,
				loading: false,
				error: null
			});
		},

		/**
		 * Clear error state
		 */
		clearError: () => {
			update(state => ({ ...state, error: null }));
		}
	};
}

export const shippingStore = createShippingStore();

// Derived stores for easier access
export const shippingInfo = derived(shippingStore, $shipping => $shipping.shippingInfo);
export const shippingLoading = derived(shippingStore, $shipping => $shipping.loading);
export const shippingError = derived(shippingStore, $shipping => $shipping.error);
