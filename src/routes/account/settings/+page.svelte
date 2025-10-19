<!-- TODO: OTP FOR DELETION -->
<!-- TODO: Function for Deletion of Address -->
<!-- TODO: Delete Function in  -->
<!-- TODO: Orders Page -->
<!-- TODO: Add new Address Function -->
<!-- TODO: Confirmation for each -->
<!-- TODO: OTP for changing numbers -->

<script>
	import { goto } from '$app/navigation';
	import { authStore, users } from '$lib/stores';
	import { showSuccess, showError, showConfirm } from '$lib/utils/sweetalert';
	import { onMount } from 'svelte';
	import { shippingStore } from '$lib/stores';
	import { form } from '$app/server';
	import { usersStore, currentUser, isAuthenticated } from '$lib/stores';
	import { orderItemsStore } from '$lib/stores';

	$: $currentUser;

	// Fetch Orders  == TODO: Make sure that the only orders here are
	// successful only.
	let orders = [];

	onMount(async () => {
		authStore.init();
		try {
			orders = await orderItemsStore.fetchByUserId($currentUser.id);
			console.log(orders);
		} catch (error) {
			console.error('Error fetching orders:', error);
		}
	});

	// To check if orders is empty:
	$: hasOrders = Array.isArray(orders) && orders.length > 0;

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

	function logout() {
		authStore.logout();
		localStorage.removeItem('token');
		showSuccess('Logged out successfully');
		setTimeout(() => {
			goto('/login');
		}, 2000);
	}

	async function deleteAddress() {
		try {
			const shippingInfo = await shippingStore.fetchByUserId($currentUser.id);
			if (shippingInfo && shippingInfo.id) {
				await shippingStore.delete(shippingInfo.id);
				showSuccess('Address deleted successfully. Your name, phone, and email remain unchanged.');
				formData.address = '';
				formData.apartment = '';
				formData.postalcode = '';
				formData.city = '';
				formData.country = '';
				existingInfo.address = '';
				existingInfo.apartment = '';
				existingInfo.postalcode = '';
				existingInfo.city = '';
				existingInfo.country = '';
				hasExistingShipping = false;
				isEditAddress = false;
			} else {
				showError('No address found to delete', 'Error');
			}
		} catch (error) {
			console.error('Failed to fetch shipping info:', error);
		}
	}

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
	//##authStore

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
	function editAddress() {
		isEditAddress = true;
	}
	function cancelEdit() {
		formData = { ...existingInfo };
		isEditAddress = false;
	}

	let hasExistingShipping = false;
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
	onMount(async () => {
		authStore.init();
		// @ts-ignore
		if ($currentUser.id) {
			try {
				// @ts-ignore
				const userShippingInfo = await shippingStore.fetchByUserId($currentUser.id);

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

	let hasDetails = false;
	async function handleSubmit() {
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
			await showSuccess('Shipping information saved successfully', 'Success');
			existingInfo = { ...formData };
			hasExistingShipping = true;
			isEditAddress = false;
			hasDetails = true;
		} catch (error) {
			console.error('Error saving shipping info:', error);
			await showError('Failed to save shipping information', 'Error');
		}
	}

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
			isEditAddress = false;
		} catch (error) {
			console.error('Error updating shipping info:', error);
			await showError('Failed to update shipping information', 'Error');
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
				{#if hasExistingShipping}
					<div class="gap-1 text-left p-7 pl-10 pt-0 w-full flex flex-col">
						<p>Your Info</p>
						<!-- 1st row -->
						<div class="flex flex-row gap-10 mt-2">
							<div class="flex flex-col gap-2">
								<label for="name" class="text-sm">Name</label>
								<p id="name" class="text-gray-600 font-bold text-md">
									{formData.fname.charAt(0).toUpperCase() + formData.fname.slice(1)}
									{formData.lname.charAt(0).toUpperCase() + formData.lname.slice(1)}
								</p>
							</div>
							<div class="flex flex-col gap-2 text-sm">
								<label for="email">Email</label>
								<p id="email" class="text-gray-600 font-bold text-md">{formData.email}</p>
							</div>
						</div>
						<!-- 2nd row -->
						<div class="flex flex-row gap-10 mt-2">
							<div class="flex flex-col gap-2 text-sm">
								<label for="phone">Phone</label>
								<p id="phone" class="text-gray-600 font-bold text-md">{formData.phone}</p>
							</div>
							<div class="flex flex-col gap-2 text-sm">
								<label for="address">Address</label>
								<p id="address" class="text-gray-600 font-bold text-md">{formData.address}</p>
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
				class="flex flex-col justify-start items-center content-center text-left gap-4 min-h-[500px] max-w-[75%] bg-black text-white rounded-3xl"
			>
				{#if hasOrders}
					<h1 class="p-10 pb-0 font-bold text-3xl w-full">Your total orders.</h1>
				{:else}
					<h1 class="p-10 pb-0 font-bold text-3xl w-full">You have no orders yet.</h1>
					<p class="text-gray-600">Start shopping now and come back to view your orders here.</p>
				{/if}
			</div>
		{:else if accountAddressesActive}
			<div
				class="flex flex-col justify-start items-center content-center text-left gap-4 min-h-[200px] max-w-[75%] bg-black text-white rounded-3xl"
			>
				<!-- if has address -->
				{#if hasExistingShipping}
					<h1 class="p-10 pb-0 font-bold text-3xl w-full">
						Your addresses, {existingInfo.fname.charAt(0).toUpperCase() +
							existingInfo.fname.slice(1)}
						{existingInfo.lname.charAt(0).toUpperCase() + existingInfo.lname.slice(1)}
					</h1>
					<hr class="border-[1] text-white w-[90%] text-center" />

					<!-- Type of Address -->
					<div class="gap-1 text-left p-7 pl-10 pt-0 w-full flex flex-col">
						<h2 class="font-medium w-full">Default Address</h2>
						<div class="w-full flex flex-row items-center justify-between">
							<p class="text-gray-600">
								{existingInfo.fname.charAt(0).toUpperCase() + existingInfo.fname.slice(1)}
								{existingInfo.lname.charAt(0).toUpperCase() + existingInfo.lname.slice(1)}
							</p>
							<button
								class="cursor-pointer w-auto text-sm font-bold flex items-center justify-center text-center border-1 border-white p-2 pr-15 pl-15 max-w-[136px] max-h-[23px] rounded-full
                                hover:bg-mabini-dark-brown hover:text-white hover:border-transparent"
								on:click={editAddress}
							>
								EDIT
							</button>
						</div>

						<div class="w-full flex flex-row items-center justify-between">
							<p class="w-full text-gray-600">
								{existingInfo.address},
								{#if existingInfo.apartment}
									, {existingInfo.apartment}
								{/if}
								{existingInfo.city}
							</p>
							<button
								class="cursor-pointer w-auto text-sm font-bold flex items-center justify-center text-center border-1 border-white p-2 pr-13 pl-13 max-w-[136px] max-h-[23px] rounded-full
                                hover:bg-red-500 hover:text-white hover:border-transparent"
								on:click={deleteAddress}>DELETE</button
							>
						</div>
					</div>

					<!-- OTHER ADDRESSES -->
					<!-- Display an another address using each with a maximum of 2 other addresses -->

					<!-- Add new Address button then check it if the other address is equal to 2  -->
					<hr class="border-[1] text-white w-[90%] text-center" />

					<!-- Another Address  -->
					<!--! TODO: Change it into a dynamic one -->
					<div class="gap-1 text-left p-7 pl-10 pt-0 w-full flex flex-col">
						<h2 class="font-medium w-full">Another Address</h2>
						<div class="w-full flex flex-row items-center justify-between">
							<p class="text-gray-600">
								{existingInfo.fname.charAt(0).toUpperCase() + existingInfo.fname.slice(1)}
								{existingInfo.lname.charAt(0).toUpperCase() + existingInfo.lname.slice(1)}
							</p>
							<button
								class="cursor-pointer w-auto text-sm font-bold flex items-center justify-center text-center border-1 border-white p-2 pr-15 pl-15 max-w-[136px] max-h-[23px] rounded-full
                                hover:bg-mabini-dark-brown hover:text-white hover:border-transparent"
								on:click={editAddress}
							>
								EDIT
							</button>
						</div>

						<div class="w-full flex flex-row items-center justify-between">
							<p class="w-full text-gray-600">
								{existingInfo.address},
								{#if existingInfo.apartment}
									, {existingInfo.apartment}
								{/if}
								{existingInfo.city}
							</p>
							<button
								class="cursor-pointer w-auto text-sm font-bold flex items-center justify-center text-center border-1 border-white p-2 pr-13 pl-13 max-w-[136px] max-h-[23px] rounded-full
                                hover:bg-red-500 hover:text-white hover:border-transparent"
								on:click={deleteAddress}>DELETE</button
							>
						</div>
					</div>
					<!-- Button for adding new address -->
					<!-- TODO: READJUST THIS SO THAT ITLL BE GONE WHEN THE EDIT BUTTON IS CLICKED -->

					{#if !isEditAddress}
						<div class="justify-start p-10 pt-0 flex w-full">
							<button
								class="text-m cursor-pointer max-w-[213px] max-h-[36px] px-5 font-bold uppercase border-[1px] border-white rounded-full
                hover:bg-mabini-dark-brown hover:text-white hover:border-transparent
                "
							>
								Add New Address
							</button>
						</div>
					{/if}
					<!-- If Edit is clicked -->
					{#if isEditAddress && hasExistingShipping && !addNewAddress}
						<div class="p-15 pt-0 justify-start items-start text-left w-full gap-4 flex flex-col">
							<h2 class="w-full text-left font-bold text-2xl">EDIT INFORMATION</h2>
							<h3 class="text-red-800">* indicates required field</h3>

							<!-- svelte-ignore component_name_lowercase -->
							<form
								on:submit|preventDefault={handleUpdateButton}
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
										<span class="mr-2"><i class="fa-classic fa-chevron-left"></i></span> Update Information
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
						on:submit|preventDefault={handleSubmit}
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
								type="button"
								on:click={cancelEdit}
								class="text-sm cursor-pointer w-fit p-5 pt-2 pb-2 rounded-full border-transparent border-1 hover:border-white"
							>
								<span><i class="fa-classic fa-chevron-left"></i></span> Cancel
							</button>
							<button
								type="submit"
								class="text-sm cursor-pointer w-[200px] p-5 pt-2 pb-2 rounded-full border-transparent border-1 bg-mabini-dark-brown hover:border-white hover:bg-transparent"
								on:click={handleSubmit}
							>
								Save Information
							</button>
						</div>
					</form>
				{/if}
			</div>
		{/if}
	</div>
</div>
