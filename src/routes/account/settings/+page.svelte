<script>
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores';
	import { showSuccess, showError } from '$lib/utils/sweetalert';
	import { onMount } from 'svelte';
	import { shippingStore } from '$lib/stores';
	import { form } from '$app/server';

	function logout() {
		authStore.logout();
		localStorage.removeItem('token');
		showSuccess('Logged out successfully');
		setTimeout(() => {
			goto('/login');
		}, 2000);
	}
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
				<h2 class="p-4 font-bold text-gray-600 w-full">Account Overview</h2>
				<button on:click={accountOverview} class="w-auto pr-5">View</button>
			</div>

			<hr class="border-[1] border-gray-500 w-[50%]" />
			<div class="w-[50%] flex flex-row self-center">
				<h2 class="p-4 font-bold text-gray-600 w-full">Orders</h2>
				<button on:click={accountOrders} class="w-auto pr-5">View</button>
			</div>
			<hr class="border-[1] border-gray-500 w-[50%]" />
			<div class="w-[50%] flex flex-row self-center">
				<h2 class="p-4 font-bold text-gray-600 w-full">Addresses</h2>
				<button on:click={accountAddresses} class="w-auto pr-5" aria-label="View Addresses"
					>View</button
				>
			</div>
			<div class="w-[50%] pt-10 self-center">
				<h2 class="pt-2 text-gray-500">
					Need help? <a href="/contact-us" class="text-gray-500 !underline">Contact us</a>
				</h2>
				<button
					type="button"
					class="cursor-pointer p-5 w-2/4 rounded-full bg-transparent border-1 border-black text-black hover:bg-black hover:text-white hover:border-black px-4 py-2 mt-5"
					on:click={logout}
				>
					Logout
				</button>
			</div>
		</div>
	</div>

	<!-- left side -->
	<div class="grow w-full m-10 mt-0 ml-0 justify-start items-center">
		{#if accountOverviewActive}
			<p>Account Overview Content</p>
		{:else if accountOrdersActive}
			<p>Account Orders Content</p>
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
                                hover:bg-mabini-dark-brown hover:text-white hover:border-transparent"
								>DELETE</button
							>
						</div>
					</div>
					{#if isEditAddress && hasExistingShipping}
						<div class="p-15 pt-0 justify-start items-start text-left w-full gap-4 flex flex-col">
							<h2 class="w-full text-left font-bold text-2xl">EDIT INFORMATION</h2>
							<h3 class="text-red-800">* indicates required field</h3>

							<!-- svelte-ignore component_name_lowercase -->
							<form
								on:submit|preventDefault={handleUpdateButton}
								class="w-full max-w-md rounded-xl space-y-4"
							>
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
							</form>

							<div class="flex flex-row gap-30 mt-2">
								<!-- CANCEL AND SAVE ADDRESS -->
							</div>
						</div>
					{/if}
					<!-- If Delete is Clicked -->
					<!-- If Edit is clicked -->

					<!-- If Delete is Clicked -->
				{:else}
					<!-- if No address -->
					<h1 class="p-10 font-bold text-3xl text-left">You have no saved addresses.</h1>
				{/if}
			</div>
		{/if}
	</div>
</div>
