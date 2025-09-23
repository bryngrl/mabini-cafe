<!-- Menu -->
<!-- Product fetch for menu items -->
<script lang="ts">
	import Item from '$lib/components/ui/Item.svelte';
	import ItemModal from '$lib/components/ui/ItemModal.svelte';

	let categories = ['All', 'Pastries', 'Beverages', 'Meals'];
	let selectedCategory = categories[0];

	// Will be fetcch din sa api ni dom
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
							>
								{subcategory}
							</li>
						{/each}
					</ul>
				</aside>

				<div class="items-container">
					<h1 class="menu-text">Our Menu</h1>
					<div class="items-grid">
						<a href="/menu/[modal]">
							<Item />
						</a>
					</div>
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
		height: 1440px;
		align-self: stretch;
	}
	.items-container {
		
		flex: 1;
		align-self: stretch;
	}

	.items-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 2rem;
		border-radius: 0rem 0rem 1rem 0rem;
		border: solid 1px gray;
		height: 1380px;
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
</style>
