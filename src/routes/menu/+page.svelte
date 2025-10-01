
<script lang="ts">
	import Item from '$lib/components/ui/Item.svelte';
	import ItemModal from '$lib/components/ui/ItemModal.svelte';
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';
	import { auth } from '$lib/stores/auth';
	import { get } from 'svelte/store';

	let categories = ['All', 'Pastries', 'Beverages', 'Meals'];
	let selectedCategory = categories[0];
	let selectedSubcategory: string | null = null;

	let subcategories: Record<string, string[]> = {
		All: [
			'Savory Special Waffle',
			'Sweet Special Waffle',
			'Croffle',
			'Pizza',
			'Pasta',
			'All Day Breakfast',
			'Ube Series',
			'Refreshers',
			'Non-Caffeine Frappe',
			'Matcha Series',
			'Hot Coffee',
			'Iced Coffee',
			'Caffeine Frappe'
		],
		Pastries: ['Savory Special Waffle', 'Sweet Special Waffle', 'Croffle'],
		Beverages: [
			'Ube Series',
			'Refreshers',
			'Non-Caffeine Frappe',
			'Matcha Series',
			'Hot Coffee',
			'Iced Coffee',
			'Caffeine Frappe'
		],
		Meals: ['Pizza', 'Pasta', 'All Day Breakfast']
	};

	let items = [];
	let loading = false;
	let error = '';

	// Itemss
	onMount(async () => {
		loading = true;
		error = '';
		try {
			const response = await fetch('http://localhost/mabini-cafe/phpbackend/routes/menu');
			const data = await response.json();
			if (response.ok && Array.isArray(data)) {
				items = data.map((item) => ({
					id: item.id,
					name: item.name,
					price: item.price,
					image: item.image_path
						? 'http://localhost/mabini-cafe/' + item.image_path.replace(/^\/?/, '')
						: '',
					description: item.description
				}));
			} else {
				error = 'Failed to load menu items.';
			}
		} catch (err) {
			error = 'Network error. Please try again.';
		} finally {
			loading = false;
		}
	});
	//add to cart
	async function addToCart(item) {
		const { isLoggedIn, user } = get(auth);
		if (!isLoggedIn) {
			alert('You must be logged in to add items to your cart.');
			return;
		}
		try {
			const response = await fetch('http://localhost/mabini-cafe/phpbackend/routes/cart', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					user_id: user.id,
					menu_item_id: item.id,
					quantity: 1,
					subtotal: item.price
				})
			});
			const data = await response.json();
			if (response.ok) {
				alert('Added to cart!');
			} else {
				alert(data.error || 'Failed to add to cart');
			}
		} catch (err) {
			alert('Network error');
		}
	}

	let selectedItem = null;
	let modalOpen = false;

	function handleViewDetails(item) {
		selectedItem = item;
		modalOpen = true;
	}
	function closeModal() {
		modalOpen = false;
		selectedItem = null;
	}

	function selectSubcategory(subcategory: string) {
		selectedSubcategory = subcategory;
	}
</script>

<svelte:head>
	<title>Menu - Mabini Cafe</title>
	<meta name="description" content="Browse our delicious menu of coffee, pastries, and more" />
</svelte:head>
<div class="page-header">
	<h2 class="font-bold text-white text-align-center m-auto text-7xl">Menu</h2>
</div>

<div class="menu-page">
	<div class="container">
		<div class="category">
			{#each categories as category}
				<button
					class="btn-primary"
					class:active={selectedCategory === category}
					class:selected-category={selectedCategory === category}
					on:click={() => (selectedCategory = category)}
				>
					{category.charAt(0).toUpperCase() + category.slice(1)}
				</button>
			{/each}
		</div>

		<div class="main-menu ml-[50px] mr-[50px]">
			<div class="menu-layout">
				<aside class="sidebar">
					<ul class="">
						{#each subcategories[selectedCategory] as subcategory, index}
							<li
								class="cursor-pointer text-of-sub"
								class:first-item={index === 0}
								class:last-item={index === subcategories[selectedCategory].length - 1}
								class:selected-subcategory={selectedSubcategory === subcategory}
								on:click={() => selectSubcategory(subcategory)}
							>
								{subcategory}
							</li>
						{/each}
					</ul>
				</aside>

				<div class="items-container">
					<h1 class="menu-text">Our Menu</h1>
					{#if loading}
						<p>Loading menu items...</p>
					{/if}
					{#if error}
						<p style="color: red;">{error}</p>
					{/if}
					<div class="items-grid">
						{#each items.filter( (item) => (selectedCategory === 'All' ? !selectedSubcategory || (item.description && item.description
												.toLowerCase()
												.trim() === selectedSubcategory
													.toLowerCase()
													.trim()) : !selectedSubcategory || (item.description && item.description
												.toLowerCase()
												.trim() === selectedSubcategory.toLowerCase().trim())) ) as item}
							<div>
								<Item
									{item}
									on:viewDetails={() => handleViewDetails(item)}
									on:addToCart={() => addToCart(item)}
								/>
							</div>
						{/each}
					</div>
					<ItemModal {selectedItem} {modalOpen} on:close={closeModal} {addToCart} />
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.page-header {
		background-image: url(/images/hero-menu.png);
		height: 50vh;
		width: 100%;
		background-size: cover;
		display: flex;
		justify-content: center;
	}

	.text-of-sub {
		padding: 2rem;
		transition: all 0.3s ease;
	}

	.text-of-sub:hover {
		color: var(--color-mabini-dark-brown);
		background-color: #bdbdbd;
		font-weight: bold;
	}

	.text-of-sub.first-item:hover {
		border-radius: 1rem 0rem 0rem 0rem;
	}

	.text-of-sub.last-item:hover {
		border-radius: 0rem 0rem 0rem 1rem;
	}

	.text-of-sub:not(.first-item):not(.last-item):hover {
		border-radius: 0;
	}
	.category {
		display: flex;
		gap: 1rem;
		flex-wrap: wrap;
		justify-content: center;
		margin: 2rem 0;
	}
	.selected-category {
		background-color: var(--color-mabini-black);
		color: var(--color-mabini-white);
	}

	.menu-layout {
		display: flex;
		margin: 2rem 0;
		align-items: stretch;
	}

	.sidebar {
		flex: 0 0 250px;
		color: black;
		background-color: #f1f1f1;
		margin-left: 25px;
		border-radius: 1rem 0rem 0rem 1rem;
		align-self: stretch;
	}
	.items-container {
		flex: 1;
		align-self: stretch;
	}

	.items-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
		gap: 1rem;
		border-radius: 0rem 0rem 1rem 0rem;
		border: solid 1px gray;
	}
	.menu-text {
		background-color: black;
		color: white;
		text-align: left;
		padding: 12px;
		padding-left: 1rem;
		font-size: 24px;
		font-weight: bold;
		border-radius: 0rem 1rem 0rem 0rem;
		border: solid 1px gray;
	}
	.selected-subcategory.first-item:hover {
		color: var(--color-mabini-dark-brown);
		background-color: #bdbdbd;
		font-weight: bold;
	}
	.selected-subcategory.last-item {
		border-radius: 0rem 0rem 0rem 1rem;
		border: solid 1px gray;
	}
	.selected-subcategory {
		color: var(--color-mabini-dark-brown);
		background-color: #bdbdbd;
		font-weight: bold;
	}
</style>
