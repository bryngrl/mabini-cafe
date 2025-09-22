<!-- Menu -->
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

<div class="menu-page">
	<div class="container">
		<div class="page-header">
			<!-- Hero Image for Menu -->
		</div>

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

		<div class="main-menu">
			<div class="menu-layout">
				<aside class="sidebar">
					<ul class="space-y-10">
						{#each subcategories[selectedCategory] as subcategory}
							<li class="cursor-pointer hover:text-mabini-yellow">{subcategory}</li>
						{/each}
					</ul>
				</aside>

				<div class="items-container">
					<div class="items-grid">
						<Item />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.category {
		display: flex;
		gap: 1rem;
		flex-wrap: wrap;
		justify-content: center;
		margin: 2rem 0;
	}

	.menu-layout {
		display: flex;
		gap: 2rem;
		margin: 2rem 0;
		align-items: flex-start;
	}

	.sidebar {
		flex: 0 0 250px;
		color: white;
		background-color: gray;
		margin-left: 25px;
		padding: 1rem;
		border-radius: 0.5rem;
	}

	.items-container {
		flex: 1;
	}

	.items-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 2rem;
	}
</style>
