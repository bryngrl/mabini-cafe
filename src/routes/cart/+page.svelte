<!-- Cart page -->
<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import { cartStore, cartItems, cartTotal, cartCount } from '$lib/stores/cart';
	import { authStore } from '$lib/stores/auth';
	import { showConfirm, showSuccess, showError, showLoginRequired } from '$lib/utils/sweetalert';

	let isLoading = true;
	let hasUnavailableItems = false;

	// Reactive statement to check for unavailable items
	$: hasUnavailableItems = $cartItems.some(
		(item) => item.menu_item_isAvailable === 0 || item.menu_item_isAvailable === '0' || item.menu_item_isAvailable === false
	);

	onMount(async () => {
		authStore.init();
		setTimeout(async () => {
			if ($authStore.isAuthenticated && $authStore.user?.id) {
				try {
					await cartStore.fetchByCustomerId($authStore.user.id);
				} catch (err) {
					console.error('Error loading cart:', err);
					showError('Failed to load cart', 'Error');
				}
			} else {
				showLoginRequired();
				const result = await showLoginRequired();
				if (result.isConfirmed) {
					goto('/login');
				} else {
					goto('/');
				}
			}
			isLoading = false;
		}, 100);
	});
	async function removeFromCart(itemId: number, itemName: string) {
		const result = await showConfirm(
			`Are you sure you want to remove ${itemName} from your cart?`,
			'Remove Item'
		);

		if (result.isConfirmed) {
			try {
				await cartStore.remove(itemId, $authStore.user?.id);
				await showSuccess('Item removed from cart', 'Removed');
			} catch (err: any) {
				await showError(err.message || 'Failed to remove item', 'Error');
				console.error('Error removing item from cart:', err);
			}
		}
	}

	async function updateQuantity(cartId: number, newQuantity: number, price: number) {
		if (newQuantity < 1) return;

		try {
			const newSubtotal = price * newQuantity;
			await cartStore.updateItem(cartId, newQuantity, newSubtotal, $authStore.user?.id);
		} catch (err: any) {
			await showError(err.message || 'Failed to update quantity', 'Error');
			console.error('Error updating quantity:', err);
		}
	}

	function checkout() {
		if ($cartItems.length === 0) {
			showError('Your cart is empty', 'Cannot Checkout');
			return;
		}

		if (hasUnavailableItems) {
			showError(
				'Some items in your cart are no longer available. Please remove them before checking out.',
				'Cannot Checkout'
			);
			return;
		}

		goto('/checkout');
	}
</script>

<svelte:head>
	<title>Cart - Mabini Cafe</title>
	<meta name="description" content="Manage your cart at Mabini Cafe" />
</svelte:head>

<div class="flex flex-col lg:flex-row min-h-[100vh]">
	{#if isLoading}
		<div class="flex-1 bg-white text-black flex items-center justify-center">
			<div class="text-center">
				<div
					class="animate-spin rounded-full h-12 w-12 border-b-2 border-mabini-brown mx-auto mb-4"
				></div>
				<p>Loading your cart...</p>
			</div>
		</div>
	{:else}
		<div class="flex-1 bg-white text-black flex p-4 sm:p-8 lg:p-20">
			<div class="w-full">
				<h2 class="text-xl sm:text-2xl font-bold mb-4 text-left">YOUR CART</h2>
				<!-- formssssszzz -->
				<div>
					{#if $cartItems.length === 0}
						<p>Your cart is empty.</p>
					{:else}
						{#each $cartItems as item}
							{@const isAvailable = item.menu_item_isAvailable === 1 || item.menu_item_isAvailable === '1' || item.menu_item_isAvailable === true}
							<div 
								class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-10 mb-4 pb-4 border-b sm:border-b-0"
								class:unavailable-item={!isAvailable}
							>
								<img
									src={item.menu_item_image
										? `https://mabini-cafe.bscs3a.com/phpbackend/${item.menu_item_image.replace(/^\/?/, '')}`
										: '/images/placeholder.png'}
									alt={item.menu_item_name}
									class="w-20 h-20 sm:w-24 sm:h-24 object-cover self-center sm:self-start"
									class:grayscale-img={!isAvailable}
								/>

								<div class="flex-1 flex flex-col sm:flex-row justify-between items-start w-full gap-2">
									<div class="flex items-start justify-between w-full sm:w-auto">
										<div class="flex-1 flex flex-col justify-between items-start">
											<h3 
												class="text-base sm:text-lg font-bold min-h-[2rem] sm:min-h-[2.5rem] flex items-start self-start"
												class:line-through-text={!isAvailable}
											>
												{item.menu_item_name}
											</h3>
											{#if !isAvailable}
												<span class="text-xs text-red-600 font-semibold bg-red-100 px-2 py-1 rounded mb-2">
													Currently Unavailable
												</span>
											{/if}
											<div class="box flex items-center gap-2 sm:gap-4 my-2 sm:my-4 bg-gray-50 min-w-[100px] sm:min-w-[120px]">
												<button
													class="minus bg-white px-2 sm:px-3 py-1 text-gray-700 font-bold text-sm"
													on:click={() =>
														updateQuantity(item.id, item.quantity - 1, item.menu_item_price)}
													disabled={item.quantity <= 1 || !isAvailable}>-</button
												>
												<span class="text-base sm:text-lg font-bold w-6 sm:w-8 text-center">{item.quantity}</span>
												<button
													class="plus px-2 sm:px-3 py-1 bg-white text-gray-700 font-bold text-sm"
													on:click={() =>
														updateQuantity(item.id, item.quantity + 1, item.menu_item_price)}
													disabled={!isAvailable}
													>+</button
												>
											</div>
										</div>
									</div>
									<div class="flex sm:flex-col justify-between items-center sm:items-end w-full sm:w-auto gap-4 sm:gap-0 sm:h-24 sm:ml-3 sm:mt-6 sm:mb-8">
										<p 
											class="text-base sm:text-lg font-bold sm:min-h-[2.5rem] flex items-start"
											class:line-through-text={!isAvailable}
										>
											₱{(typeof item.subtotal === 'number' && !isNaN(item.subtotal)
												? item.subtotal
												: item.menu_item_price * (item.quantity || 1) || 0
											).toFixed(2)}
										</p>
										<button
											class="remove-btn"
											on:click={() => removeFromCart(item.id, item.menu_item_name)}
											aria-label="Remove from cart"
										>
											<img
												src="/items/icon.svg"
												alt="Remove"
												width="15"
												height="15"
												style="display: inline-block;"
											/>
										</button>
									</div>
								</div>
							</div>
						{/each}
					{/if}
				</div>
			</div>
		</div>

		<div class="flex-1 bg-white text-black flex p-4 sm:p-8 lg:p-15">
			<div class="bg-[#666666] rounded-xl p-6 sm:p-10 w-full h-fit text-white">
				<div class="flex items-center justify-between w-full">
					<h2 class="text-lg sm:text-[24px]">Subtotal</h2>
					<p class="text-base sm:text-lg font-[900] pl-4 sm:pl-20">₱{$cartTotal.toFixed(2)}</p>
				</div>
				<p class="text-sm sm:text-normal pt-3 sm:pt-5 pb-3 sm:pb-5">* shipping calculated at checkout.</p>
				{#if hasUnavailableItems}
					<p class="text-sm text-red-400 pb-3">
						⚠️ Please remove unavailable items to proceed with checkout
					</p>
				{/if}
				<button
					class="cursor-pointer w-full bg-mabini-dark-brown font-bold rounded-full py-2 px-6 sm:px-10 text-sm sm:text-base tracking-wide text-center"
					class:opacity-50={$cartItems.length === 0 || hasUnavailableItems}
					class:cursor-not-allowed={$cartItems.length === 0 || hasUnavailableItems}
					disabled={$cartItems.length === 0 || hasUnavailableItems}
					on:click={checkout}
				>
					{#if $cartItems.length === 0}
						<span class="block text-center text-white font-semibold" aria-live="polite">
							<a href="/menu">Browse Menu</a>
						</span>
					{:else}
						Checkout - ₱{$cartTotal.toFixed(2)}
					{/if}
				</button>
			</div>
		</div>
	{/if}
</div>

<style>
	.box {
		border: 1px solid black;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
	}
	.plus {
		border-left: 1px solid black;
		border-radius: 0 4px 4px 0;
	}
	.minus {
		border-right: 1px solid black;
		border-radius: 4px 0 0 4px;
	}
	.remove-btn {
		background: none;
		border: none;
		font-size: 1rem;
		cursor: pointer;
		padding: 0.5rem;
		border-radius: 0.5rem;
		transition: all 0.3s;
	}

	.remove-btn:hover {
		background-color: #fee;
		transform: scale(1.1);
	}

	.unavailable-item {
		opacity: 0.6;
		background-color: #f9f9f9;
		padding: 1rem;
		border-radius: 0.5rem;
	}

	.grayscale-img {
		filter: grayscale(70%);
	}

	.line-through-text {
		text-decoration: line-through;
		color: #999;
	}

	button:disabled {
		cursor: not-allowed;
		opacity: 0.5;
	}
</style>
