import { writable, derived } from 'svelte/store';
import { 
	storeShippingInfo,
	getShippingInfoByUserId,
	getShippingInfoById,
	updateShippingInfo,
	deleteShippingInfo,
	addNewShippingInfo
} from '$lib/utils/fetcher';

function createShippingStore() {
	const { subscribe, set, update } = writable({
		shippingAddresses: [], // Changed to array to support multiple addresses
		loading: false,
		error: null
	});

	return {
		subscribe,

		/**
		 * Store new shipping information (for first-time users)
		 * @param {Object} shippingData - The shipping information to store
		 */
		store: async (shippingData) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await storeShippingInfo(shippingData);
				update(state => ({ 
					...state, 
					shippingAddresses: [result],
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
		 * Get all shipping addresses by user ID
		 * @param {number} userId - The user ID to get shipping info for
		 */
		fetchByUserId: async (userId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const addresses = await getShippingInfoByUserId(userId);
				update(state => ({ 
					...state, 
					shippingAddresses: Array.isArray(addresses) ? addresses : [],
					loading: false 
				}));
				return addresses;
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
		 * Add a new shipping address for a user
		 * @param {number} userId - The user ID
		 * @param {Object} shippingData - The new shipping address data
		 */
		addAddress: async (userId, shippingData) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await addNewShippingInfo(userId, shippingData);
				update(state => ({ 
					...state, 
					shippingAddresses: [...state.shippingAddresses, result],
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
		 * Update shipping information by user ID
		 * @param {number} userId - The user ID to update shipping info for
		 * @param {Object} shippingData - The updated shipping information
		 */
		updateAddress: async (userId, shippingData) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await updateShippingInfo(userId, shippingData);
				// Refresh the list after update
				const addresses = await getShippingInfoByUserId(userId);
				update(state => ({ 
					...state, 
					shippingAddresses: Array.isArray(addresses) ? addresses : [],
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
		 * Delete shipping address
		 * @param {number} shipInfoId - The shipping info ID to delete
		 * @param {number} userId - The user ID to refresh addresses after deletion
		 */
		deleteAddress: async (shipInfoId, userId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await deleteShippingInfo(shipInfoId);
				// Remove from local state
				update(state => ({ 
					...state, 
					shippingAddresses: state.shippingAddresses.filter(addr => addr.id !== shipInfoId),
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
				shippingAddresses: [],
				loading: false,
				error: null
			});
		},

		/**
		 * Clear error state
		 */
		clearError: () => {
			update(state => ({ ...state, error: null }));
		},

	};
}

export const shippingStore = createShippingStore();

// Derived stores for easier access
export const shippingInfo = derived(shippingStore, $shipping => $shipping.shippingAddresses);
export const shippingAddresses = derived(shippingStore, $shipping => $shipping.shippingAddresses);
export const shippingLoading = derived(shippingStore, $shipping => $shipping.loading);
export const shippingError = derived(shippingStore, $shipping => $shipping.error);
