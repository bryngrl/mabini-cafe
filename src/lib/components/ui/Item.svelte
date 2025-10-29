<!-- Item Component -->

<script lang="ts">
	import { createEventDispatcher } from 'svelte';

	export let item: {
		name: string;
		price: string | number;
		image_path: string;
		description?: string;
		isAvailable?: boolean | number;
	};
	interface Item {
		name: string;
		price: string | number;
		image_path: string;
		description?: string;
		isAvailable?: boolean | number;
	}
	const dispatch = createEventDispatcher();

	function viewDetails(item: Item) {
		dispatch('viewDetails', item);
	}
	
	// Check if item is available
	$: isAvailable = item.isAvailable === 1 || item.isAvailable === true;
</script>

<div
	class="flex gap-[2%] flex-wrap flex-row box-shadow-lg p-4 rounded-lg hover:shadow-xl transition-shadow duration-300 item-card"
	class:unavailable={!isAvailable}
>
	<div class="w-50 h-100 self-start p-4 text-center flex flex-col gap-1">
		<img
			src={`https://mabini-cafe.bscs3a.com/phpbackend/${item.image_path.replace(/^\/?/, '')}`}
			alt={item.name}
			class="w-40 h-40 object-contain rounded mb-2 self-center"
			class:grayscale={!isAvailable}
		/>
		<div class="flex flex-col items-start w-full mb-2 gap-2">
			<h2 class="font-extrabold text-[1rem] text-left min-h-[3rem] flex items-start"
				class:line-through={!isAvailable}>
				{item.name.toUpperCase()}
			</h2>
			<p class="text-base text-left text-gray-600 font-bold mt-1"
				class:line-through={!isAvailable}>
				₱{item.price}
			</p>
			{#if !isAvailable}
				<span class="text-xs text-red-600 font-semibold bg-red-100 px-2 py-1 rounded">
					Currently Unavailable
				</span>
			{/if}
		</div>
		<div class="flex flex-col gap-2 mt-auto">
			<button
				class="cart rounded-2xl h-8 flex items-center justify-center"
				on:click={() => dispatch('addToCart', item)}
				disabled={!isAvailable}
				class:disabled={!isAvailable}
			>
				{isAvailable ? 'Add to Cart' : 'Unavailable'}
			</button>
			<button 
				class="cursor-pointer" 
				on:click={() => viewDetails(item)}
				disabled={!isAvailable}
				class:disabled={!isAvailable}
			>
				View Details
			</button>
		</div>
	</div>
</div>
<style>
	.item-card {
		border: 1px solid #eee;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
		transition: transform 0.3s ease, opacity 0.3s ease;
	}
	.item-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
	}
	
	.item-card.unavailable {
		opacity: 0.6;
		background-color: #f9f9f9;
	}
	
	.item-card.unavailable:hover {
		transform: none;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
	}
	
	.grayscale {
		filter: grayscale(70%);
	}
	
	.line-through {
		text-decoration: line-through;
		color: #999;
	}
	
	.cart {
		background-color: #d9d9d9;
		color: #666666;
		font-weight: bold;
		margin-top: 5px;
		transition: background-color 0.3s ease;
		cursor: pointer;
	}

	.cart:hover {
		background-color: var(--color-mabini-yellow);
		color: var(--color-mabini-dark-brown);
	}
	
	.cart.disabled,
	button.disabled {
		background-color: #e0e0e0;
		color: #999;
		cursor: not-allowed;
		opacity: 0.5;
	}
	
	.cart.disabled:hover,
	button.disabled:hover {
		background-color: #e0e0e0;
		color: #999;
	}
	
	button:not(.cart) {
		color: #2a1c0f;
		cursor: pointer;
	}

	button:not(.cart):hover {
		color: #666666;
	}
	
	button:not(.cart).disabled:hover {
		color: #999;
	}
</style>
