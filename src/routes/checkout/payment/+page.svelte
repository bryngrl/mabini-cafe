<!-- Form and Checkout -->
<!-- Verification in changing the email and phone number -->
<!-- Responsiveness  -->
<!-- Need to furnish more -->

<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import { cartStore, cartItems, cartTotal, cartCount } from '$lib/stores/cart';
	import { authStore } from '$lib/stores/auth';
	import { showError, showSuccess } from '$lib/utils/sweetalert';
	import { shippingStore, shippingInfo, shippingLoading, shippingError } from '$lib/stores';
	import { selectedShippingMethod, shippingCost, checkoutTotal } from '$lib/stores/checkout';
	import { ordersStore } from '$lib/stores/orders';
	import { orderItemsStore } from '$lib/stores/orderItems';
	import { createPaymongoCheckout } from '$lib/utils/fetcher';
	import { orderNoteStore } from '$lib/stores/orderNote';

	function redirectToShipping() {
		goto('/checkout/shipping');
	}
	
	let isProcessingPayment = false;

	async function redirectToPayMongo() {
		// Check if shipping method is selected
		if (!$selectedShippingMethod) {
			showError('Please select a shipping method', 'Shipping Method Required');
			goto('/checkout/shipping');
			return;
		}

		if (isProcessingPayment) return;
		isProcessingPayment = true;

		try {
			// Determine shipping_fee_id based on selected method
			// Standard = 1 (₱79), Priority = 2 (₱109)
			const shippingFeeId = $selectedShippingMethod === 'standard' ? 1 : 2;

			// First, create the order with the note from orderNoteStore
			const orderData = {
				user_id: $authStore.user?.id,
				total_amount: $cartTotal,
				shipping_fee_id: shippingFeeId,
				message: $orderNoteStore || ''
			};

			const orderResult = await ordersStore.create(orderData);
			const orderId = orderResult.order_id;

			if (!orderId) {
				throw new Error('Failed to create order');
			}

			// Second, create order items from cart
			for (const cartItem of $cartItems) {
				await orderItemsStore.create({
					order_id: orderId,
					menu_item_id: cartItem.menu_item_id,
					quantity: cartItem.quantity,
					price: cartItem.menu_item_price
				});
			}

			// Third, create PayMongo checkout session
			const paymentData = {
				user_id: $authStore.user?.id,
				order_id: orderId,
				payment_method: 'gcash'
			};

			const checkoutResult = await createPaymongoCheckout(paymentData);

			if (checkoutResult.checkout_url) {
				// DO NOT clear cart here - it will be cleared by webhook when payment is successful
				// Store order_id in localStorage so we can clear cart after successful payment
				localStorage.setItem('pending_order_id', orderId.toString());
				
				// Redirect to PayMongo checkout page
				window.location.href = checkoutResult.checkout_url;
			} else {
				throw new Error('No checkout URL received');
			}
		} catch (error) {
			console.error('Error creating checkout:', error);
			await showError(
				error.message || 'Failed to create checkout session. Please try again.',
				'Payment Error'
			);
			isProcessingPayment = false;
		}
	}

	let userAddresses = [];
	let selectedAddress = null;
	let showAddressSelector = false;

	function selectAddress(address) {
		selectedAddress = address;
		existingInfo = {
			email: address.email,
			fname: address.first_name,
			lname: address.last_name,
			address: address.address,
			apartment: address.apartment_suite_etc || '',
			postalcode: address.postal_code,
			city: address.city,
			country: address.region,
			phone: address.phone
		};
		formData = { ...existingInfo };
		showAddressSelector = false;
	}
	let existingInfo = {
		email: '',
		fname: '',
		lname: '',
		address: '',
		apartment: '',
		postalcode: '',
		city: '',
		country: '',
		phone: ''
	};

	let formData = {
		email: '',
		fname: '',
		lname: '',
		address: '',
		apartment: '',
		postalcode: '',
		city: '',
		country: '',
		phone: ''
	};
	let hasExistingShipping = false;
	let isEditMode = false;
	let isAddress = false;
	let isContact = false;
	let isShippingMethod = false;

	function cancelEditAddress() {
		formData = { ...existingInfo };
		isAddress = false;
	}
	function cancelEditContact() {
		formData = { ...existingInfo };
		isContact = false;
	}
	function enableAddress() {
		isAddress = true;
	}
	function enableContact() {
		isContact = true;
	}
	function enableShippingMethod() {
		isShippingMethod = true;
	}
	function cancelShippingMethod() {
		formData = { ...existingInfo };
		isShippingMethod = false;
	}

	// Get the cart items and check if the user is logged in or not
	onMount(async () => {
		authStore.init();
		if (!$authStore.isAuthenticated) {
			await showError('Please login to continue', 'Authentication Required');
			goto('/login');
			return;
		}

		try {
			await cartStore.fetchByCustomerId($authStore.user?.id);

			if ($cartItems.length === 0) {
				await showError('Your cart is empty', 'Cannot Checkout');
				goto('/menu');
			}
		} catch (err: any) {
			console.error('Error loading cart:', err);
			await showError('Failed to load cart', 'Error');
		}

		if ($authStore.user?.id) {
			try {
				await shippingStore.fetchByUserId($authStore.user.id);
				userAddresses = $shippingInfo || [];

				if (userAddresses.length > 0) {
					const defaultAddress = userAddresses[0];
					selectAddress(defaultAddress);
					hasExistingShipping = true;
				}
			} catch (error) {
				console.error('Error fetching shipping info:', error);
			}
		} else {
			console.log('No auth user found');
		}
	});

	async function handleUpdateButton() {
		const shippingData = {
			user_id: $authStore.user?.id,
			email: formData.email,
			first_name: formData.fname,
			last_name: formData.lname,
			address: formData.address,
			apartment_suite_etc: formData.apartment,
			postal_code: formData.postalcode,
			city: formData.city,
			region: formData.country,
			phone: formData.phone
		};

		try {
			await shippingStore.updateAddress($authStore.user?.id, shippingData);
			await showSuccess('Shipping information updated successfully', 'Success');
			existingInfo = { ...formData };
			isEditMode = false;
		} catch (error) {
			console.error('Error updating shipping info:', error);
			await showError('Failed to update shipping information', 'Error');
		}
	}

	async function handleSubmit(event) {
		event.preventDefault();
		const shippingData = {
			user_id: $authStore.user?.id,
			email: formData.email,
			first_name: formData.fname,
			last_name: formData.lname,
			address: formData.address,
			apartment_suite_etc: formData.apartment,
			postal_code: formData.postalcode,
			city: formData.city,
			region: formData.country,
			phone: formData.phone
		};
		try {
			await shippingStore.store(shippingData);
			await showSuccess('Shipping information saved successfully!');
			goto('/payment');
		} catch (error) {
			console.error('Failed to save shipping info:', error);
			await showError('Failed to save shipping information', 'Error');
		}
	}
</script>

<svelte:head>
	<title>Order - Mabini Cafe</title>
	<meta name="description" content="Place your order at Mabini Cafe" />
</svelte:head>
<!-- Head -->
<div class="flex flex-col lg:flex-row gap-[2px] min-h-screen content-center">
	<!-- Left Side -->
	<div class="grow w-2/4 bg-black p-4 sm:p-6 lg:p-10 text-white justify-center">
		<img src="/images/LOGO-4.png" alt="Logo" class="mb-2 w-[238px] h-[68px] m-auto" />
		<nav class="flex mb-10 text-sm m-auto font-semibold gap-4 items-center justify-center">
			<a href="/cart" class="text-gray-400 hover:text-white transition px-2"> Cart</a>
			<a href="/checkout" class=" text-gray-400 hover:text-white transition px-2">Information</a>
			<a href="/checkout/shipping" class="text-gray-400 hover:text-white transition px-2"
				>Shipping</a
			>
			<a href="/checkout/payment" class="text-white px-2">Payment</a>
		</nav>
		<!-- Field Form -->

		{#if !isShippingMethod && !isAddress && !isContact && !showAddressSelector}
			<div
				class="flex-1 flex-col text-white flex justify-center items-left bg-mabini-white shadow-md rounded-xl"
			>
				<div class="w-full p-6 pb-0 pt-2 pl-10 flex items-center justify-between space-x-4">
					<div class="flex items-center space-x-4">
						<p class="text-gray-600 mb-0">Ship to</p>
						<p class="text-l p-2 mb-0 text-black">
							{existingInfo.address}, {existingInfo.city}, {existingInfo.postalcode}
						</p>
					</div>
					<div class="flex gap-2">
						{#if userAddresses.length > 1}
							<button
								on:click={() => (showAddressSelector = true)}
								class="cursor-pointer text-mabini-dark-brown underline text-sm hover:text-mabini-yellow"
							>
								Change
							</button>
						{:else}
							<button
								on:click={enableAddress}
								class="cursor-pointer text-mabini-dark-brown underline text-sm hover:text-mabini-yellow"
							>
								Change
							</button>
						{/if}
					</div>
				</div>

				<hr class="border-gray-400 ml-8 mr-8 border-1" />

				<div class="w-full p-6 pt-0 pl-10 pb-2 flex items-center justify-between space-x-4">
					<div class="flex items-center space-x-2">
						<p class="text-gray-600 mb-0">Contact</p>
						<p class="text-l p-2 mb-0 text-black">{existingInfo.email}</p>
					</div>
					<button
						on:click={enableContact}
						class="cursor-pointer text-mabini-dark-brown underline text-sm hover:text-mabini-yellow"
					>
						Change
					</button>
				</div>
				<hr class="border-gray-400 ml-8 mr-8 border-1" />

				<div class="w-full p-6 pt-0 pl-10 pb-2 flex items-center justify-between space-x-4">
					<div class="flex items-center space-x-2">
						<p class="text-gray-600 mb-0">Shipping Method</p>
						<p class="text-l p-2 mb-0 text-black">
							{#if $selectedShippingMethod === 'standard'}
								Standard Delivery
							{:else if $selectedShippingMethod === 'priority'}
								Priority Delivery
							{:else}
								No shipping method selected
							{/if}
						</p>
					</div>
					<button
						on:click={enableShippingMethod}
						class="cursor-pointer text-mabini-dark-brown underline text-sm hover:text-mabini-yellow"
					>
						Change
					</button>
				</div>
			</div>

			<h2 class="font-bold text-xl pt-10">Payment</h2>
			<h3 class="font-light text-sm">All transactions are secure and encrypted.</h3>
			<div
				class="flex gap-0 flex-col items-start flex-nowrap justify-start content-center pt-10 text-gray-500"
			>
				<div class="w-full h-[50px] bg-white rounded-t-xl text-left">
					<h2 class="p-2 pt-4 pl-10 text-black">Secure Payments via PayMongo</h2>
				</div>
				<div class="w-full h-[250px] bg-gray-200 rounded-b-xl text-center">
					<img
						src="/items/money.svg"
						alt="PayMongo Logo"
						class="w-40 h-40 object-contain m-auto pt-7"
					/>
					<h2 class="pt-0 p-25">
						After clicking “Pay now”, you will be redirected to Secure Payments via PayMongo to
						complete your purchase securely.
					</h2>
				</div>
			</div>

			<div class="gap-53 mt-4 flex flex-col sm:flex-row">
				<button
					on:click={redirectToShipping}
					class="text-sm cursor-pointer w-full sm:w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 hover:border-white"
					disabled={isProcessingPayment}
				>
					<span><i class="fa-classic fa-chevron-left mr-2"></i></span> Return to Shipping
				</button>
				<button
					on:click={redirectToPayMongo}
					class="text-sm cursor-pointer w-full sm:w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 bg-mabini-dark-brown hover:border-white hover:bg-transparent disabled:opacity-50 disabled:cursor-not-allowed"
					disabled={isProcessingPayment}
				>
					{isProcessingPayment ? 'Processing...' : 'Pay Now'}
				</button>
			</div>
		{:else if isShippingMethod}
			<!-- TODO: -->
			<!-- Add the chosen shipping method to the total and bind it -->
			<!-- formData.shippingMethod not yet implemented. -->
			<!-- Send it to the backend -->
			<h2 class="pb-5 font-bold text-xl text-white bg-black">Shipping Method</h2>
			<div
				class="flex-1 flex-col text-white flex justify-center items-left bg-mabini-white shadow-md rounded-xl"
			>
				<div class="p-4">
					<label class="flex items-center mb-0 cursor-pointer">
						<input
							type="radio"
							name="shippingMethod"
							value="standard"
							bind:group={$selectedShippingMethod}
							class="form-radio mr-2 border-10 border-gray-700 accent-white focus:ring-gray-700"
						/>
						<span class="text-black font-medium">Standard Delivery</span>
						<span class="ml-auto text-black">₱79.00</span>
					</label>
					<div class="pl-6 mb-4 mt-0 text-gray-600 text-sm">
						Estimated delivery time: 40 minutes
					</div>
					<label class="flex items-center mb-0 cursor-pointer">
						<input
							type="radio"
							name="shippingMethod"
							value="priority"
							bind:group={$selectedShippingMethod}
							class="form-radio mr-2 border-10 border-gray-700 accent-white focus:ring-gray-700"
						/>

						<span class="text-black font-medium">Priority Delivery</span>
						<span class="ml-auto text-black">₱109.00</span>
					</label>
					<div class="pl-6 mt-0 text-gray-600 text-sm">Estimated delivery time: 30 minutes</div>
				</div>
			</div>
			<div class=" p-6 pb-0 pt-2 pl-10">
				<div class="flex flex-col sm:flex-row gap-30 mt-2">
					<button
						type="button"
						on:click={cancelShippingMethod}
						class="text-sm cursor-pointer w-full sm:w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 hover:border-white"
					>
						<span><i class="fa-classic fa-chevron-left"></i></span> Cancel
					</button>
					<div class="flex-1 flex justify-end">
						<button
							type="submit"
							class="text-sm cursor-pointer w-full sm:w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 bg-mabini-dark-brown hover:border-white hover:bg-transparent"
							on:click={cancelShippingMethod}
						>
							Update Information
						</button>
					</div>
				</div>
			</div>
		{:else if showAddressSelector}
			<!-- Address Selection -->
			<div class="w-full rounded-xl shadow-md space-y-4 bg-mabini-white p-6">
				<div class="p-2 rounded-lg text-mabini-black">
					<h2 class="text-xl font-bold mb-4 text-black">Select Delivery Address</h2>
					<div class="space-y-3">
						{#each userAddresses as address}
							<button
								type="button"
								on:click={() => selectAddress(address)}
								class="w-full p-4 text-left border rounded-lg hover:bg-gray-100 transition
									{selectedAddress?.id === address.id ? 'border-mabini-dark-brown bg-gray-50' : 'border-gray-300'}"
							>
								<p class="font-semibold text-black">
									{address.first_name}
									{address.last_name}
								</p>
								<p class="text-gray-600 text-sm">
									{address.address}{#if address.apartment_suite_etc}, {address.apartment_suite_etc}{/if}
								</p>
								<p class="text-gray-600 text-sm">
									{address.city}, {address.region}
									{address.postal_code}
								</p>
								<p class="text-gray-600 text-sm">{address.phone}</p>
							</button>
						{/each}
					</div>
				</div>
				<button
					on:click={() => (showAddressSelector = false)}
					class="text-sm cursor-pointer w-full sm:w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 hover:border-white"
				>
					<span><i class="fa-classic fa-chevron-left mr-2"></i></span> Cancel
				</button>
			</div>
		{:else if isAddress}
			<!-- svelte-ignore component_name_lowercase -->
			<!-- Address Update -->
			<form on:submit|preventDefault={handleUpdateButton}>
				<div class="flex-1 flex-col text-white flex justify-center items-left shadow-md rounded-xl">
					<div class=" p-6 pb-0 pt-2 pl-10">
						<h2 class="text-xl font-bold mb-2">Address Information</h2>
						<h3 class="text-red-800">* indicates required field</h3>
					</div>

					<!-- Input Fields -->
					<div class="w-full p-6 pb-0 pt-2 pl-10 flex items-center justify-between space-x-4">
						<input
							id="address"
							type="text"
							bind:value={formData.address}
							required
							class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
							placeholder="Address *"
						/>
					</div>
					<div class="w-full p-6 pb-0 pt-2 pl-10 flex items-center justify-between space-x-4">
						<input
							id="apartment"
							type="text"
							bind:value={formData.apartment}
							class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
							placeholder="Apartment, suite, etc. (optional)"
						/>
					</div>
					<div class="w-full p-6 pb-0 pt-2 pl-10 flex items-center justify-between space-x-4">
						<input
							id="postalcode"
							type="text"
							bind:value={formData.postalcode}
							required
							class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
							placeholder="Postal Code *"
						/>
						<input
							id="city"
							type="text"
							bind:value={formData.city}
							required
							class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
							placeholder="City *"
						/>
					</div>
					<div class="w-full p-6 pb-0 pt-2 pl-10 flex items-center justify-between space-x-4">
						<input
							id="country"
							type="text"
							bind:value={formData.country}
							required
							class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
							placeholder="Country *"
						/>
					</div>

					<!-- Buttonss -->
					<div class=" p-6 pb-0 pt-2 pl-10">
						<div class="flex flex-col sm:flex-row gap-30 mt-2">
							<button
								type="button"
								on:click={cancelEditAddress}
								class="text-sm cursor-pointer w-full sm:w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 hover:border-white"
							>
								<span><i class="fa-classic fa-chevron-left"></i></span> Cancel
							</button>
							<div class="flex-1 flex justify-end">
								<button
									type="submit"
									class="text-sm cursor-pointer w-full sm:w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 bg-mabini-dark-brown hover:border-white hover:bg-transparent"
								>
									Update Information
								</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		{:else if isContact}
			<!-- svelte-ignore component_name_lowercase -->
			<!-- Contact Update -->
			<form on:submit|preventDefault={handleUpdateButton}>
				<div class="flex-1 flex-col text-white flex justify-center items-left shadow-md rounded-xl">
					<div class=" p-6 pb-0 pt-2 pl-10">
						<h2 class="text-xl font-bold mb-2">Contact Information</h2>
						<h3 class="text-red-800">* indicates required field</h3>
					</div>

					<!-- Input Fields -->
					<div class="w-full p-6 pb-0 pt-2 pl-10 flex items-center justify-between space-x-4">
						<input
							id="email"
							type="email"
							bind:value={formData.email}
							required
							class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
							placeholder="Email *"
						/>
					</div>
					<div class="w-full p-6 pb-0 pt-2 pl-10 flex items-center justify-between space-x-4">
						<input
							id="phone"
							type="tel"
							bind:value={formData.phone}
							required
							class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
							placeholder="Phone *"
						/>
					</div>

					<!-- Buttonss -->
					<div class=" p-6 pb-0 pt-2 pl-10">
						<div class="flex flex-col sm:flex-row gap-30 mt-2">
							<button
								type="button"
								on:click={cancelEditContact}
								class="text-sm cursor-pointer w-full sm:w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 hover:border-white"
							>
								<span><i class="fa-classic fa-chevron-left"></i></span> Cancel
							</button>
							<div class="flex-1 flex justify-end">
								<button
									type="submit"
									class="text-sm cursor-pointer w-full sm:w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 bg-mabini-dark-brown hover:border-white hover:bg-transparent"
								>
									Update Information
								</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		{/if}
	</div>

	<!-- Right Side -->
	<div class="w-full lg:w-2/4 bg-white text-black p-4 sm:p-6 lg:p-10">
		<div class="w-full">
			<h2 class="text-2xl font-bold mb-4 text-left">Items</h2>

			{#if $cartStore.loading}
				<p class="text-gray-500">Loading cart...</p>
			{:else if $cartItems.length > 0}
				{#each $cartItems as item}
					<div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-10 mb-4 pb-4 border-b sm:border-b-0">
						<img
							src={item.menu_item_image
								? `https://mabini-cafe.bscs3a.com/phpbackend/${item.menu_item_image.replace(/^\/?/, '')}`
								: '/images/placeholder.png'}
							alt={item.menu_item_name}
							class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-lg border border-gray-300"
						/>
						<div class="flex-1 flex flex-col sm:flex-row justify-between items-start sm:items-center w-full">
							<div class="mb-2 sm:mb-0">
								<h3 class="text-base sm:text-lg font-semibold">{item.menu_item_name}</h3>
								<p class="text-sm sm:text-base text-gray-600">Quantity: {item.quantity}</p>
								<p class="text-sm sm:text-base text-gray-600">₱{parseFloat(item.menu_item_price).toFixed(2)} each</p>
							</div>
							<p class="text-base sm:text-lg text-gray-500 font-semibold">
								₱{parseFloat(item.subtotal).toFixed(2)}
							</p>
						</div>
					</div>
				{/each}
				<hr class="my-4 border-gray-300" />
				
				<!-- Order Note Section -->
				{#if $orderNoteStore}
					<div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
						<h3 class="text-sm font-semibold text-gray-700 mb-2">Note:</h3>
						<p class="text-sm text-gray-600 whitespace-pre-wrap">{$orderNoteStore}</p>
					</div>
					<hr class="my-4 border-gray-300" />
				{/if}
				
				<div class="flex justify-between items-center mb-2">
					<div class="flex flex-1 justify-between items-center">
						<span class="text-gray-600"
							>Subtotal • {$cartCount} {$cartCount === 1 ? 'Item' : 'Items'}</span
						>
						<span class="font-semibold">₱{$cartTotal.toFixed(2)}</span>
					</div>
				</div>
				<div class="flex flex-1 justify-between items-center">
					<span class="text-gray-600">Shipping</span>
					<span class="text-gray-600">
						{#if $selectedShippingMethod === 'standard'}
							₱79.00
						{:else if $selectedShippingMethod === 'priority'}
							₱109.00
						{:else}
							-
						{/if}
					</span>
				</div>
				<hr class="my-4 border-gray-300" />
				<div class="flex justify-between items-center mb-2">
					<div class="flex flex-1 justify-between items-center">
						<span class="text-xl sm:text-2xl font-semibold">Total</span>
						<span class="text-lg font-bold">₱{$checkoutTotal.toFixed(2)}</span>
					</div>
				</div>
			{:else}
				<p class="text-gray-500">Your cart is empty</p>
			{/if}
		</div>
	</div>
</div>

<!-- Paymogo LOGO -->
<!-- <div class="flex gap-0 flex-col items-stretch flex-nowrap justify-start content-center">
	<div class="w-full h-[50px]">1</div>
	<div class="w-full">2</div>
</div> -->

<style lang="postcss">
	/* Mobile Responsiveness */
	@media (max-width: 1024px) {
		/* Stack layout vertically on tablets and mobile */
		.flex-col.lg\:flex-row {
			flex-direction: column !important;
		}
		
		/* Make both sides full width on mobile */
		.grow.w-2\/4,
		.w-full.lg\:w-2\/4 {
			width: 100% !important;
		}
	}

	@media (max-width: 768px) {
		/* Reduce padding on mobile */
		.p-4.sm\:p-6.lg\:p-10 {
			padding: 1rem !important;
		}

		/* Logo sizing */
		img[alt="Logo"] {
			width: 160px !important;
			height: 46px !important;
			margin-bottom: 1rem;
		}

		/* Navigation adjustments */
		nav.flex {
			flex-wrap: wrap;
			gap: 0.5rem !important;
			font-size: 0.75rem !important;
		}

		nav a {
			padding: 0.25rem 0.5rem !important;
		}

		/* Form sections */
		.flex-1.flex-col {
			padding: 1rem !important;
		}

		.p-6.pb-0.pt-2.pl-10,
		.p-6.pt-0.pl-10.pb-2 {
			padding: 1rem !important;
		}

		/* Input fields - stack vertically on mobile */
		.w-full.p-6.pb-0.pt-2.pl-10.flex.items-center.justify-between.space-x-4 {
			flex-direction: column;
			padding: 0.5rem !important;
			space-x: 0 !important;
		}

		.w-full.p-6.pb-0.pt-2.pl-10.flex.items-center.justify-between.space-x-4 input {
			margin-bottom: 0.75rem;
		}

		/* Buttons - full width on mobile */
		.gap-53,
		.gap-30 {
			gap: 0.75rem !important;
		}

		button.w-full.sm\:w-\[200px\] {
			width: 100% !important;
			padding: 0.75rem !important;
		}

		/* Headings */
		h2.text-2xl {
			font-size: 1.5rem !important;
		}

		h2.text-xl {
			font-size: 1.25rem !important;
		}

		/* Address selector */
		.space-y-3 button {
			padding: 0.75rem !important;
			font-size: 0.875rem;
		}

		/* Order note section */
		.mb-4.p-4.bg-gray-50 {
			padding: 0.75rem !important;
		}

		/* Payment method cards */
		.rounded-lg.border {
			padding: 0.75rem !important;
		}
	}

	@media (max-width: 480px) {
		/* Extra small devices */
		nav.flex {
			font-size: 0.7rem !important;
			gap: 0.25rem !important;
		}

		nav a {
			padding: 0.25rem !important;
		}

	}
</style>



