<script lang="ts">
	import { goto } from '$app/navigation';
	import { cartItems, cartTotal, cartCount } from '$lib/stores/cart';
	import { createEventDispatcher } from 'svelte';

	export let isOpen = false;
	export let itemAdded = '';

	const dispatch = createEventDispatcher();

	function close() {
		dispatch('close');
	}

	function goToCart() {
		close();
		goto('/cart');
	}

	function continueShopping() {
		close();
	}
</script>

{#if isOpen}
	<div class="modal-overlay" on:click={close} on:keydown={(e) => e.key === 'Escape' && close()} role="button" tabindex="0">
		<div class="modal-content" on:click|stopPropagation on:keydown role="dialog" tabindex="-1">
			<button class="close-btn" on:click={close} aria-label="Close">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					fill="none"
					viewBox="0 0 24 24"
					stroke-width="2"
					stroke="currentColor"
					class="w-6 h-6"
				>
					<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>

			<div class="success-icon">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					fill="none"
					viewBox="0 0 24 24"
					stroke-width="2"
					stroke="currentColor"
					class="checkmark"
				>
					<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
				</svg>
			</div>

			<h2>Added to Cart!</h2>
			{#if itemAdded}
				<p class="item-name">{itemAdded} has been added to your cart</p>
			{/if}

			<div class="cart-summary">
				<div class="summary-row">
					<span>Items in cart:</span>
					<span class="value">{$cartCount}</span>
				</div>
				<div class="summary-row total">
					<span>Total:</span>
					<span class="value">₱{$cartTotal.toFixed(2)}</span>
				</div>
			</div>

			<div class="button-group">
				<button class="btn-secondary" on:click={continueShopping}>Continue Shopping</button>
				<button class="btn-primary" on:click={goToCart}>View Cart & Checkout</button>
			</div>
		</div>
	</div>
{/if}

<style>
	.modal-overlay {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: rgba(0, 0, 0, 0.6);
		display: flex;
		align-items: center;
		justify-content: center;
		z-index: 9999;
		animation: fadeIn 0.2s ease-out;
	}

	@keyframes fadeIn {
		from {
			opacity: 0;
		}
		to {
			opacity: 1;
		}
	}

	.modal-content {
		background: white;
		border-radius: 20px;
		padding: 2.5rem;
		max-width: 450px;
		width: 90%;
		position: relative;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
		animation: slideUp 0.3s ease-out;
		text-align: center;
	}

	@keyframes slideUp {
		from {
			transform: translateY(50px);
			opacity: 0;
		}
		to {
			transform: translateY(0);
			opacity: 1;
		}
	}

	.close-btn {
		position: absolute;
		top: 1rem;
		right: 1rem;
		background: transparent;
		border: none;
		cursor: pointer;
		padding: 0.5rem;
		color: #666;
		transition: color 0.2s;
	}

	.close-btn:hover {
		color: #000;
	}

	.success-icon {
		width: 70px;
		height: 70px;
		background: #4caf50;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto 1.5rem;
		animation: scaleIn 0.4s ease-out;
	}

	@keyframes scaleIn {
		0% {
			transform: scale(0);
		}
		50% {
			transform: scale(1.2);
		}
		100% {
			transform: scale(1);
		}
	}

	.checkmark {
		width: 40px;
		height: 40px;
		color: white;
	}

	h2 {
		font-size: 1.75rem;
		font-weight: bold;
		color: #333;
		margin-bottom: 0.5rem;
	}

	.item-name {
		color: #666;
		margin-bottom: 1.5rem;
		font-size: 1rem;
	}

	.cart-summary {
		background: #f5f5f5;
		border-radius: 12px;
		padding: 1.25rem;
		margin-bottom: 1.5rem;
	}

	.summary-row {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 0.5rem 0;
		font-size: 1rem;
		color: #555;
	}

	.summary-row.total {
		border-top: 2px solid #ddd;
		margin-top: 0.5rem;
		padding-top: 1rem;
		font-weight: bold;
		font-size: 1.125rem;
		color: #333;
	}

	.value {
		font-weight: 600;
		color: var(--color-mabini-dark-brown, #3e2723);
	}

	.button-group {
		display: flex;
		gap: 1rem;
		flex-direction: column;
	}

	.btn-primary,
	.btn-secondary {
		padding: 0.875rem 1.5rem;
		border-radius: 10px;
		font-weight: 600;
		font-size: 1rem;
		cursor: pointer;
		transition: all 0.2s;
		border: none;
		width: 100%;
	}

	.btn-primary {
		background: var(--color-mabini-dark-brown, #3e2723);
		color: white;
	}

	.btn-primary:hover {
		background: #2c1810;
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
	}

	.btn-secondary {
		background: white;
		color: var(--color-mabini-dark-brown, #3e2723);
		border: 2px solid var(--color-mabini-dark-brown, #3e2723);
	}

	.btn-secondary:hover {
		background: #f5f5f5;
	}

	@media (max-width: 640px) {
		.modal-content {
			padding: 2rem 1.5rem;
		}

		h2 {
			font-size: 1.5rem;
		}

		.button-group {
			gap: 0.75rem;
		}
	}
</style>
