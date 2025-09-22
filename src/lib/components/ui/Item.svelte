<!-- Item Component -->
<!-- This is where the items will be displayed -->
 <!-- Fetch the items to the backend -->

<script lang="ts">
	interface Item {
		name: string;
		price: string;
		image: string;
	}

	let item: Item = {
		// This should be replaced with actual data fetched from  a API ni domsikie
		name: 'Bacon & Egg',
		price: '$5.00',
		image: '/items/bacon-egg.png'
	};

	function addToCart(item: Item) {
		console.log(`Added ${item.name} to cart`);
	}

	function viewDetails(item: Item) {
		console.log(`Viewing details for ${item.name}`);
	}
</script>

<link rel="stylesheet" href="/styles/item.css" />
<div class="item-card">
	<div class="item-content">
		<img src={item.image} alt={item.name} />
		<h2 class="item-name font-extrabold">{item.name.toUpperCase()}</h2>
		<p class="item-price">{item.price}</p>
		<button class="cursor-pointer cart" on:click={() => addToCart(item)}>Add to Cart</button>
		<button class="cursor-pointer" on:click={() => viewDetails(item)}>View Details</button>
	</div>
</div>

<style>
	.item-card {
		border: 1px solid #eee;
		border-radius: 0.5rem;
		overflow: hidden;
		width: 280px;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
		transition: transform 0.3s ease;
		margin: 1rem;
        
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
	}

	.item-content img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		border-radius: 0.25rem;
		gap: 0.5rem;
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
