<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import { cartStore, cartItems, cartTotal, cartCount } from '$lib/stores/cart';
	import { authStore } from '$lib/stores/auth';
	import { showConfirm, showSuccess, showError, showLoginRequired } from '$lib/utils/sweetalert';
	import { browser } from '$app/environment';
	import { orderNoteStore } from '$lib/stores/orderNote';

	export let isOpen = false;
	export let onClose: () => void;
	let orderNote = '';
	let hasUnavailableItems = false;

	// Subscribe to order note store
	orderNoteStore.subscribe((note) => {
		orderNote = note;
	});

	// Save note to store when it changes
	$: if (browser) {
		orderNoteStore.setNote(orderNote);
	}

	// Reactive statement to check for unavailable items
	$: hasUnavailableItems = $cartItems.some(
		(item) =>
			item.menu_item_isAvailable === 0 ||
			item.menu_item_isAvailable === '0' ||
			item.menu_item_isAvailable === false
	);

	$: {
		if (browser && isOpen) {
			document.body.style.overflow = 'hidden';
			if ($authStore.isAuthenticated && $authStore.user?.id) {
				cartStore.fetchByCustomerId($authStore.user.id).catch((err) => {
					console.error('Error loading cart:', err);
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

		onClose();
		goto('/checkout');
	}
</script>

{#if isOpen}
	<div class="modal-overlay" on:click={onClose}>
		<div class="modal-content" on:click|stopPropagation>
			<div class="modal-header">
				<h2 class="modal-title">Cart ({$cartCount} items)</h2>
				<button class="close-btn" on:click={onClose}
					><svg
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
					</svg></button
				>
			</div>

			<hr class="ml-8" />

			<div class="modal-body">
				{#if $cartStore.loading}
					<div class="loading-state">
						<p>Loading cart...</p>
					</div>
				{:else if $cartItems.length > 0}
					<div class="cart-items">
						{#each $cartItems as item}
							{@const isAvailable =
								item.menu_item_isAvailable === 1 ||
								item.menu_item_isAvailable === '1' ||
								item.menu_item_isAvailable === true}
							<div class="cart-item" class:unavailable-item={!isAvailable}>
								<img
									src={item.menu_item_image
										? `https://mabini-cafe.bscs3a.com/phpbackend/${item.menu_item_image.replace(/^\/?/, '')}`
										: '/images/placeholder.png'}
									alt={item.menu_item_name}
									class="item-image"
									class:grayscale-img={!isAvailable}
								/>
								<div class="item-details">
									<h3 class="item-name" class:line-through-text={!isAvailable}>
										{item.menu_item_name}
									</h3>
									{#if !isAvailable}
										<span class="unavailable-badge">Currently Unavailable</span>
									{/if}
								</div>
								<div class="box item-quantity">
									<button
										class="minus qty-btn"
										on:click={() =>
											updateQuantity(item.id, item.quantity - 1, item.menu_item_price)}
										disabled={item.quantity <= 1 || !isAvailable}
									>
										−
									</button>
									<span class="qty-value">{item.quantity}</span>
									<button
										class="plus qty-btn"
										on:click={() =>
											updateQuantity(item.id, item.quantity + 1, item.menu_item_price)}
										disabled={!isAvailable}
									>
										+
									</button>
								</div>
								<div class="item-subtotal">
									<p class="subtotal-label">Subtotal:</p>
									<p class="subtotal-value" class:line-through-text={!isAvailable}>
										₱{parseFloat(item.subtotal).toFixed(2)}
									</p>
								</div>
								<button
									class="remove-btn"
									on:click={() => removeFromCart(item.id, item.menu_item_name)}
									title="Remove item"
								>
									<img src="/items/icon.svg" alt="Remove" width="24" height="24" />
								</button>
							</div>
						{/each}
					</div>
					<div class="order-note-section">
						<span class="order-note">Add Order Note:</span>
						<textarea
							class="order-note"
							bind:value={orderNote}
							placeholder="Add a note to your order..."
							style="overflow: hidden;"
						></textarea>
					</div>
					<div class="cart-footer">
						<p class="taxes-shipping">TAXES AND SHIPPING CALCULATED AT CHECKOUT</p>
						{#if hasUnavailableItems}
							<p class="unavailable-warning">
								⚠️ Please remove unavailable items to proceed with checkout
							</p>
						{/if}
						<button
							class="checkout-btn"
							on:click={checkout}
							disabled={hasUnavailableItems}
							class:disabled-btn={hasUnavailableItems}
						>
							Checkout - ₱{$cartTotal.toFixed(2)}
						</button>
					</div>
				{:else}
					<div class="empty-cart">
						<p class="empty-message">Your cart is empty</p>
						<button
							class="browse-btn"
							on:click={() => {
								onClose();
								goto('/menu');
							}}
						>
							Browse Menu
						</button>
					</div>
				{/if}
			</div>
		</div>
	</div>
{/if}

<style>
	.box {
		/* Mobile (default) height: 105px */
		width: 105px;
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

	.taxes-shipping {
		font-size: 0.75rem;
		color: white;
		margin: auto;
		text-align: center;
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

	.modal-body {
		flex: 1;
		overflow-y: auto;
		padding: 1.5rem 2rem;
		/* min-height: 90vh; */
	}

	.cart-items {
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}

	.cart-item {
		display: grid;
		grid-template-columns: 80px 1fr auto auto auto;
		gap: 1rem;
		align-items: center;
		padding: 1rem;
		border-bottom: 1px solid #e0e0e0;
		/* border-radius: 1rem; */
		/* transition: box-shadow 0.3s; */
	}

	.cart-item:hover {
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

	.item-price {
		font-size: 0.9rem;
		color: #666;
		margin: 0;
	}

	.item-quantity {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		background-color: #f5f5f5;
		/* padding: 0.25rem; */
		/* border-radius: 2rem; */
	}

	.qty-btn {
		width: 32px;
		height: 32px;
		color: black;
		/* border: none; */
		/* background-color: var(--color-mabini-dark-brown); */
		/* color: white; */
		/* border-radius: 50%; */
		cursor: pointer;
		font-size: 1.2rem;
		font-weight: bold;
		display: flex;
		align-items: center;
		justify-content: center;
		/* transition: all 0.3s; */
	}

	.qty-btn:hover:not(:disabled) {
		/* background-color: var(--color-mabini-yellow); */
		color: var(--color-mabini-dark-black);
		/* transform: scale(1.1); */
	}

	.qty-btn:disabled {
		opacity: 0.5;
		cursor: not-allowed;
	}

	.qty-value {
		min-width: 30px;
		text-align: center;
		font-weight: 700;
		font-size: 1rem;
	}

	.item-subtotal {
		text-align: right;
		min-width: 100px;
	}

	.subtotal-label {
		font-size: 0.75rem;
		color: #666;
		margin: 0 0 0.25rem 0;
	}

	.subtotal-value {
		font-size: 1.1rem;
		font-weight: 700;
		color: var(--color-mabini-dark-brown);
		margin: 0;
	}

	.remove-btn {
		background: none;
		border: none;
		font-size: 1.5rem;
		cursor: pointer;
		padding: 0.5rem;
		border-radius: 0.5rem;
		transition: all 0.3s;
	}

	.remove-btn:hover {
		background-color: #fee;
		transform: scale(1.1);
	}

	.cart-footer {
		border-top: 2px solid #f0f0f0;
		padding: 1.5rem 2rem;
		background-color: black;
		height: 180px;
		border-radius: 0 0 0 1.5rem;
		margin-top: 0;
		margin-left: -2rem;
		margin-right: -2rem;
		margin-bottom: -1.5rem;
		display: flex;
		flex-direction: column;
		justify-content: flex-end;
	}

	.order-note-section {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 1rem 2rem;
		padding-right: 0px;
		height: 50px;
		margin-top: 1.5rem;
		margin-left: -2rem;
		margin-right: -2rem;
		background-color: #f9f9f9;
		border-radius: 0;
		box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.15);
	}
	.order-note-section textarea {
		flex: 1;
		padding: 0.75rem;
		border-radius: 0.5rem;
		font-size: 1rem;
		/* resize: none; */
		height: 50px;
		width: 100%;
	}

	.order-note {
		font-size: 1rem;
		font-weight: 700;
		color: var(--color-mabini-black);
	}

	.checkout-btn {
		width: 100%;
		padding: 1rem 2rem;
		background-color: var(--color-mabini-dark-brown);
		color: white;
		border: none;
		border-radius: 50px;
		font-size: 1.1rem;
		font-weight: 700;
		text-transform: uppercase;
		cursor: pointer;
		transition: all 0.3s;
		border: 2px solid var(--color-mabini-dark-brown);
		/* margin-top: auto; */
	}

	.checkout-btn:hover {
		background-color: transparent;
		border-color: var(--color-mabini-white);
		color: var(--color-mabini-white);
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(255, 174, 0, 0.3);
	}

	.checkout-btn:disabled,
	.disabled-btn {
		background-color: #999;
		border-color: #999;
		cursor: not-allowed;
		opacity: 0.6;
	}

	.checkout-btn:disabled:hover,
	.disabled-btn:hover {
		background-color: #999;
		border-color: #999;
		color: white;
		transform: none;
		box-shadow: none;
	}

	.unavailable-item {
		opacity: 0.7;
		background-color: #f9f9f9;
		border-left: 4px solid #f44336;
	}

	.grayscale-img {
		filter: grayscale(70%);
	}

	.line-through-text {
		text-decoration: line-through;
		color: #999;
	}

	.unavailable-badge {
		display: inline-block;
		font-size: 0.75rem;
		color: #d32f2f;
		font-weight: 600;
		background-color: #ffebee;
		padding: 0.25rem 0.5rem;
		border-radius: 0.25rem;
		margin-top: 0.25rem;
	}

	.unavailable-warning {
		font-size: 0.875rem;
		color: #d32f2f;
		background-color: #ffebee;
		padding: 0.5rem;
		border-radius: 0.5rem;
		margin: 0.5rem 0;
		text-align: center;
	}

	.qty-btn:disabled {
		cursor: not-allowed;
		opacity: 0.5;
		background-color: #f0f0f0;
	}

	.loading-state,
	.empty-cart {
		text-align: center;
		padding: 3rem 1rem;
	}

	.empty-message {
		font-size: 1.25rem;
		color: #666;
		margin-bottom: 1.5rem;
	}

	.browse-btn {
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

	.browse-btn:hover {
		background-color: var(--color-mabini-yellow);
		color: var(--color-mabini-dark-brown);
	}

	/* Mobile Responsive */
	@media (max-width: 768px) {
		.cart-modal {
			width: 100vw !important;
			max-width: 100vw !important;
			height: 100vh !important;
			border-radius: 0 !important;
			margin: 0 !important;
		}

		.cart-header {
			padding: 1rem !important;
		}

		.cart-header h2 {
			font-size: 1.25rem !important;
		}

		.cart-content {
			padding: 1rem !important;
		}

		.cart-item {
			grid-template-columns: 60px 1fr;
			gap: 0.75rem;
			position: relative;
		}

		.item-image {
			width: 60px;
			height: 60px;
			grid-row: 1 / 3;
		}

		.item-quantity {
			grid-column: 2;
		}

		.item-subtotal {
			grid-column: 2;
			text-align: left;
		}

		.remove-btn {
			position: absolute;
			top: 0.5rem;
			right: 0.5rem;
		}

		.quantity-controls button {
			width: 30px !important;
			height: 30px !important;
			font-size: 0.875rem !important;
		}

		.cart-footer {
			padding: 1rem !important;
		}

		.checkout-btn {
			padding: 0.75rem !important;
			font-size: 0.875rem !important;
		}

		/* Order note textarea */
		.cart-content textarea {
			font-size: 0.875rem !important;
			padding: 0.75rem !important;
		}
	}

	@media (max-width: 480px) {
		.cart-header h2 {
			font-size: 1.125rem !important;
		}

		.item-image {
			width: 50px;
			height: 50px;
		}

		.cart-item {
			grid-template-columns: 50px 1fr;
			gap: 0.5rem;
		}
	}
</style>
