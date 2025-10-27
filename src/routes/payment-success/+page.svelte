<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import { cartStore, cartItems } from '$lib/stores/cart';
	import { authStore } from '$lib/stores/auth';
	import { showSuccess } from '$lib/utils/sweetalert';
	import { orderNoteStore } from '$lib/stores/orderNote';

	let isClearing = true;

	onMount(async () => {
		authStore.init();

		const pendingOrderId = localStorage.getItem('pending_order_id');

		if (pendingOrderId && $authStore.user?.id) {
			try {
				for (const item of $cartItems) {
					await cartStore.remove(item.id);
				}

				localStorage.removeItem('pending_order_id');

				orderNoteStore.clear();

				await showSuccess(
					'Your payment was successful! Your order is being processed.',
					'Payment Successful'
				);
			} catch (error) {
				console.error('Error clearing cart:', error);
			}
		}

		isClearing = false;

		// Redirect to orders page after 2 seconds
		setTimeout(() => {
			goto('/account/settings');
		}, 2000);
	});
</script>

<svelte:head>
	<title>Payment Success - Mabini Cafe</title>
</svelte:head>

<div class="success-page">
	<div class="success-container">
		{#if isClearing}
			<div class="loading">
				<div class="spinner"></div>
				<p>Processing your payment...</p>
			</div>
		{:else}
			<div class="success-content">
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
				<h1>Payment Successful!</h1>
				<p>
					Thank you for your order. To view your order status click Orders
				</p>
			</div>
		{/if}
	</div>
</div>

<style>
	.success-page {
		min-height: 100vh;
		display: flex;
		align-items: center;
		justify-content: center;
		background: #f5f5f5;
		padding: 2rem;
	}

	.success-container {
		background: white;
		border-radius: 20px;
		padding: 3rem;
		max-width: 500px;
		width: 100%;
		text-align: center;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
	}

	.loading {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 1.5rem;
	}

	.spinner {
		width: 60px;
		height: 60px;
		border: 4px solid #f3f3f3;
		border-top: 4px solid #667eea;
		border-radius: 50%;
		animation: spin 1s linear infinite;
	}

	@keyframes spin {
		0% {
			transform: rotate(0deg);
		}
		100% {
			transform: rotate(360deg);
		}
	}

	.success-content {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 1.5rem;
	}

	.success-icon {
		width: 80px;
		height: 80px;
		background: #4caf50;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		animation: scaleIn 0.5s ease-out;
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
		width: 50px;
		height: 50px;
		color: white;
	}

	h1 {
		font-size: 2rem;
		font-weight: bold;
		color: #333;
		margin: 0;
	}

	p {
		font-size: 1.1rem;
		color: #666;
		margin: 0;
	}

	@media (max-width: 640px) {
		.success-container {
			padding: 2rem;
		}

		h1 {
			font-size: 1.5rem;
		}

		p {
			font-size: 1rem;
		}
	}
</style>
