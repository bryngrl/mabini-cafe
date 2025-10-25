<script lang="ts">
	// Bugs in reposniveness
	
	import Item from '$lib/components/ui/Item.svelte';
	import ItemModal from '$lib/components/ui/ItemModal.svelte';
	import { onMount } from 'svelte';
	import { menuStore, cartStore, authStore, menuItems } from '$lib/stores';
	import { currentUser } from '$lib/stores';
	import { goto } from '$app/navigation';
	import { showSuccess, showError, showLoginRequired } from '$lib/utils/sweetalert';

	// Export the data from the server load
	export let data: { items: any[]; error?: string };

	onMount(() => {
		authStore.init();
	});

	let items = data.items || [];
	let loading = false;
	let error = data.error || '';

	$: uniqueCategories = [...new Set(items.map((item) => item.category_name))].filter(Boolean);
	$: categories = ['All', ...uniqueCategories];

	let selectedCategory = 'All';
	let selectedSubcategory: string | null = null;

	$: subcategories = (() => {
		const subcatMap: Record<string, string[]> = {};

		const allSubcats = [...new Set(items.map((item) => item.description))].filter(Boolean);
		subcatMap['All'] = allSubcats;

		uniqueCategories.forEach((category) => {
			const categoryItems = items.filter((item) => item.category_name === category);
			const categorySubcats = [...new Set(categoryItems.map((item) => item.description))].filter(
				Boolean
			);
			subcatMap[category] = categorySubcats;
		});

		return subcatMap;
	})();

	let selectedItem = null;
	let modalOpen = false;
	let mobileMenuOpen = false;

	function toggleMobileMenu() {
		mobileMenuOpen = !mobileMenuOpen;
	}

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
		mobileMenuOpen = false; 
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
	<h2 class="font-bold text-white text-center m-auto text-4xl sm:text-5xl md:text-6xl lg:text-7xl">Menu</h2>
</div>

<div class="menu-page">
	<div class="container">

		<!-- Category buttons - hidden on mobile, visible on desktop -->
		<div class="category hidden lg:flex">
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

		<!-- Mobile Dropdown Menu -->
		{#if mobileMenuOpen}
			<div class="mobile-menu lg:hidden">
				<div class="mobile-categories">
					<h3 class="text-lg font-bold mb-3">Categories</h3>
					{#each categories as category}
						<button
							class="mobile-category-btn"
							class:selected-category={selectedCategory === category}
							on:click={() => selectCategory(category)}
						>
							{category.charAt(0).toUpperCase() + category.slice(1)}
						</button>
					{/each}
				</div>
				
				{#if subcategories[selectedCategory].length > 0}
					<div class="mobile-subcategories">
						<h3 class="text-lg font-bold mb-3">Subcategories</h3>
						{#each subcategories[selectedCategory] as subcategory}
							<button
								class="mobile-subcategory-btn"
								class:selected-subcategory={selectedSubcategory === subcategory}
								on:click={() => {
									selectSubcategory(subcategory);
									mobileMenuOpen = false;
								}}
							>
								{subcategory}
							</button>
						{/each}
					</div>
				{/if}
			</div>
		{/if}

		<div class="main-menu px-4 sm:px-8 md:ml-[50px] md:mr-[50px]">
			<div class="menu-layout">
				<!-- Sidebar - Hidden on mobile, visible on desktop -->
				<aside class="sidebar hidden lg:block">
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
				<!-- Our Menu header with hamburger button -->
				<div class="menu-text-container">
					<h1 class="menu-text">Our Menu</h1>
					<!-- Mobile Hamburger Button beside "Our Menu" -->
					<button 
						class="hamburger-btn-inline lg:hidden"
						on:click={toggleMobileMenu}
						aria-label="Toggle menu"
					>
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
							<path stroke-linecap="round" stroke-linejoin="round" d={mobileMenuOpen ? "M6 18L18 6M6 6l12 12" : "M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"} />
						</svg>
					</button>
				</div>					{#if loading}
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
							{#each items
								.filter((item) => {
									const matchesCategory = selectedCategory === 'All' || item.category_name === selectedCategory;
									const matchesSubcategory = !selectedSubcategory || (item.description && item.description
												.toLowerCase()
												.trim() === selectedSubcategory.toLowerCase().trim());

									return matchesCategory && matchesSubcategory;
								})
								.slice(0, 50) as item}
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
		height: 40vh;
		width: 100%;
		background-size: cover;
		display: flex;
		justify-content: center;
		align-items: center;
		position: relative;
	}
	@media (min-width: 768px) {
		.page-header {
			height: 50vh;
		}
	}

	/* Hamburger button in header */
	.hamburger-btn-header {
		position: absolute;
		right: 1rem;
		top: 50%;
		transform: translateY(-50%);
		background-color: rgba(0, 0, 0, 0.7);
		color: white;
		border: 2px solid white;
		border-radius: 0.5rem;
		padding: 0.75rem;
		cursor: pointer;
		transition: all 0.3s ease;
		z-index: 10;
	}
	.hamburger-btn-header:hover {
		background-color: rgba(0, 0, 0, 0.9);
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
		flex-direction: column;
		margin: 2rem 0;
		align-items: stretch;
	}
	@media (min-width: 1024px) {
		.menu-layout {
			flex-direction: row;
		}
	}

	.sidebar {
		width: 100%;
		color: black;
		background-color: #f1f1f1;
		border-radius: 1rem 1rem 0 0;
		margin-bottom: 1rem;
	}
	@media (min-width: 1024px) {
		.sidebar {
			flex: 0 0 250px;
			margin-left: 25px;
			margin-bottom: 0;
			border-radius: 1rem 0 0 1rem;
		}
	}
	.items-container {
		flex: 1;
		align-self: stretch;
	}

	.items-grid {
		display: grid;
		grid-template-columns: 1fr;
		gap: 1rem;
		border-radius: 0 0 1rem 1rem;
		border: solid 1px gray;
		padding: 1rem;
	}
	@media (min-width: 640px) {
		.items-grid {
			grid-template-columns: repeat(2, 1fr);
		}
	}
	@media (min-width: 1024px) {
		.items-grid {
			grid-template-columns: repeat(3, 1fr);
			border-radius: 0 0 1rem 0;
		}
	}
	@media (min-width: 1280px) {
		.items-grid {
			grid-template-columns: repeat(4, 1fr);
		}
	}
	.menu-text-container {
		background-color: black;
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 12px 1rem;
		border: solid 1px gray;
		border-radius: 0 0 0 0;
	}
	@media (min-width: 1024px) {
		.menu-text-container {
			border-radius: 0 1rem 0 0;
		}
	}
	.menu-text {
		color: white;
		font-size: 18px;
		font-weight: bold;
		margin: 0;
	}
	@media (min-width: 768px) {
		.menu-text {
			font-size: 24px;
		}
	}

	.hamburger-btn-inline {
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 0.5rem;
		background-color: white;
		color: black;
		border: none;
		border-radius: 0.375rem;
		cursor: pointer;
		transition: background-color 0.3s ease;
	}
	.hamburger-btn-inline:hover {
		background-color: #f5f5f5;
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

	/* Hamburger Menu Styles */
	.mobile-menu {
		background-color: white;
		border: 1px solid #e5e5e5;
		border-radius: 0.5rem;
		padding: 1rem;
		margin-bottom: 1rem;
		max-height: 70vh;
		overflow-y: auto;
	}

	.mobile-categories,
	.mobile-subcategories {
		margin-bottom: 1.5rem;
	}

	.mobile-category-btn,
	.mobile-subcategory-btn {
		display: block;
		width: 100%;
		text-align: left;
		padding: 0.75rem 1rem;
		margin-bottom: 0.5rem;
		background-color: #f5f5f5;
		border: 2px solid transparent;
		border-radius: 0.5rem;
		font-size: 1rem;
		cursor: pointer;
		transition: all 0.3s ease;
	}

	.mobile-category-btn:hover,
	.mobile-subcategory-btn:hover {
		background-color: #e5e5e5;
	}

	.mobile-category-btn.selected-category {
		background-color: black;
		color: white;
		font-weight: 600;
	}

	.mobile-subcategory-btn.selected-subcategory {
		background-color: var(--color-mabini-dark-brown);
		color: white;
		font-weight: 600;
		border-color: var(--color-mabini-dark-brown);
	}
</style>
