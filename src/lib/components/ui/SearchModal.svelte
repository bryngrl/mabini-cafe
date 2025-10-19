<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import { menuStore, cartStore } from '$lib/stores';
	import { authStore } from '$lib/stores/auth';
	import { showSuccess, showError, showLoginRequired } from '$lib/utils/sweetalert';
	import { browser } from '$app/environment';

	export let isOpen = false;
	export let onClose: () => void;

	let searchTerm = '';
	let menuItems = $menuStore.items || [];
	let filteredItems = [];

	$: {
		if (browser && isOpen) {
			document.body.style.overflow = 'hidden';

			if (menuItems.length === 0) {
				menuStore
					.fetchAll()
					.then((items) => {
						menuItems = items;
					})
					.catch((err) => {
						console.error('Error loading menu:', err);
					});
			}
		} else if (browser) {
			document.body.style.overflow = '';
		}
	}

	onMount(() => {
		return () => {
			if (browser) {
				document.body.style.overflow = '';
			}
		};
	});

	function handleSearchInput(event: Event) {
		const target = event.target as HTMLInputElement;
		searchTerm = target.value;

		if (searchTerm.trim() === '') {
			filteredItems = [];
		} else {
			filteredItems = menuItems.filter(
				(item) =>
					item.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
					item.description?.toLowerCase().includes(searchTerm.toLowerCase()) ||
					item.category_name?.toLowerCase().includes(searchTerm.toLowerCase())
			);
		}
	}

	$: {
		if (searchTerm.trim() === '') {
			filteredItems = [];
		} else {
			filteredItems = menuItems.filter(
				(item) =>
					item.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
					item.description?.toLowerCase().includes(searchTerm.toLowerCase()) ||
					item.category_name?.toLowerCase().includes(searchTerm.toLowerCase())
			);
		}
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

	function handleViewDetails(item) {
		onClose();
		goto(`/menu?item=${item.id}`);
	}
</script>

{#if isOpen}
	<!-- svelte-ignore a11y_no_static_element_interactions -->
	<div class="modal-overlay" on:click={onClose}>
		<div class="modal-content" on:click|stopPropagation>
			<div class="modal-header">
				<h2 class="modal-title">SEARCH</h2>
				<button class="close-btn" on:click={onClose}>
					<svg
						width="24"
						height="20"
						viewBox="0 0 34 29"
						fill="none"
						xmlns="http://www.w3.org/2000/svg"
					>
						<path
							d="M29.0118 0.497009C30.2196 0.496942 31.3818 0.958407 32.2605 1.78699C33.1393 2.61557 33.6682 3.74863 33.739 4.95433L33.7469 5.23213V24.1726C33.747 25.3804 33.2855 26.5426 32.4569 27.4213C31.6284 28.3001 30.4953 28.829 29.2896 28.8998L29.0118 28.9077H11.8202C11.0687 28.9075 10.3281 28.7285 9.6595 28.3855C8.99092 28.0424 8.4136 27.5451 7.97525 26.9348L7.80478 26.6822L1.36502 16.3754C1.07629 15.9137 0.911911 15.3852 0.887849 14.8412C0.863788 14.2972 0.980864 13.7562 1.2277 13.2708L1.36344 13.0293L7.80478 2.72252C8.20305 2.08516 8.74731 1.55176 9.39257 1.16642C10.0378 0.781077 10.7655 0.554863 11.5155 0.50648L11.8202 0.497009H29.0118ZM29.0118 3.65376H11.8202C11.5867 3.65383 11.3562 3.70569 11.1452 3.80558C10.9343 3.90547 10.7481 4.05092 10.6001 4.23144L10.4817 4.39559L4.04194 14.7024L10.4817 25.0091C10.6054 25.2072 10.7715 25.3753 10.968 25.5014C11.1645 25.6275 11.3866 25.7084 11.6181 25.7384L11.8202 25.751H29.0118C29.3984 25.7509 29.7715 25.609 30.0604 25.3521C30.3493 25.0952 30.5339 24.7412 30.5791 24.3573L30.5902 24.1726V5.23213C30.5901 4.84553 30.4482 4.4724 30.1913 4.1835C29.9344 3.8946 29.5804 3.71004 29.1965 3.6648L29.0118 3.65376ZM16.1938 9.12124L19.54 12.4705L22.8893 9.12124C23.0359 8.9747 23.21 8.85847 23.4016 8.7792C23.5931 8.69993 23.7984 8.65917 24.0058 8.65925C24.2131 8.65932 24.4184 8.70023 24.6099 8.77963C24.8014 8.85904 24.9754 8.97538 25.1219 9.12203C25.2684 9.26868 25.3847 9.44275 25.4639 9.63431C25.5432 9.82588 25.584 10.0312 25.5839 10.2385C25.5838 10.4458 25.5429 10.6511 25.4635 10.8426C25.3841 11.0341 25.2678 11.2081 25.1211 11.3546L21.775 14.7024L25.1211 18.0501C25.4173 18.3461 25.5837 18.7475 25.5839 19.1662C25.584 19.5849 25.4179 19.9865 25.1219 20.2827C24.8259 20.5789 24.4245 20.7453 24.0058 20.7455C23.5871 20.7456 23.1855 20.5795 22.8893 20.2835L19.5416 16.9342L16.1938 20.2835C15.8977 20.5797 15.496 20.746 15.0771 20.746C14.6583 20.746 14.2566 20.5797 13.9604 20.2835C13.6643 19.9873 13.4979 19.5856 13.4979 19.1668C13.4979 18.748 13.6643 18.3463 13.9604 18.0501L17.3097 14.7024L13.9604 11.3546C13.6643 11.0585 13.4979 10.6568 13.4979 10.2379C13.4979 9.8191 13.6643 9.41741 13.9604 9.12124C14.2566 8.82507 14.6583 8.65869 15.0771 8.65869C15.496 8.65869 15.8977 8.82507 16.1938 9.12124Z"
							fill="black"
						/>
					</svg>
				</button>
			</div>


			<!-- Search Bar Section -->
			<div class="search-section">
				<div class="search-input-container">
					
					<input
						type="text"
						placeholder="Search for coffee, pastries, meals..."
						class="search-input"
						on:input={handleSearchInput}
						bind:value={searchTerm}
					/>
				</div>
			</div>

			<div class="modal-body">
				{#if searchTerm.trim() === ''}
					<div class="search-prompt">
						<h3 class="search-prompt-title">Search our menu</h3>
						<p class="search-prompt-text">Start typing to find your favorite items...</p>
					</div>
				{:else if filteredItems.length > 0}
					<div class="search-results">
						<p class="results-count">
							{filteredItems.length} result{filteredItems.length === 1 ? '' : 's'} found for '{searchTerm}'
						</p>
						<div class="menu-items">
							{#each filteredItems as item}
								<div class="menu-item">
									<img
										src={item.image_path
											? `http://localhost/mabini-cafe/phpbackend/${item.image_path.replace(/^\/?/, '')}`
											: '/items/icon.svg'}
										alt={item.name}
										class="item-image"
									/>
									<div class="item-details">
										<h3 class="item-name">{item.name}</h3>
										<p class="item-description">{item.description || ''}</p>
										<p class="item-category">{item.category_name}</p>
										<p class="item-price">₱{parseFloat(item.price).toFixed(2)}</p>
									</div>
									<div class="item-actions">
										<button class="add-btn" on:click={() => handleAddToCart(item)}>
											Add to Cart
										</button>
									</div>
								</div>
							{/each}
						</div>
					</div>
				{:else}
					<div class="no-results">
						<div class="no-results-icon">:c</div>
						<h3 class="no-results-title">No items found</h3>
						<p class="no-results-text">Try searching with different keywords</p>
						<button
							class="browse-menu-btn"
							on:click={() => {
								onClose();
								goto('/menu');
							}}
						>
							Browse Full Menu
						</button>
					</div>
				{/if}
			</div>
		</div>
	</div>
{/if}

<style>
	.modal-overlay {
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: auto;
		justify-content: flex-end;
		display: flex;
		align-items: center;
		z-index: 1000;
	}

	.modal-content {
		background: white;
		border-radius: 1.5rem 0 0 1.5rem;
		max-width: 800px;
		width: 100%;
		max-height: 90vh;
		display: flex;
		flex-direction: column;
		box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
	}

	.modal-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 1.5rem 2rem;
        padding-bottom: 0;
		position: relative;
	}

	.modal-title {
		font-size: 1.75rem;
		font-weight: 900;
		color: var(--color-mabini-dark-brown);
		margin: 0;
	}

	.close-btn {
		background: none;
		border: none;
		font-size: 2rem;
		color: #666;
		cursor: pointer;
		padding: 0;
		width: 40px;
		height: 40px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
		transition: all 0.3s;
	}

	.close-btn:hover {
		background-color: #f0f0f0;
		color: var(--color-mabini-dark-brown);
	}

	.search-section {
		padding: 1rem;
		background-color: #f9f9f9;
		border-bottom: 1px solid #e0e0e0;
	}

	.search-input-container {
		position: relative;
		display: flex;
		align-items: center;
	}

	.search-icon {
		position: absolute;
		left: 1rem;
		color: #666;
		z-index: 1;
	}

	.search-input {
		width: 100%;
		padding: 1rem;
		border: 2px solid #e0e0e0;
		border-radius: 15px;
		font-size: 1rem;
		transition: all 0.3s;
		background-color: white;
	}

	.search-input:focus {
		outline: none;
		border-color: var(--color-mabini-dark-brown);
		box-shadow: 0 0 0 3px rgba(42, 28, 15, 0.1);
	}

	.modal-body {
		flex: 1;
		overflow-y: auto;
		padding: 1.5rem 2rem;
	}

	.search-prompt {
		text-align: center;
		padding: 3rem 1rem;
	}

	

	.search-prompt-title {
		font-size: 1.5rem;
		font-weight: 600;
		color: var(--color-mabini-dark-brown);
		margin-bottom: 0.5rem;
	}

	.search-prompt-text {
		color: #666;
		font-size: 1rem;
	}

	.results-count {
		font-size: 0.9rem;
		color: #666;
		margin-bottom: 1rem;
		font-weight: 500;
	}

	.menu-items {
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}

	.menu-item {
		display: grid;
		grid-template-columns: 80px 1fr auto;
		gap: 1rem;
		align-items: center;
		padding: 1rem;
		
		transition: box-shadow 0.3s;
	}

    .menu-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background-color: #ECECEC;

    }

	.item-image {
		width: 80px;
		height: 80px;
		object-fit: cover;
		border-radius: 0.5rem;
	}

	.item-details {
		display: flex;
		flex-direction: column;
		gap: 0.25rem;
	}

	.item-name {
		font-size: 1.1rem;
		font-weight: 700;
		color: var(--color-mabini-black);
		margin: 0;
	}

	.item-description {
		font-size: 0.85rem;
		color: #666;
		margin: 0;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		max-width: 300px;
	}

	.item-category {
		font-size: 0.75rem;
		color: var(--color-mabini-dark-brown);
		font-weight: 500;
		text-transform: uppercase;
		margin: 0;
	}

	.item-price {
		font-size: 1rem;
		font-weight: 700;
		color: var(--color-mabini-dark-brown);
		margin: 0;
	}

	.item-actions {
		display: flex;
		flex-direction: column;
		gap: 0.5rem;
		min-width: 100px;
	}

	/* .view-btn, */
	.add-btn {
		padding: 0.5rem 1rem;
		border: none;
		border-radius: 50px;
		font-size: 0.85rem;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s;
		text-align: center;
	}

	/* .view-btn {
		background-color: transparent;
		color: var(--color-mabini-dark-brown);
		border: 2px solid var(--color-mabini-dark-brown);
	}

	.view-btn:hover {
		background-color: var(--color-mabini-dark-brown);
		color: white;
	} */

	.add-btn {
		background-color: var(--color-mabini-dark-brown);
		color: white;
	}

	.add-btn:hover {
		background-color: var(--color-mabini-yellow);
		color: var(--color-mabini-dark-brown);
	}

	.no-results {
		text-align: center;
		padding: 3rem 1rem;
	}

	.no-results-icon {
		font-size: 3rem;
		margin-bottom: 1rem;
	}

	.no-results-title {
		font-size: 1.5rem;
		font-weight: 600;
		color: var(--color-mabini-dark-brown);
		margin-bottom: 0.5rem;
	}

	.no-results-text {
		color: #666;
		margin-bottom: 1.5rem;
	}

	.browse-menu-btn {
		padding: 0.75rem 2rem;
		background-color: var(--color-mabini-dark-brown);
		color: white;
		border: none;
		border-radius: 50px;
		font-size: 1rem;
		font-weight: 700;
		text-transform: uppercase;
		cursor: pointer;
		transition: all 0.3s;
	}

	.browse-menu-btn:hover {
		background-color: var(--color-mabini-yellow);
		color: var(--color-mabini-dark-brown);
	}

	/* Mobile Responsive */
	@media (max-width: 768px) {
		.menu-item {
			grid-template-columns: 60px 1fr;
			gap: 0.75rem;
		}

		.item-image {
			width: 60px;
			height: 60px;
			grid-row: 1 / 3;
		}

		.item-actions {
			grid-column: 2;
			flex-direction: row;
		}

		.item-description {
			max-width: 200px;
		}
	}
</style>
