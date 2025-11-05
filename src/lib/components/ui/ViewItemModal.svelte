<script lang="ts">
	import { orderItemsStore } from '$lib/stores/orderItems';
	import { menuStore } from '$lib/stores/menu';
	import { onMount } from 'svelte';

	interface Props {
		order: {
			id: number;
			user_id?: number;
			customer_name?: string;
			total_amount: number;
			order_time: string;
			status: string; // The status will be passed in from outside
			payment_status?: string;
			payment_method?: string;
			items?: any[];
			shipping_address?: string;
			message?: string;
			shipping_name?: string;
			shipping_fee?: number;
			shipping_fee_id?: number;
		};
		onClose: () => void;
	}

	let { order, onClose }: Props = $props();

	let orderItems: any[] = $state([]);
	let menuItemsMap = new Map();
	let isLoadingItems = $state(true);

	async function fetchOrderItems() {
		try {
			isLoadingItems = true;

			// Fetch menu items first
			const menuItems = await menuStore.fetchAll();
			menuItemsMap = new Map(menuItems.map(item => [item.id, item]));

			// Fetch items for this specific order
			const items = await orderItemsStore.fetchByOrderId(order.id);
			if (Array.isArray(items)) {
				// Filter items for this specific order and add menu item details
				orderItems = items
					.filter(item => item.order_id === order.id)
					.map(item => ({
						...item,
						menuItem: menuItemsMap.get(item.menu_item_id),
						name: menuItemsMap.get(item.menu_item_id)?.name || 'Unknown Item',
						quantity: item.quantity
					}));
			}

			isLoadingItems = false;
		} catch (error) {
			console.error('Error fetching order items:', error);
			isLoadingItems = false;
		}
	}

	onMount(() => {
		fetchOrderItems();
	});
</script>

<div
	class="fixed inset-0 backdrop-blur-lg bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-10"
	onclick={onClose}
	onkeydown={(e) => e.key === 'Escape' && onClose()}
	role="button"
	tabindex="0"
	aria-label="Close modal"
>
	<div
		class="bg-white rounded-2xl shadow-2xl w-[600px] max-h-[90vh]"
		onclick={(e) => e.stopPropagation()}
		onkeydown={(e) => e.stopPropagation()}
		role="dialog"
		aria-modal="true"
		tabindex="-1"
	>
		<div class="bg-black text-white p-5 rounded-t-2xl flex justify-between items-center">
			<h1 class="text-2xl font-bold">ORDER DETAILS</h1><button
				type="button"
				onclick={onClose}
				class="hover:bg-gray-900 cursor-pointer rounded-full p-2 transition-colors"
				aria-label="Close"
			>
				<img src="/icons/exit.svg" alt="Close" class="w-5 h-5" />
			</button>
		</div>
		<div class="p-10 pt-8 space-y-4">
			<div class="flex justify-between items-start">
				<span class="font-bold text-lg uppercase">Order ID</span>
				<span class="text-lg font-semibold items-end text-right">#{String(order.id).padStart(4, '0')}</span>
			</div>
			<div class="flex justify-between items-start">
				<span class="font-bold text-lg uppercase">Customer Name</span>
				<span class="text-lg items-end text-right">{order.customer_name || 'N/A'}</span>
			</div>
			<div class="border-t pt-4">
				<span class="font-bold text-lg uppercase block mb-3">Order/s</span>
				<div class="bg-gray-50 p-4 rounded-lg space-y-2">
					{#if isLoadingItems}
						<p class="text-sm text-gray-500 italic">Loading items...</p>
					{:else if orderItems && orderItems.length > 0}
						{#each orderItems as item}
							<div class="flex justify-between text-sm">
								<span class="font-medium">{item.name}</span>
								<span class="text-gray-600">Qty: {item.quantity}</span>
							</div>
						{/each}
					{:else}
						<p class="text-sm text-gray-500 italic">No items data available</p>
					{/if}
				</div>
			</div>
			<div class="flex justify-between items-start">
				<span class="font-bold text-lg uppercase">Address</span>
				<span class="text-lg text-right max-w-[60%]"
					>{order.shipping_address || 'No address provided'}</span
				>
			</div>
			<div class="flex justify-between items-start">
				<span class="font-bold text-lg uppercase">Shipping Method</span>
				<span class="text-lg">{order.shipping_name || 'N/A'}</span>
			</div>
			<div class="flex justify-between items-start border-t pt-4">
				<span class="font-bold text-lg uppercase">Total Amount</span>
				<span class="text-lg font-bold text-green-600">₱{order.total_amount}</span>
			</div>
			<div class="flex justify-between items-start">
				<span class="font-bold text-lg uppercase">Time Ordered</span>
				<span class="text-lg text-right">
					{order.order_time
						? new Date(order.order_time.replace(' ', 'T')).toLocaleString('en-US', {
								month: 'short',
								day: 'numeric',
								year: 'numeric',
								hour: 'numeric',
								minute: '2-digit',
								hour12: true
							})
						: 'N/A'}
				</span>
			</div>
			<div class="flex justify-between items-start">
				<span class="font-bold text-lg uppercase">Message</span>
				<span class="text-lg text-right max-w-[60%] italic">{order.message || 'No message'}</span>
			</div>
			<div class="flex justify-between items-center border-t pt-4">
				<span class="font-bold text-lg uppercase">Status</span>
				<span
					class="px-4 py-2 rounded-full text-sm font-bold capitalize
                    {order.status?.toLowerCase() === 'pending' || !order.status
						? 'bg-gray-200 text-gray-700'
						: order.status?.toLowerCase() === 'preparing'
							? 'bg-blue-200 text-blue-800'
							: order.status?.toLowerCase() === 'delivering'
								? 'bg-purple-200 text-purple-800' // <--- This handles 'Delivering'
								: order.status?.toLowerCase() === 'completed'
									? 'bg-green-200 text-green-800'
									: 'bg-red-200 text-red-800'}"
				>
					{order.status}
				</span>
			</div>
		</div>
	</div>
</div>
