<!-- Cart page -->
<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import { cartStore, cartItems, cartTotal, cartCount } from '$lib/stores/cart';
	import { authStore } from '$lib/stores/auth';
	import { showConfirm, showSuccess, showError, showLoginRequired } from '$lib/utils/sweetalert';

	let isLoading = true;

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
		goto('/checkout');
	}
</script>

<svelte:head>
	<title>Cart - Mabini Cafe</title>
	<meta name="description" content="Manage your cart at Mabini Cafe" />
</svelte:head>

<div class="flex min-h-[100vh]">
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
		<div class="flex-1 bg-white text-black flex p-20">
			<div>
				<h2 class="text-2xl font-bold mb-4 text-left">YOUR CART</h2>
				<!-- formssssszzz -->
				<div>
					{#if $cartItems.length === 0}
						<p>Your cart is empty.</p>
					{:else}
						{#each $cartItems as item}
							<div class="flex items-center gap-10 mb-4">
								<img
									src={item.menu_item_image
										? `http://localhost/mabini-cafe/phpbackend/${item.menu_item_image.replace(/^\/?/, '')}`
										: '/images/placeholder.png'}
									alt={item.menu_item_name}
									class="w-24 h-24 object-cover"
								/>

								<div class="flex-1 flex justify-between items-start">
									<div class="flex items-start justify-between">
										<div class="flex-1 flex flex-col justify-between items-start">
											<h3 class="text-lg font-bold min-h-[2.5rem] flex items-start self-start">
												{item.menu_item_name}
											</h3>
											<div class=" box flex items-center gap-4 my-4 bg-gray-50 min-w-[120px]">
												<button
													class="minus bg-white px-3 py-1 text-gray-700 font-bold text-sm"
													on:click={() =>
														updateQuantity(item.id, item.quantity - 1, item.menu_item_price)}
													disabled={item.quantity <= 1}>-</button
												>
												<span class="text-l font-bold w-8 text-center">{item.quantity}</span>
												<button
													class="plus px-3 py-1 bg-white text-gray-700 font-bold text-sm"
													on:click={() =>
														updateQuantity(item.id, item.quantity + 1, item.menu_item_price)}
													>+</button
												>
											</div>
										</div>
									</div>
									<div class="h-24 w-24 ml-3 mt-6 mb-8 self-center flex items-center">
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
									<!-- <div class="flex flex-col justify-between h-full items-start"> -->
										<p class="text-lg font-bold min-h-[2.5rem] flex items-start self-start">
											₱{(typeof item.subtotal === 'number' && !isNaN(item.subtotal)
												? item.subtotal
												: item.menu_item_price * (item.quantity || 1) || 0
											).toFixed(2)}
										</p>
									<!-- </div> -->
								</div>
							</div>
						{/each}
					{/if}
				</div>
			</div>
		</div>

		<div class="flex-1 bg-white text-black flex p-15">
			<div class="bg-[#666666] rounded-xl p-10 w-full h-fit text-white">
				<div class="flex items-center justify-between w-full">
					<h2 class="text-[24px]">Subtotal</h2>
					<p class="text-lg font-[900] pl-20">₱{$cartTotal.toFixed(2)}</p>
				</div>
				<p class="text-normal pt-5 pb-5">* shipping calculated at checkout.</p>
				<button
					class="cursor-pointer w-full bg-mabini-dark-brown font-bold rounded-full py-2 px-10 text-base tracking-wide text-center"
					disabled={$cartItems.length === 0}
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
</style>
