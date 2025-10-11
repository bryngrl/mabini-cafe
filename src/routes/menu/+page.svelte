<script lang="ts">
	import Item from '$lib/components/ui/Item.svelte';
	import ItemModal from '$lib/components/ui/ItemModal.svelte';
	import { onMount } from 'svelte';
	import { menuStore, cartStore, authStore } from '$lib/stores';
	import { goto } from '$app/navigation';
	import { showSuccess, showError, showLoginRequired } from '$lib/utils/sweetalert';

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

	let selectedItem = null;
	let modalOpen = false;

	onMount(async () => {
		loading = true;
		error = '';
		try {
			items = await menuStore.fetchAll();
		} catch (err: any) {
			error = err.message || 'Failed to load menu items';
			console.error('Error loading menu:', err);
		} finally {
			loading = false;
		}
	});

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

	function selectCategory(category: string) {
		selectedCategory = category;
		selectedSubcategory = null;
	}

	async function handleAddToCart(item) {
		try {
			const quantity = 1;
			const subtotal = parseFloat(item.price) * quantity;
			
			await cartStore.add({
				user_id: $authStore.user?.id,
				menu_item_id: item.id,
				quantity: quantity,
				subtotal: subtotal
			});
			
			await showSuccess(`${item.name} has been added to your cart!`, 'Added to Cart');
		} catch (err: any) {
			// Handle login required error
			if (err.type === 'LOGIN_REQUIRED') {
				const result = await showLoginRequired();
				if (result.isConfirmed) {
					goto('/login');
				}
				return;
			}
			
			await showError(err.message || 'Failed to add item to cart', 'Error');
			console.error('Error adding to cart:', err);
		}
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
					on:click={() => selectCategory(category)}
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
						<div class="loading-state">
							<p>Loading menu items...</p>
						</div>
					{:else if error}
						<div class="error-message">
							<p>{error}</p>
						</div>
					{:else if items.length === 0}
						<div class="empty-state">
							<p>No menu items available</p>
						</div>
					{:else}
						<div class="items-grid">
							{#each items.filter((item) => {
								const matchesCategory = selectedCategory === 'All' || item.category_name === selectedCategory;
								const matchesSubcategory = !selectedSubcategory || (item.description && item.description
											.toLowerCase()
											.trim() === selectedSubcategory.toLowerCase().trim());

								return matchesCategory && matchesSubcategory;
							}) as item}
								<div>
									<Item
										{item}
										on:viewDetails={() => handleViewDetails(item)}
										on:addToCart={() => handleAddToCart(item)}
									/>
								</div>
							{/each}
						</div>
					{/if}

					<ItemModal
						{selectedItem}
						{modalOpen}
						on:close={closeModal}
						on:addToCart={() => selectedItem && handleAddToCart(selectedItem)}
					/>
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
		grid-template-columns: repeat(4, 1fr); /* Always 4 columns on desktop */
		gap: 1rem;
		border-radius: 0rem 0rem 1rem 0rem;
		border: solid 1px gray;
		padding: 1rem;
	}
	@media (max-width: 1024px) {
		.items-grid {
			grid-template-columns: repeat(2, 1fr);
		}
	}
	@media (max-width: 600px) {
		.items-grid {
			grid-template-columns: 1fr; 
		}
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

	.loading-state,
	.empty-state {
		padding: 3rem;
		text-align: center;
		color: #666;
		font-size: 1.1rem;
	}
</style>
