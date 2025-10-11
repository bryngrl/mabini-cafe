<!-- Item Component -->

<script lang="ts">
	import { createEventDispatcher } from 'svelte';

	export let item: {
		name: string;
		price: string | number;
		image_path: string;
		description?: string;
	};
	interface Item {
		name: string;
		price: string | number;
		image_path: string;
		description?: string;
	}
	const dispatch = createEventDispatcher();

	function viewDetails(item: Item) {
		dispatch('viewDetails', item);
	}
</script>

<div
	class="flex gap-[2%] flex-wrap flex-row box-shadow-lg p-4 rounded-lg hover:shadow-xl transition-shadow duration-300 item-card"
>
	<div class="w-50 h-100 self-start p-4 text-center flex flex-col gap-1">
		<img
			src={`http://localhost/mabini-cafe/phpbackend/${item.image_path.replace(/^\/?/, '')}`}
			alt={item.name}
			class="w-40 h-40 object-contain rounded mb-2 self-center"
		/>
		<div class="flex flex-col items-start w-full mb-2 gap-2">
			<h2 class="font-extrabold text-[1rem] text-left min-h-[3rem] flex items-start">
				{item.name.toUpperCase()}
			</h2>
			<p class="text-base text-left text-gray-600 font-bold mt-1">
				₱{item.price}
			</p>
		</div>
		<div class="flex flex-col gap-2 mt-auto">
			<button
				class="cursor-pointer cart rounded-2xl h-8 flex items-center justify-center"
				on:click={() => dispatch('addToCart', item)}
			>
				Add to Cart
			</button>
			<button class="cursor-pointer" on:click={() => viewDetails(item)}>View Details</button>
		</div>
	</div>
</div>
<style>
	.item-card {
		border: 1px solid #eee;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
		transition: transform 0.3s ease;
	}
	.item-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
	}
	.cart {
		background-color: #d9d9d9;
		color: #666666;
		font-weight: bold;
		margin-top: 5px;
	}

	.cart:hover {
		background-color: var(--color-mabini-yellow);
		color: var(--color-mabini-dark-brown);
	}
	button:not(.cart) {
		color: #2a1c0f;
	}

	button:not(.cart):hover {
		color: #666666;
	}
</style>
