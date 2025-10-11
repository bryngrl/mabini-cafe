import { writable, derived } from 'svelte/store';
import { 
	getAllOrders,
	getOrderById,
	getOrdersByCustomerId,
	createOrder,
	updateOrderToPreparing,
	updateOrderToDelivering,
	updateOrderToCompleted,
	updateOrderToCancelled,
	updatePaymentStatus,
	setPaymentToCash,
	setPaymentToOnline,
	getTotalOrders,
	getTotalDelivered,
	getTotalCancelled
} from '$lib/utils/fetcher';

function createOrdersStore() {
	const { subscribe, set, update } = writable({
		orders: [],
		selectedOrder: null,
		statistics: {
			total: 0,
			delivered: 0,
			cancelled: 0
		},
		loading: false,
		error: null
	});

	return {
		subscribe,

		
		fetchAll: async () => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const orders = await getAllOrders();
				update(state => ({ ...state, orders, loading: false }));
				return orders;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		
		fetchById: async (orderId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const order = await getOrderById(orderId);
				update(state => ({ ...state, selectedOrder: order, loading: false }));
				return order;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		
		fetchByCustomerId: async (customerId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const orders = await getOrdersByCustomerId(customerId);
				update(state => ({ ...state, orders, loading: false }));
				return orders;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		
		create: async (orderData) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const result = await createOrder(orderData);
				update(state => ({ ...state, loading: false }));
				return result;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		
		updateToPreparing: async (orderId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await updateOrderToPreparing(orderId);
				const orders = await getAllOrders();
				update(state => ({ ...state, orders, loading: false }));
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		
		updateToDelivering: async (orderId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await updateOrderToDelivering(orderId);
				
				const orders = await getAllOrders();
				update(state => ({ ...state, orders, loading: false }));
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		updateToCompleted: async (orderId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await updateOrderToCompleted(orderId);
				const orders = await getAllOrders();
				update(state => ({ ...state, orders, loading: false }));
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		updateToCancelled: async (orderId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await updateOrderToCancelled(orderId);
				const orders = await getAllOrders();
				update(state => ({ ...state, orders, loading: false }));
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		markAsPaid: async (orderId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await updatePaymentStatus(orderId);
				update(state => ({ ...state, loading: false }));
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		setPaymentCash: async (orderId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await setPaymentToCash(orderId);
				update(state => ({ ...state, loading: false }));
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		setPaymentOnline: async (orderId) => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				await setPaymentToOnline(orderId);
				update(state => ({ ...state, loading: false }));
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		fetchStatistics: async () => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const [total, delivered, cancelled] = await Promise.all([
					getTotalOrders(),
					getTotalDelivered(),
					getTotalCancelled()
				]);
				update(state => ({ 
					...state, 
					statistics: { total, delivered, cancelled },
					loading: false 
				}));
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

export const ordersStore = createOrdersStore();

export const orders = derived(ordersStore, $orders => $orders.orders);
export const selectedOrder = derived(ordersStore, $orders => $orders.selectedOrder);
export const orderStatistics = derived(ordersStore, $orders => $orders.statistics);
export const ordersLoading = derived(ordersStore, $orders => $orders.loading);
