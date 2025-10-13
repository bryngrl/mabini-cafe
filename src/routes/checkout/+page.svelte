<!-- Form and Checkout -->

<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import { cartStore, cartItems, cartTotal, cartCount } from '$lib/stores/cart';
	import { authStore } from '$lib/stores/auth';
	import { showError, showSuccess } from '$lib/utils/sweetalert';
	import { shippingStore, shippingInfo, shippingLoading, shippingError } from '$lib/stores';
	import { form } from '$app/server';

	function redirectToCart() {
		goto('/cart');
	}
	function redirectToShipping() {
		goto('/checkout/shipping');
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

	function enableEditMode() {
		isEditMode = true;
	}

	function cancelEdit() {
		formData = { ...existingInfo };
		isEditMode = false;
	}

	// Get the cart items and check if the user is logged in
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
				const userShippingInfo = await shippingStore.fetchByUserId($authStore.user.id);

				if (userShippingInfo && userShippingInfo.email) {
					existingInfo = {
						email: userShippingInfo.email,
						fname: userShippingInfo.first_name,
						lname: userShippingInfo.last_name,
						address: userShippingInfo.address,
						apartment: userShippingInfo.apartment_suite_etc || '',
						postalcode: userShippingInfo.postal_code,
						city: userShippingInfo.city,
						country: userShippingInfo.region,
						phone: userShippingInfo.phone
					};
					formData = { ...existingInfo };
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
			await shippingStore.update($authStore.user?.id, shippingData);
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
<div class="flex gap-[2px] h-screen content-center">
	<!-- left side -->
	<div class=" grow w-2/4 bg-black text-white flex justify-center p-10">
		<div>
			<img src="/images/LOGO-4.png" alt="Logo" class="mb-2 w-[238px] h-[68px] m-auto" />
			<nav class="flex mb-10 text-sm m-auto font-semibold gap-4 items-center justify-center">
				<a href="/cart" class="text-gray-400 hover:text-white transition px-2"> Cart</a>
				<a href="/checkout" class="text-white px-2">Information</a>
				<a href="/checkout/shipping" class="text-gray-400 hover:text-white transition px-2">Shipping</a>
				<a href="/checkout/payment" class="text-gray-400 hover:text-white transition px-2">Payment</a>
			</nav>

			<div class="flex-1 flex-col text-white flex justify-center items-left">
				{#if hasExistingShipping && !isEditMode}
					<!-- Display existing shipping info -->
					<div class="w-full rounded-xl shadow-md space-y-4 bg-mabini-white p-6">
						<div class="p-2 rounded-lg text-mabini-black flex justify-between items-center w-full">
							<h2 class="text-l font-thin text-gray-600">Review Your Information</h2>
							<button
								on:click={enableEditMode}
								class="text-mabini-dark-brown underline text-sm hover:text-mabini-yellow"
							>
								Edit
							</button>
						</div>
						<hr class="border-gray-300 my-4" />
						<div class="p-2 rounded-lg text-mabini-black">
							<h2 class="text-l font-thin text-gray-600">Recipient Information</h2>
							<p class="text-l mt-2 p-2">
								<span class="flex justify-between items-center w-full">
									<span>
										<span class="font-thin text-gray-600">Name:</span>
										{existingInfo.fname.charAt(0).toUpperCase() + existingInfo.fname.slice(1)}
										{existingInfo.lname.charAt(0).toUpperCase() + existingInfo.lname.slice(1)}
									</span>
								</span>
							</p>
							<p class="text-l mt-2 p-2">
								<span class="font-thin text-gray-600">Email:</span>
								{existingInfo.email}
							</p>
							<p class="text-l mt-2 p-2">
								<span class="font-thin text-gray-600">Phone:</span>
								{existingInfo.phone}
							</p>
						</div>
						<hr class="border-gray-300 my-4" />
						<div class="p-2 rounded-lg text-mabini-black">
							<h2 class="text-l font-thin text-gray-600">Delivery Address</h2>
							<p class="text-l mt-2 p-2">
								<span class="font-thin text-gray-600">Address: </span>
								{existingInfo.address}, {existingInfo.city}, {existingInfo.postalcode}
							</p>
						</div>
					</div>

					<div class="gap-60 mt-4 flex flex-row">
						<button
							on:click={redirectToCart}
							class="text-sm cursor-pointer w-fit p-5 pt-2 pb-2 rounded-full border-transparent border-1 hover:border-white"
						>
							<span><i class="fa-classic fa-chevron-left mr-2"></i></span> Return to Cart
						</button>
						<button
							on:click={redirectToShipping}
							class="text-sm cursor-pointer w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 bg-mabini-dark-brown hover:border-white hover:bg-transparent"
						>
							Continue to Shipping
						</button>
					</div>
				{:else if hasExistingShipping && isEditMode}
					<!-- Edit mode form -->
					<!-- svelte-ignore component_name_lowercase -->
					<form
						on:submit|preventDefault={handleUpdateButton}
						class="w-full max-w-md rounded-xl shadow-md space-y-4"
					>
						<h2 class="text-xl font-bold mb-2">Edit Contact Information</h2>
						<h3 class="text-red-800">* indicates required field</h3>
						<div>
							<input
								id="email"
								type="email"
								bind:value={formData.email}
								required
								class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
								placeholder="Email *"
							/>
						</div>
						<div class="flex gap-4">
							<div class="flex-1">
								<input
									id="fname"
									type="text"
									bind:value={formData.fname}
									required
									class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
									placeholder="First Name *"
								/>
							</div>
							<div class="flex-1">
								<input
									id="lname"
									type="text"
									bind:value={formData.lname}
									required
									class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
									placeholder="Last Name *"
								/>
							</div>
						</div>
						<div>
							<h2 class="text-xl font-bold mb-2 text-white">Delivery Address</h2>
							<input
								id="address"
								type="text"
								bind:value={formData.address}
								required
								class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
								placeholder="Address *"
							/>
						</div>
						<div>
							<input
								id="apartment"
								type="text"
								bind:value={formData.apartment}
								class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
								placeholder="Apartment, suite, etc. (optional)"
							/>
						</div>
						<div class="flex gap-4">
							<div class="flex-1">
								<input
									id="postalcode"
									type="text"
									bind:value={formData.postalcode}
									required
									class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
									placeholder="Postal Code *"
								/>
							</div>
							<div class="flex-1">
								<input
									id="city"
									type="text"
									bind:value={formData.city}
									required
									class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
									placeholder="City *"
								/>
							</div>
						</div>
						<div>
							<input
								id="country"
								type="text"
								bind:value={formData.country}
								required
								class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
								placeholder="Country *"
							/>
						</div>
						<div>
							<input
								id="phone"
								type="tel"
								bind:value={formData.phone}
								required
								class="w-full border rounded-xl px-3 py-2 text-white bg-transparent"
								placeholder="Phone *"
							/>
						</div>

						<div class="flex flex-row gap-30 mt-2">
							<button
								type="button"
								on:click={cancelEdit}
								class="text-sm cursor-pointer w-fit p-5 pt-2 pb-2 rounded-full border-transparent border-1 hover:border-white"
							>
								<span><i class="fa-classic fa-chevron-left"></i></span> Cancel
							</button>
							<button
								type="submit"
								class="text-sm cursor-pointer w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 bg-mabini-dark-brown hover:border-white hover:bg-transparent"
							>
								Update Information
							</button>
						</div>
					</form>
				{:else}
					<!-- svelte-ignore component_name_lowercase -->
					<form
						on:submit|preventDefault={handleSubmit}
						class="w-full max-w-md rounded-xl shadow-md space-y-4"
					>
						<h2 class="text-xl font-bold mb-2">Contact Information</h2>
						<h3 class="text-red-800">* indicates required field</h3>
						<div>
							<input
								id="email"
								type="email"
								bind:value={formData.email}
								required
								class="w-full border rounded-xl px-3 py-2 text-white"
								placeholder="Email *"
							/>
						</div>
						<div class="flex gap-4">
							<div class="flex-1">
								<input
									id="fname"
									type="text"
									bind:value={formData.fname}
									required
									class="w-full border rounded-xl px-3 py-2 text-white"
									placeholder="First Name *"
								/>
							</div>
							<div class="flex-1">
								<input
									id="lname"
									type="text"
									bind:value={formData.lname}
									required
									class="w-full border rounded-xl px-3 py-2 text-white"
									placeholder="Last Name *"
								/>
							</div>
						</div>
						<div>
							<h2 class="text-xl font-bold mb-2 text-white">Delivery Address</h2>
							<input
								id="address"
								type="text"
								bind:value={formData.address}
								required
								class="w-full border rounded-xl px-3 py-2 text-white"
								placeholder="Address *"
							/>
						</div>
						<div>
							<input
								id="apartment"
								type="text"
								bind:value={formData.apartment}
								class="w-full border rounded-xl px-3 py-2 text-white"
								placeholder="Apartment, suite, etc. (optional)"
							/>
						</div>
						<div class="flex gap-4">
							<div class="flex-1">
								<input
									id="postalcode"
									type="text"
									bind:value={formData.postalcode}
									required
									class="w-full border rounded-xl px-3 py-2 text-white"
									placeholder="Postal Code *"
								/>
							</div>
							<div class="flex-1">
								<input
									id="city"
									type="text"
									bind:value={formData.city}
									required
									class="w-full border rounded-xl px-3 py-2 text-white"
									placeholder="City *"
								/>
							</div>
						</div>
						<div>
							<input
								id="country"
								type="text"
								bind:value={formData.country}
								required
								class="w-full border rounded-xl px-3 py-2 text-white"
								placeholder="Country *"
							/>
						</div>
						<div>
							<input
								id="phone"
								type="tel"
								bind:value={formData.phone}
								required
								class="w-full border rounded-xl px-3 py-2 text-white"
								placeholder="Phone *"
							/>
						</div>
						<div class="gap-60 mt-4 flex flex-row">
							<button
								on:click={redirectToCart}
								class="text-sm cursor-pointer w-fit p-5 pt-2 pb-2 rounded-full border-transparent border-1 hover:border-white"
							>
								<span><i class="fa-classic fa-chevron-left mr-2"></i></span> Return to Cart
							</button>
							<button
								on:click={redirectToShipping}
								class="text-sm cursor-pointer w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 bg-mabini-dark-brown hover:border-white hover:bg-transparent"
							>
								Continue to Shipping
							</button>
						</div>
					</form>
				{/if}
			</div>
		</div>
	</div>
	<!-- right side -->
	<div class="grow w-2/4 bg-white text-black p-10">
		<!-- Cart Items -->

		<div class="w-full">
			<h2 class="text-2xl font-bold mb-4 text-left">Items</h2>

			{#if $cartStore.loading}
				<p class="text-gray-500">Loading cart...</p>
			{:else if $cartItems.length > 0}
				{#each $cartItems as item}
					<div class="flex items-center gap-10 mb-4">
						<img
							src={item.menu_item_image
								? `http://localhost/mabini-cafe/phpbackend/${item.menu_item_image.replace(/^\/?/, '')}`
								: '/images/placeholder.png'}
							alt={item.menu_item_name}
							class="w-24 h-24 object-cover rounded-lg border border-gray-300"
						/>
						<div class="flex-1 flex justify-between items-center">
							<div>
								<h3 class="text-lg font-semibold">{item.menu_item_name}</h3>
								<p class="text-gray-600">Quantity: {item.quantity}</p>
								<p class="text-gray-600">₱{parseFloat(item.menu_item_price).toFixed(2)} each</p>
							</div>
							<p class="text-lg text-gray-500 font-semibold pl-[250px]">
								₱{parseFloat(item.subtotal).toFixed(2)}
							</p>
						</div>
					</div>
				{/each}
				<hr class="my-4 border-gray-300" />
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
					<span class="text-gray-600">Calculate at next step</span>
				</div>
				<hr class="my-4 border-gray-300" />
				<div class="flex justify-between items-center mb-2">
					<div class="flex flex-1 justify-between items-center">
						<span class="text-[25px] font-semibold">Total</span>
						<span class="text-lg font-bold">₱{$cartTotal.toFixed(2)}</span>
					</div>
				</div>
			{:else}
				<p class="text-gray-500">Your cart is empty</p>
			{/if}
		</div>
	</div>
</div>
