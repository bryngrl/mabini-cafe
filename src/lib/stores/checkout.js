import { writable, derived } from 'svelte/store';
import { cartTotal } from './cart';

// Selected address ID
export const selectedAddressId = writable(null);

// Selected shipping method (standard or priority)
export const selectedShippingMethod = writable(null);

// Shipping costs
const SHIPPING_COSTS = {
	standard: 79,
	priority: 109
};

// Calculate shipping cost based on selected method
export const shippingCost = derived(selectedShippingMethod, ($method) => {
	if (!$method) return 0;
	return SHIPPING_COSTS[$method] || 0;
});

// Calculate total (cart subtotal + shipping)
export const checkoutTotal = derived(
	[cartTotal, shippingCost],
	([$cartTotal, $shippingCost]) => {
		return $cartTotal + $shippingCost;
	}
);

// Reset checkout state
export function resetCheckout() {
	selectedAddressId.set(null);
	selectedShippingMethod.set(null);
}
