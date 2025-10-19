<script>
	import { form } from '$app/server';
	import { onMount } from 'svelte';
	let selectedTab = $state('customize');

	let currentDate = new Date();
	onMount(() => {
		currentDate = new Date();
	});

	// Format date Month-Day-Year

	const formattedDate = new Intl.DateTimeFormat('en-US', {
		year: 'numeric',
		month: 'long',
		day: 'numeric'
	});
</script>

<div class="flex h-screen">
	<!-- Black bg -->
	<div class="w-[30%] h-fit bg-black text-white items-center text-center">
		<!-- Logo -->
		<div class="flex justify-center p-10 pt-20">
			<img src="/admin/logo.svg" alt="Logo" class="max-w-full max-h-full" />
		</div>
		<!-- Icons -->
		<!-- Icon 1 -->
		<div class="flex justify-start items-center p-10 pb-2">
			<button
				type="button"
				class="w-full justify-start items-center flex flex-row gap-7 cursor-pointer active:border-1 active:border-white active:rounded-md p-2"
				aria-label="Customize"
				onclick={() => (selectedTab = 'customize')}
			>
				<img src="/admin/overview.svg" alt="Customize Icon" class="w-10 h-10" />
				<span class="flex flex-col items-center justify-center text-center text-lg">Customize</span>
			</button>
		</div>
		<!-- Icon 2 -->
		<div class="flex justify-start items-center pb-2 pt-0 p-10">
			<button
				type="button"
				class="w-full justify-start items-center flex flex-row gap-7 cursor-pointer active:border-1 active:border-white active:rounded-md p-2"
				aria-label="Orders"
				onclick={() => (selectedTab = 'orders')}
			>
				<img src="/admin/orders.svg" alt="Users Icon" class="w-10 h-10" />
				<span class="flex flex-col items-center justify-center text-center text-lg"> Orders </span>
			</button>
		</div>
		<!-- Icon 3 -->
		<div class="flex justify-start items-center pb-2 pt-0 p-10">
			<button
				type="button"
				class="w-full justify-start items-center flex flex-row gap-7 cursor-pointer active:border-1 active:border-white active:rounded-md p-2"
				aria-label="Products"
				onclick={() => (selectedTab = 'products')}
			>
				<img src="/admin/products.svg" alt="Products Icon" class="w-10 h-10" />
				<span class="flex flex-col items-center justify-center text-center text-lg">Products</span>
			</button>
		</div>
		<!-- Logout -->
		<div class="flex justify-start items-center mt-100 p-10">
			<button
				type="button"
				class="w-full justify-start items-center flex flex-row gap-7 cursor-pointer active:border-1 active:border-white active:rounded-md p-2"
				aria-label="Logout"
				onclick={() => (selectedTab = 'logout')}
			>
				<img src="/admin/logout.svg" alt="Logout Icon" class="w-10 h-10" />
				<span class="flex flex-col items-center justify-center text-center text-lg">Logout</span>
			</button>
		</div>
	</div>

	<!-- white bg -->
	<div class="w-[60%] h-full bg-white">
		{#if selectedTab === 'customize'}
			<!-- Customize Content -->
			<div class="ml-20 flex gap-0 flex-col justify-center items-center p-10 w-full h-full">
				<div class="w-full h-full justify-center items-center rounded-2xl shadow-lg mt-10">
					<!-- Black Header -->
					<div class="w-full h-[10%] bg-black rounded-t-2xl">
						<!-- Title -->
						<h1 class="text-white text-2xl p-5 text-start">Customize Website</h1>
					</div>
					<!-- White Content with border -->
					<div class="w-full h-[90%] rounded-b-2xl">
						<p class="p-3 text-end text-gray-500">Date: {formattedDate.format(currentDate)}</p>

						<!-- Pick a theme color -->
						<!-- Circle with Gray thingy -->
					</div>
				</div>
			</div>
		{:else if selectedTab === 'orders'}
			<div class="ml-20 flex gap-0 flex-col justify-center items-center p-10 w-full h-full">
				<!-- Orders Content -->
				<div class="w-full h-full justify-center items-center rounded-2xl shadow-lg mt-10">
					<!-- Black Header -->
					<div class="w-full h-[10%] bg-black rounded-t-2xl">
						<!-- Title -->
						<h1 class="text-white text-2xl p-5 text-start">Orders Section</h1>
					</div>
					<!-- White Content with border -->
					<div class="w-full h-[90%] rounded-b-2xl">
						<p class="p-3 text-end text-gray-500">Date: {formattedDate.format(currentDate)}</p>

						<!-- Table Header -->
						<div
							class="m-5 mb-0 mt-0 rounded-lg bg-gray-300 flex items-stretch text-center justify-between"
						>
							<div class="w-1/6 flex items-center justify-center">
								<h1 class="font-bold uppercase p-3 text-sm whitespace-nowrap">Order ID</h1>
							</div>
							<div class="w-1/6 flex items-center justify-center">
								<h1 class="font-bold uppercase p-3 text-sm whitespace-nowrap">Customer Name</h1>
							</div>
							<div class="w-1/6 flex items-center justify-center">
								<h1 class="font-bold uppercase p-3 text-sm whitespace-nowrap">Total</h1>
							</div>
							<div class="w-1/6 flex items-center justify-center">
								<h1 class="font-bold uppercase p-3 text-sm whitespace-nowrap">Time Ordered</h1>
							</div>
							<div class="w-1/6 flex items-center justify-center">
								<h1 class="font-bold uppercase p-3 text-sm whitespace-nowrap">Status</h1>
							</div>
							<div class="w-1/6 flex items-center justify-center"></div>
						</div>

						<!-- make a for each for orders -->
						<div class="w-full flex flex-col">
							<!-- Example row, repeat for each order -->
							<div
								class="m-5 mt-0 flex items-center text-center justify-between border-b border-gray-200"
							>
								<div class="w-1/6 flex items-center justify-center">
									<span class="p-3 text-sm">#12345</span>
								</div>
								<div class="w-1/6 flex items-center justify-center">
									<span class="p-3 text-sm">John Doe</span>
								</div>
								<div class="w-1/6 flex items-center justify-center">
									<span class="p-3 text-sm">$25.00</span>
								</div>
								<div class="w-1/6 flex items-center justify-center">
									<span class="p-3 text-sm">10:30 AM</span>
								</div>
								<div class="w-1/6 flex items-center justify-center">
									<span class="p-3 text-sm">
										<select
											name="status"
											id="status"
											class="border border-gray-300 rounded-lg px-2 py-1"
										>
											<option value="pending">Pending</option>
											<option value="completed">Completed</option>
											<option value="canceled">Canceled</option>
										</select>
									</span>
								</div>
								<div class="w-1/6 flex items-center justify-center">
									<div class="p-3 text-sm">
										<button class="bg-mabini-yellow text-white px-4 py-2 rounded-lg cursor-pointer">
											View
										</button>
									</div>
								</div>
							</div>
							<!-- Add more rows as needed -->
						</div>
					</div>
				</div>
			</div>
		{:else if selectedTab === 'products'}
			<div class="ml-20 flex gap-0 flex-col justify-center items-center p-10 w-full h-full">
				<!-- Products Content -->
				<div class="w-full h-full justify-center items-center rounded-2xl shadow-lg mt-10">
					<!-- Black Header -->
					<div class="w-full h-[10%] bg-black rounded-t-2xl">
						<!-- Title -->
						<h1 class="text-white text-2xl p-5 text-start">Products Section</h1>
					</div>
					<!-- White Content with border -->
					<div class="w-full h-[90%] rounded-b-2xl">
						<div class="p-5">
							<p>Fill out the details to create a new product entry for your menu.</p>
							<p class="text-red-600">* indicates required fields</p>
							<!-- Form -->
							<!-- svelte-ignore component_name_lowercase -->
							<form class="flex flex-col gap-4 mt-4 pb-20">
								<label class="flex flex-col">
									<span class="font-bold">Product Name *</span>
									<input
										type="text"
										class="border border-gray-300 rounded-lg px-4 py-2 mt-1"
										placeholder="Enter product name"
										required
									/>
								</label>
								<label class="flex flex-col">
									<span class="font-bold">Price (₱) *</span>
									<input
										type="number"
										class="border border-gray-300 rounded-lg px-4 py-2 mt-1"
										placeholder="Enter product price"
										required
									/>
								</label>
								<label class="flex flex-col">
									<span class="font-bold">Category *</span>
									<div class="flex gap-2 mt-1">
										<select class="border border-gray-300 rounded-lg px-4 py-2 w-full" required>
											<option value="">Select category</option>
											<option value="coffee">Coffee</option>
											<option value="tea">Tea</option>
											<option value="pastry">Pastry</option>
											<!-- Add more categories as needed -->
										</select>
									</div>
								</label>
								<label class="flex flex-col">
									<span class="font-bold">Subcategory *</span>
									<div class="flex gap-2 mt-1">
										<select class="border border-gray-300 rounded-lg px-4 py-2 w-full" required>
											<option value="">Select subcategory</option>
											<option value="hot">Hot</option>
											<option value="cold">Cold</option>
											<option value="dessert">Dessert</option>
											<!-- Add more subcategories as needed -->
										</select>
									</div>
								</label>
								<label class="flex flex-col">
									<span class="font-bold">Description *</span>
									<textarea
										class="border border-gray-300 rounded-lg px-4 py-2 mt-1"
										placeholder="Enter product description"
										required
									></textarea>
								</label>
								<label class="flex flex-col">
									<span class="font-bold">Product Photo *</span>
									<input
										type="text"
										class="border border-gray-300 rounded-lg px-4 py-2 mt-1"
										placeholder="Enter image URL"
										required
									/>
								</label>
								<button
									type="submit"
									class="bg-mabini-yellow text-white px-4 py-2 rounded-lg mt-4 cursor-pointer w-full"
								>
									Add Product
								</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		{:else if selectedTab === 'logout'}
			<!-- Logout Content -->
			<div class="w-full h-full justify-center items-center rounded-2xl shadow-lg mt-10">
				<!-- Black Header -->
				<div class="w-full h-[10%] bg-black rounded-t-2xl">
					<!-- Title -->
					<h1 class="text-white text-2xl p-5 text-start">Logout Section</h1>
				</div>
				<!-- White Content with border -->
				<div class="w-full h-[90%] rounded-b-2xl">
					<p class="p-5">Logout content goes here</p>
				</div>
			</div>
		{/if}
		<!-- </div> -->
	</div>
</div>
