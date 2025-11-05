<svelte:head>
	<title>Orders & Payment - Mabini Cafe</title>
	<meta name="description" content="Manage your orders and payments at Mabini Cafe" />
</svelte:head>

<div class="div-static-content">
	<div class="text-left">
		<h1 class="header-text !text-left">
			ORDERS & <span class="text-mabini-yellow">PAYMENT</span>
		</h1>
		<h2 class="header-2-text">WHAT SHIPPING TYPES YOU HAVE?</h2>
		<p class="content-paragraph">Our payment option only include GCash as of now.</p>

		<div class="flex justify-center items-center w-full">
			<img
				src="/images/payment-methods.svg"
				alt="Payment Methods"
				class="w-[600px] max-w-full md:w-[300px] lg:w-[400px] xl:w-[500px] h-auto mx-auto"
			/>
		</div>

		<h2 class="header-2-text">Can I cancel my order?</h2>
		<p class="content-paragraph">
			Order cancellations are not available once an order has been confirmed. For any concerns or
			<br class="mobile-hide-br" />

			assistance, please contact us at contact.mabinicafe@gmail.com
		</p>
	</div>
</div>

<style>
	.mobile-hide-br {
		display: inline;
	}
	@media (max-width: 600px) {
		.mobile-hide-br {
			display: none;
		}
	}
</style>
