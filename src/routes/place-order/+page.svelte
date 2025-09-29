<script lang="ts">
	let email = '';
	let fname = '';
	let lname = '';
	let address = '';
	let apartment = '';
	let postalcode = '';
	let city = '';
	let country = '';
	let phone = '';
</script>

<svelte:head>
	<title>Order - Mabini Cafe</title>
	<meta name="description" content="Place your order at Mabini Cafe" />
</svelte:head>

<div class="flex min-h-[200vh]">
	<div class="flex-1 bg-black text-white flex justify-center p-8">
		<div>
			<!-- Logo -->
			<img src="/images/LOGO-4.png" alt="Logo" class="mb-2 w-[238px] h-[68px] m-auto" />
			<nav class="flex mb-10 text-sm m-auto font-semibold gap-4 items-center justify-center">
				<a href="/cart" class="text-gray-400 hover:text-white transition px-2"> Cart</a>
				<a href="/order" class="text-white px-2">Information</a>
				<a href="/shipping" class="text-gray-400 hover:text-white transition px-2">Shipping</a>
				<a href="/payment" class="text-gray-400 hover:text-white transition px-2">Payment</a>
			</nav>

			<!-- formssssszzz -->

			<div class="flex-1 text-white flex justify-center items-left">
				<form class="w-full max-w-md rounded-xl shadow-md space-y-4">
					<h2 class="text-xl font-bold mb-2">Contact Information</h2>
					<h3 class="text-red-800">* indicates required field</h3>
					<div>
						<input
							id="email"
							type="email"
							bind:value={email}
							required
							class="w-full border rounded-xl px-3 py-2"
							placeholder="Email"
						/>
					</div>
					<div class="flex gap-4">
						<div class="flex-1">
							<input
								id="fname"
								type="text"
								bind:value={fname}
								required
								class="w-full border rounded-xl px-3 py-2"
								placeholder="First Name *"
							/>
						</div>
						<div class="flex-1">
							<input
								id="lname"
								type="text"
								bind:value={lname}
								required
								class="w-full border rounded-xl px-3 py-2"
								placeholder="Last Name *"
							/>
						</div>
					</div>
					<div>
						<h2 class="text-xl font-bold mb-2 text-white">Delivery Address</h2>
						<input
							id="address"
							type="text"
							bind:value={address}
							required
							class="w-full border rounded-xl px-3 py-2"
							placeholder="Address *"
						/>
					</div>
					<div>
						<input
							id="apartment"
							type="text"
							bind:value={apartment}
							class="w-full border rounded-xl px-3 py-2"
							placeholder="Apartment, suite, etc. (optional)"
						/>
					</div>
					<div class="flex gap-4">
						<div class="flex-1">
							<input
								id="postalcode"
								type="text"
								bind:value={postalcode}
								required
								class="w-full border rounded-xl px-3 py-2"
								placeholder="Postal Code *"
							/>
						</div>
						<div class="flex-1">
							<input
								id="city"
								type="text"
								bind:value={city}
								required
								class="w-full border rounded-xl px-3 py-2"
								placeholder="City *"
							/>
						</div>
					</div>
					<div>
						<input
							id="country"
							type="text"
							bind:value={country}
							required
							class="w-full border rounded-xl px-3 py-2"
							placeholder="Country *"
						/>
					</div>
					<div>
						<input
							id="phone"
							type="tel"
							bind:value={phone}
							required
							class="w-full border rounded-xl px-3 py-2"
							placeholder="Phone *"
						/>
					</div>
					<div class="flex flex-row gap-25 mt-2">
						<a
							href="/cart"
							class="cursor-pointer w-auto px-6 py-3 rounded-full text-white hover:bg-transparent hover:border-1 hover:border-white transition text-sm"
						>
							Return to Cart
						</a>
						<button
							type="submit"
							class="cursor-pointer w-auto px-6 py-3 bg-mabini-dark-brown text-white rounded-full font-medium border border-transparent hover:bg-transparent hover:border-white transition text-sm"
						>
							Continue to Shipping
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="flex-1 bg-white text-black flex justify-center p-8">
		<div>
			<h2 class="text-2xl font-bold mb-4">Items</h2>
			<p>Cart Items</p>
		</div>
	</div>
</div>
