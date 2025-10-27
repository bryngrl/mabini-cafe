<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import { showError } from '$lib/utils/sweetalert';

	onMount(async () => {
		// Remove pending order from localStorage (payment was cancelled)
		localStorage.removeItem('pending_order_id');

		await showError(
			'Your payment was cancelled. You can try again when ready.',
			'Payment Cancelled'
		);

		// Redirect to cart after 2 seconds
		setTimeout(() => {
			goto('/cart');
		}, 2000);
	});
</script>

<svelte:head>
	<title>Payment Cancelled - Mabini Cafe</title>
</svelte:head>

<div class="cancelled-page">
	<div class="cancelled-container">
		<div class="cancelled-icon">
			<svg
				xmlns="http://www.w3.org/2000/svg"
				fill="none"
				viewBox="0 0 24 24"
				stroke-width="2"
				stroke="currentColor"
				class="x-mark"
			>
				<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
			</svg>
		</div>
		<h1>Payment Cancelled</h1>
		<p>Your payment was cancelled. Redirecting you back to your cart...</p>
	</div>
</div>

<style>
	.cancelled-page {
		min-height: 100vh;
		display: flex;
		align-items: center;
		justify-content: center;
		background: #f5f5f5;
		padding: 2rem;
	}

	.cancelled-container {
		background: white;
		border-radius: 20px;
		padding: 3rem;
		max-width: 500px;
		width: 100%;
		text-align: center;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 1.5rem;
	}

	.cancelled-icon {
		width: 80px;
		height: 80px;
		background: #f44336;
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

	.x-mark {
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
		.cancelled-container {
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
