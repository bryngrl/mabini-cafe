<!-- Item Component -->
<!-- This is where the items will be displayed -->
<!-- Fetch the items to the backend -->

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

<link rel="stylesheet" href="/styles/item.css" />
<div class="item-card">
	<div class="item-content">
		<img src={item.image} alt={item.name} class="w-20 h-20 object-contain" />
		<h2 class="item-name font-extrabold">{item.name.toUpperCase()}</h2>
		<p class="item-price">
			{typeof item.price === 'number' ? `₱ ${item.price.toFixed(2)}` : item.price}
		</p>
		{#if item.description}
			<p class="item-desc">{item.description}</p>
		{/if}
		<button class="cursor-pointer cart" on:click={() => dispatch('addToCart', item)}>Add to Cart</button>
		<button class="cursor-pointer" on:click={() => viewDetails(item)}>View Details</button>
	</div>
</div>

<style>
	.item-card {
		border: 1px solid #eee;
		border-radius: 0.5rem;
		overflow: hidden;
		width: 200px;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
		transition: transform 0.3s ease;
		margin: 1rem;
		height: 400px;
	}

	.item-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
	}

	.item-content {
		padding: 1rem;
		text-align: center;
		display: flex;
		flex-direction: column;
		gap: 0.25rem;
		height: 100%;
	}

	.item-content img {
		width: 100%;
		max-height: 120px;
		height: auto;
		object-fit: contain;
		border-radius: 0.25rem;
		margin-bottom: 0.5rem;
	}

	.item-content h2 {
		font-size: 1.1rem;
		text-align: left;
		margin: 0;
		font-weight: bolder;
		color: black;
	}

	.item-content p {
		font-size: 1rem;
		text-align: left;
		color: #666666;
		font-weight: bold;
		padding-bottom: 1rem;
	}

	.cart {
		background-color: #d9d9d9;
		color: #666666;
		font-weight: bold;
		margin-top: auto;
	}

	.cart:hover {
		background-color: var(--color-mabini-yellow);
		color: var(--color-mabini-dark-brown);
	}

	.item-content button {
		border: none;
		padding: 0.5rem 1rem;
		border-radius: 3rem;
		cursor: pointer;
		font-size: 0.9rem;
		transition: background-color 0.3s ease;
		width: 100%;
	}

	.item-content button:not(.cart) {
		color: #2a1c0f;
	}

	.item-content button:not(.cart):hover {
		color: #666666;
	}
</style>
