<script>
	// TODO: OTP in Adding new phone
	// TODO: OTP in Submitting an address
	//

	import { goto } from '$app/navigation';
	import { authStore, users } from '$lib/stores';
	import { showSuccess, showError, showConfirm } from '$lib/utils/sweetalert';
	import { onMount } from 'svelte';
	import { shippingStore, shippingInfo } from '$lib/stores';
	import { form } from '$app/server';
	import { usersStore, currentUser, isAuthenticated } from '$lib/stores';
	import { ordersStore } from '$lib/stores';

	$: $currentUser;

	// Fetch Orders by customer ID
	let orders = [];

	async function getUserOrders() {
		try {
			if (!$currentUser?.id) {
				return [];
			}
			const userOrders = await ordersStore.fetchByCustomerId($currentUser.id);
			return userOrders || [];
		} catch (error) {
			console.error('Error fetching orders:', error);
			return [];
		}
	}
	
	$: if ($currentUser?.id) {
		getUserOrders().then((result) => {
			orders = result;
		});
	}
	$: hasOrders = Array.isArray(orders) && orders.length > 0;

	// Getting the Account Overview details
	// Specifically for username
	let username = '';
	onMount(async () => {
		authStore.init();
		if ($currentUser?.id) {
			try {
				const user = await usersStore.fetchById($currentUser.id);
				if (user && user.username) {
					username = user.username;
				}

				return user;
			} catch (error) {
				console.error('Error fetching user:', error);
			}
		}
	});

	let addNewAddress = false;
	let editingAddressId = null;

	// Reactive statement to track if user has any addresses
	$: userAddresses = $shippingInfo || [];
	$: hasExistingShipping = userAddresses.length > 0;
	$: hasReachedAddressLimit = userAddresses.length >= 3;

	// Logout button
	function logout() {
		showConfirm('Do you want you to logout?', 'Logout');
		authStore.logout();
		localStorage.removeItem('token');
		showSuccess('Logged out successfully');
		setTimeout(() => {
			goto('/login');
		}, 2000);
	}

	// Deleting Address
	async function deleteAddress(addressId) {
		const confirmed = await showConfirm(
			'Are you sure you want to delete this address?',
			'Delete Address'
		);
		if (!confirmed) return;

		try {
			await shippingStore.deleteAddress(addressId, $currentUser.id);
			await showSuccess('Address deleted successfully!', 'Success');
			
			// Refresh addresses
			await shippingStore.fetchByUserId($currentUser.id);
			
			// Reset form if we were editing this address
			if (editingAddressId === addressId) {
				cancelEdit();
			}
		} catch (error) {
			console.error('Failed to delete address:', error);
			await showError('Failed to delete address', 'Error');
		}
	}

	// Deleting Account
	// Use sweetalerts
	async function deleteAccount() {
		if (confirm('Are you sure you want to delete your account?')) {
			try {
				// @ts-ignore
				await usersStore.delete($currentUser.id);
				await authStore.logout();
				goto('/');
			} catch (error) {
				console.error('Failed to delete account:', error);
			}
		}
	}

	// TODO: Simplify this further
	let accountOverviewActive = false;
	let accountOrdersActive = false;
	let accountAddressesActive = false;
	function accountOverview() {
		accountOverviewActive = true;
	}
	function accountOrders() {
		accountOrdersActive = true;
		accountOverviewActive = false;
		accountAddressesActive = false;
	}
	function accountAddresses() {
		accountAddressesActive = true;
		accountOverviewActive = false;
		accountOrdersActive = false;
	}
	let isEditAddress = false;
	
	function editAddress(address) {
		isEditAddress = true;
		addNewAddress = false;
		editingAddressId = address.id;
		formData = {
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
	}
	
	function showAddNewAddressForm() {
		addNewAddress = true;
		isEditAddress = false;
		editingAddressId = null;
		formData = {
			email: $currentUser?.email || '',
			fname: '',
			lname: '',
			address: '',
			apartment: '',
			postalcode: '',
			city: '',
			country: '',
			phone: ''
		};
	}
	
	function cancelEdit() {
		formData = {
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
		isEditAddress = false;
		addNewAddress = false;
		editingAddressId = null;
	}

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

	// Fetching shipping addresses
	$: if ($currentUser?.id) {
		shippingStore.fetchByUserId($currentUser.id);
	}

	// Add New Address
	async function handleAddNewAddress() {
		if (userAddresses.length >= 3) {
			await showError('You can only have up to 3 addresses', 'Address Limit Reached');
			return;
		}

		const shippingData = {
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
			await shippingStore.addAddress($currentUser.id, shippingData);
			await showSuccess('New address added successfully!', 'Success');
			// Refresh the page
			window.location.reload();
		} catch (error) {
			console.error('Error adding address:', error);
			await showError('Failed to add address', 'Error');
		}
	}

	// Update Existing Address
	async function handleUpdateAddress() {
		const shippingData = {
			user_id: $currentUser.id,
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
			await shippingStore.updateAddress($currentUser.id, shippingData);
			await showSuccess('Address updated successfully!', 'Success');
			cancelEdit();
			// Refresh addresses
			await shippingStore.fetchByUserId($currentUser.id);
		} catch (error) {
			console.error('Error updating address:', error);
			await showError('Failed to update address', 'Error');
		}
	}
</script>

<div class="flex gap-0 min-h-screen pt-20 flex-row justify-center text-center">
	<!-- Right Side -->
	<div class="grow w-full">
		<div
			class="ml-30 flex gap-0 flex-col justify-start items-center content-center text-left w-full"
		>
			<!-- SideBar -->
			<hr class="border-[1] border-gray-500 w-[50%]" />
			<div class="w-[50%] flex flex-row self-center">
				<button
					type="button"
					class="p-4 font-bold text-gray-600 w-full cursor-pointer bg-transparent border-none text-left"
					on:click={accountOverview}
				>
					Account Overview
				</button>
				<div class="flex items-center justify-center w-full">
					<input
						type="radio"
						name="account-section"
						checked={accountOverviewActive}
						on:change={accountOverview}
						class="checked:bg-green-400 max-w-[4px] max-h-[4px] appearance-none w-auto items-center ml-20 cursor-pointer bg-gray-300 text-white font-bold py-2 px-2 rounded-full border-none focus:outline-none"
					/>
				</div>
			</div>

			<hr class="border-[1] border-gray-500 w-[50%]" />
			<div class="w-[50%] flex flex-row self-center">
				<h2 class="p-4 font-bold text-gray-600 w-full cursor-pointer" on:click={accountOrders}>
					Orders
				</h2>
				<div class="flex items-center justify-center w-full">
					<input
						type="radio"
						name="account-section"
						checked={accountOrdersActive}
						on:change={accountOrders}
						class="checked:bg-green-400 max-w-[4px] max-h-[4px] appearance-none w-auto items-center ml-20 cursor-pointer bg-gray-300 text-white font-bold py-2 px-2 rounded-full border-none focus:outline-none"
					/>
				</div>
			</div>
			<hr class="border-[1] border-gray-500 w-[50%]" />
			<div class="w-[50%] flex flex-row self-center">
				<h2 class="p-4 font-bold text-gray-600 w-full cursor-pointer" on:click={accountAddresses}>
					Addresses
				</h2>
				<div class="flex items-center justify-center w-full">
					<input
						type="radio"
						name="account-section"
						checked={accountAddressesActive}
						on:change={accountAddresses}
						class="checked:bg-green-400 max-w-[4px] max-h-[4px] appearance-none w-auto items-center ml-20 cursor-pointer bg-gray-300 text-white font-bold py-2 px-2 rounded-full border-none focus:outline-none"
					/>
				</div>
			</div>
			<div class="w-[50%] pt-10 self-center">
				<h2 class="pt-2 text-gray-500">
					Need help? <a href="/contact-us" class="text-gray-500 !underline">Contact us</a>
				</h2>
				<div class="flex flex-row gap-2 justify-center items-center font-bold">
					<button
						type="button"
						class="uppercase cursor-pointer text-sm p-5 w-full max-w-[130px] rounded-full bg-transparent border-1 border-black text-black hover:bg-mabini-dark-brown hover:text-white hover:border-transparent px-4 py-2 mt-5"
						on:click={logout}
					>
						Logout
					</button>
					<button
						type="button"
						class="text-sm uppercase cursor-pointer w-full p-2 rounded-full bg-transparent border-1 border-red-500 text-red-500 hover:bg-red-500 hover:text-white hover:border-transparent px-4 py-2 mt-5"
						on:click={deleteAccount}
					>
						Delete Account
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- left side -->
	<div class="grow w-full m-10 mt-0 ml-0 justify-start items-center">
		{#if accountOverviewActive}
			<div
				class="flex flex-col justify-start items-center content-center text-left gap-4 min-h-[500px] max-w-[75%] bg-black text-white rounded-3xl"
			>
				<h1 class="p-10 pb-0 font-bold text-3xl w-full">
					Hi, {username.charAt(0).toUpperCase() + username.slice(1)}!
				</h1>
				<hr class="border-[1] text-white w-[90%] text-center" />

				<!-- if has info -->
				<!-- Info section -->
				{#if hasExistingShipping && userAddresses.length > 0}
					<div class="gap-1 text-left p-7 pl-10 pt-0 w-full flex flex-col">
						<p>Your Info</p>
						<!-- 1st row -->
						<div class="flex flex-row gap-10 mt-2">
							<div class="flex flex-col gap-2">
								<label for="name" class="text-sm">Name</label>
								<p id="name" class="text-gray-600 font-bold text-md">
									{userAddresses[0].first_name.charAt(0).toUpperCase() + userAddresses[0].first_name.slice(1)}
									{userAddresses[0].last_name.charAt(0).toUpperCase() + userAddresses[0].last_name.slice(1)}
								</p>
							</div>
							<div class="flex flex-col gap-2 text-sm">
								<label for="email">Email</label>
								<p id="email" class="text-gray-600 font-bold text-md">{userAddresses[0].email}</p>
							</div>
						</div>
						<!-- 2nd row -->
						<div class="flex flex-row gap-10 mt-2">
							<div class="flex flex-col gap-2 text-sm">
								<label for="phone">Phone</label>
								<p id="phone" class="text-gray-600 font-bold text-md">{userAddresses[0].phone}</p>
							</div>
							<div class="flex flex-col gap-2 text-sm">
								<label for="address">Address</label>
								<p id="address" class="text-gray-600 font-bold text-md">{userAddresses[0].address}</p>
							</div>
						</div>
						<!-- 3rd row -->
					</div>
				{:else}
					<h1 class="p-10 pb-0 font-bold text-3xl w-full text-center">
						You have no account details yet.
					</h1>
					<p class="text-gray-600">Add your details in the Addresses section.</p>
				{/if}
			</div>
		{:else if accountOrdersActive}
			<div
				class="flex flex-col justify-start items-center content-center text-left gap-4 min-h-[500px] max-w-[75%] bg-black text-white rounded-3xl pb-10"
			>
				{#if hasOrders}
					<h1 class="p-10 pb-0 font-bold text-3xl w-full pl-6">Your Orders</h1>
					<hr class="border-[1] text-white w-[90%] text-center" />
					<!-- Orders List -->
					<div class="w-full px-6 space-y-6">
						{#each orders as order}
							<div class="bg-gray-900 rounded-lg p-6">
								<div class="flex justify-between items-start mb-4">
									<div>
										<h2 class="text-xl font-bold">Order #{String(order.id).padStart(4, '0')}</h2>
										<p class="text-gray-400 text-sm mt-1">
											Placed on: {order.order_time
												? new Date(order.order_time.replace(' ', 'T')).toLocaleDateString('en-US', {
														month: 'long',
														day: 'numeric',
														year: 'numeric',
														hour: 'numeric',
														minute: '2-digit',
														hour12: true
													})
												: 'N/A'}
										</p>
								</div>
								<!-- Status Badge -->
								<span class="px-4 py-2 rounded-full text-sm font-bold capitalize
									{order.status?.toLowerCase() === 'pending' || !order.status ? 'bg-yellow-200 text-yellow-800' :
									order.status?.toLowerCase() === 'preparing' ? 'bg-blue-200 text-blue-800' :
									order.status?.toLowerCase() === 'delivering' ? 'bg-purple-200 text-purple-800' :
									order.status?.toLowerCase() === 'completed' ? 'bg-green-200 text-green-800' :
									'bg-red-200 text-red-800'}">
									{order.status || 'pending'}
								</span>
							</div>								<!-- Order Items (if available) -->
								{#if order.items && order.items.length > 0}
									<div class="space-y-2 mb-4">
										{#each order.items as item}
											<div class="flex justify-between items-center">
												<div>
													<p class="font-medium">{item.name} x {item.quantity}</p>
													<p class="text-gray-400 text-sm">₱{item.price} each</p>
												</div>
												<p class="font-bold">₱{item.price * item.quantity}</p>
											</div>
										{/each}
									</div>
								{:else}
									<p class="text-gray-400 text-sm mb-4">Order details not available</p>
								{/if}

								<!-- Order Details -->
								<div class="border-t border-gray-700 pt-4 space-y-2">
									{#if order.shipping_name}
										<div class="flex justify-between text-sm">
											<span class="text-gray-400">Shipping Method:</span>
											<span>{order.shipping_name}</span>
										</div>
									{/if}
									{#if order.shipping_fee}
										<div class="flex justify-between text-sm">
											<span class="text-gray-400">Shipping Fee:</span>
											<span>₱{order.shipping_fee}</span>
										</div>
									{/if}
									<div class="flex justify-between text-sm">
										<span class="text-gray-400">Payment Method:</span>
										<span class="capitalize">{order.payment_method || 'N/A'}</span>
									</div>
									<div class="flex justify-between text-sm">
										<span class="text-gray-400">Payment Status:</span>
										<span class="px-2 py-1 rounded-full text-xs font-bold
											{order.payment_status === 'Paid' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800'}">
											{order.payment_status || 'Pending'}
										</span>
									</div>
								</div>

								<!-- Order Total -->
								<div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-700">
									<p class="font-bold text-lg">Total Amount:</p>
									<p class="font-extrabold text-xl text-green-400">₱{order.total_amount}</p>
								</div>

								{#if order.message}
									<div class="mt-4 pt-4 border-t border-gray-700">
										<p class="text-gray-400 text-sm">Message:</p>
										<p class="text-sm italic">{order.message}</p>
									</div>
								{/if}
							</div>
						{/each}
					</div>
				{:else}
					<h1 class="p-10 pb-0 font-bold text-3xl w-full text-center">You have no orders yet.</h1>
					<p class="text-gray-400 text-center">Start shopping now and come back to view your orders here.</p>
					<a href="/menu" class="bg-mabini-yellow text-black px-6 py-3 rounded-full font-bold hover:bg-yellow-500 transition-colors">
						Browse Menu
					</a>
				{/if}
			</div>
		{:else if accountAddressesActive}
			<div
				class="flex flex-col justify-start items-center content-center text-left gap-4 min-h-[200px] max-w-[75%] bg-black text-white rounded-3xl"
			>
				<!-- if has address -->
				{#if hasExistingShipping}
					<h1 class="p-10 pb-0 font-bold text-3xl w-full">
						Your addresses, {userAddresses[0]?.first_name.charAt(0).toUpperCase() +
							userAddresses[0]?.first_name.slice(1)}
						{userAddresses[0]?.last_name.charAt(0).toUpperCase() + userAddresses[0]?.last_name.slice(1)}
					</h1>
					<hr class="border-[1] text-white w-[90%] text-center" />

					<!-- Display all addresses dynamically -->
					{#each userAddresses as address, index}
						<div class="gap-1 text-left p-7 pl-10 pt-0 w-full flex flex-col">
							<h2 class="font-medium w-full">{index === 0 ? 'Default Address' : `Address ${index + 1}`}</h2>
							<div class="w-full flex flex-row items-center justify-between">
								<p class="text-gray-600">
									{address.first_name.charAt(0).toUpperCase() + address.first_name.slice(1)}
									{address.last_name.charAt(0).toUpperCase() + address.last_name.slice(1)}
								</p>
								<button
									class="cursor-pointer w-auto text-sm font-bold flex items-center justify-center text-center border-1 border-white p-2 pr-15 pl-15 max-w-[136px] max-h-[23px] rounded-full
									hover:bg-mabini-dark-brown hover:text-white hover:border-transparent"
									on:click={() => editAddress(address)}
								>
									EDIT
								</button>
							</div>

							<div class="w-full flex flex-row items-center justify-between">
								<p class="w-full text-gray-600">
									{address.address}{#if address.apartment_suite_etc}, {address.apartment_suite_etc}{/if}, {address.city}, {address.region}
								</p>
								<button
									class="cursor-pointer w-auto text-sm font-bold flex items-center justify-center text-center border-1 border-white p-2 pr-13 pl-13 max-w-[136px] max-h-[23px] rounded-full
									hover:bg-red-500 hover:text-white hover:border-transparent"
									on:click={() => deleteAddress(address.id)}>DELETE</button
								>
							</div>
						</div>
						{#if index < userAddresses.length - 1}
							<hr class="border-[1] text-white w-[90%] text-center" />
						{/if}
					{/each}

					<!-- Button for adding new address -->
					{#if !isEditAddress && !addNewAddress}
						<hr class="border-[1] text-white w-[90%] text-center" />
						<div class="justify-start p-10 pt-0 flex w-full">
							{#if hasReachedAddressLimit}
								<p class="text-gray-500 text-sm">You have reached the maximum limit of 3 addresses</p>
							{:else}
								<button
									on:click={showAddNewAddressForm}
									class="text-m cursor-pointer max-w-[213px] max-h-[36px] px-5 font-bold uppercase border-[1px] border-white rounded-full
									hover:bg-mabini-dark-brown hover:text-white hover:border-transparent"
								>
									Add New Address
								</button>
							{/if}
						</div>
					{/if}
					<!-- If Edit or Add New is clicked -->
					{#if (isEditAddress || addNewAddress) && hasExistingShipping}
						<div class="p-15 pt-0 justify-start items-start text-left w-full gap-4 flex flex-col">
							<h2 class="w-full text-left font-bold text-2xl">
								{isEditAddress ? 'EDIT ADDRESS' : 'ADD NEW ADDRESS'}
							</h2>
							<h3 class="text-red-800">* indicates required field</h3>

							<!-- svelte-ignore component_name_lowercase -->
							<form
								on:submit|preventDefault={isEditAddress ? handleUpdateAddress : handleAddNewAddress}
								class="w-full max-w-md rounded-xl space-y-4"
							>
								<!-- form fields here -->
								<div>
									<input
										id="email"
										type="email"
										bind:value={formData.email}
										required
										class="w-full border rounded-xl px-3 py-2 text-black bg-white"
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
											class="w-full border rounded-xl px-3 py-2 text-black bg-white"
											placeholder="First Name *"
										/>
									</div>
									<div class="flex-1">
										<input
											id="lname"
											type="text"
											bind:value={formData.lname}
											required
											class="w-full border rounded-xl px-3 py-2 text-black bg-white"
											placeholder="Last Name *"
										/>
									</div>
								</div>
								<div>
									<input
										id="address"
										type="text"
										bind:value={formData.address}
										required
										class="w-full border rounded-xl px-3 py-2 text-black bg-white"
										placeholder="Address *"
									/>
								</div>
								<div>
									<input
										id="apartment"
										type="text"
										bind:value={formData.apartment}
										class="w-full border rounded-xl px-3 py-2 text-black bg-white"
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
											class="w-full border rounded-xl px-3 py-2 text-black bg-white"
											placeholder="Postal Code *"
										/>
									</div>
									<div class="flex-1">
										<input
											id="city"
											type="text"
											bind:value={formData.city}
											required
											class="w-full border rounded-xl px-3 py-2 text-black bg-white"
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
										class="w-full border rounded-xl px-3 py-2 text-black bg-white"
										placeholder="Country *"
									/>
								</div>
								<div>
									<input
										id="phone"
										type="tel"
										bind:value={formData.phone}
										required
										class="w-full border rounded-xl px-3 py-2 text-black bg-white"
										placeholder="Phone *"
									/>
								</div>
								<div class="flex flex-row gap-5 mt-2">
									<button
										type="submit"
										class="text-xs cursor-pointer min-w-[220px] max-w-[320px] h-[40px] px-6 font-bold uppercase border-[1px] border-white rounded-full
									hover:bg-mabini-dark-brown hover:text-white hover:border-transparent flex items-center justify-center"
									>
										{isEditAddress ? 'Update Address' : 'Add Address'}
									</button>
									<button
										type="button"
										on:click={cancelEdit}
										class="text-xs cursor-pointer min-w-[150px] h-[40px] px-6 font-bold uppercase border-[1px] border-white rounded-full
									hover:bg-mabini-dark-brown hover:text-white hover:border-transparent flex items-center justify-center"
										>Cancel
									</button>
								</div>
							</form>
						</div>
					{/if}

					<!-- If Delete is Clicked -->
					<!-- If Edit is clicked -->

					<!-- If Delete is Clicked -->
				{:else}
					<!-- if No address -->
					<h1 class="p-10 pb-0 font-bold text-3xl text-left">You have no saved addresses.</h1>
					<!-- svelte-ignore component_name_lowercase -->
					<form
						on:submit|preventDefault={handleAddNewAddress}
						class="w-full max-w-md rounded-xl shadow-md space-y-4"
					>
						<div>
							<h2 class="text-xl font-bold mb-2 text-white">Add Delivery Address</h2>
							<h3 class="text-red-800">* indicates required field</h3>
						</div>

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

						<div class="flex flex-row gap-30 mt-2 mb-20">
							<button
								type="submit"
								class="text-sm cursor-pointer w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 bg-mabini-dark-brown hover:border-white hover:bg-transparent"
							>
								Save Address
							</button>
						</div>
					</form>
				{/if}
			</div>
		{/if}
	</div>
</div>
