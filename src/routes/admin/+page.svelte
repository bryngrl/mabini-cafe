<script lang="ts">
	import { form } from '$app/server';
	import { ordersStore, orders, menuStore, menuItems } from '$lib/stores';
	import { onMount } from 'svelte';
	import { showError, showSuccess } from '$lib/utils/sweetalert';

	interface Order {
		id: number;
		user_id?: number;
		customer_name?: string;
		total_amount: number;
		order_time: string;
		status: string;
		payment_status: string;
		payment_method: string;
	}

	let selectedTab = $state('customize');
	let currentDate = $state(new Date());

	// Type-safe orders reference
	let ordersList = $derived($orders as Order[]);

	// Product form data
	let productName = $state('');
	let productPrice = $state('');
	let productCategory = $state('');
	let productDescription = $state('');
	let productImage: File | null = $state(null);
	let imagePreview = $state('');

	const categories = [
		{ id: 1, name: 'Pastries' },
		{ id: 2, name: 'Meals' },
		{ id: 3, name: 'Beverages' }
	];

	let subcategories = $derived(
		[...new Set($menuItems.map((item: any) => item.description))].filter(Boolean) as string[]
	);

	onMount(() => {
		currentDate = new Date();
	});

	onMount(async () => {
		try {
			await ordersStore.fetchAll();
			await menuStore.fetchAll();
		} catch (err) {
			console.error('Error fetching data:', err);
			showError('Failed to fetch data');
		}
	});

	async function handleStatusChange(orderId: number, newStatus: string) {
		try {
			switch (newStatus) {
				case 'preparing':
					await ordersStore.updateToPreparing(orderId);
					showSuccess('Order status updated to Preparing');
					break;
				case 'delivering':
					await ordersStore.updateToDelivering(orderId);
					showSuccess('Order status updated to Delivering');
					break;
				case 'completed':
					await ordersStore.updateToCompleted(orderId);
					showSuccess('Order status updated to Completed');
					break;
				case 'cancelled':
					await ordersStore.updateToCancelled(orderId);
					showSuccess('Order status updated to Cancelled');
					break;
			}
		} catch (err) {
			console.error('Error updating order status:', err);
			showError('Failed to update order status');
		}
	}

	function handleImageChange(e: Event) {
		const target = e.target as HTMLInputElement;
		const file = target.files?.[0];
		if (file) {
			productImage = file;
			const reader = new FileReader();
			reader.onload = (e) => {
				imagePreview = e.target?.result as string;
			};
			reader.readAsDataURL(file);
		}
	}

	async function handleProductSubmit(e: Event) {
		e.preventDefault();

		try {
			if (!productName || !productPrice || !productCategory) {
				showError('Please fill in all required fields');
				return;
			}

			if (!productImage) {
				showError('Please upload a product image');
				return;
			}

			const itemData = {
				name: productName,
				price: parseFloat(productPrice),
				category_id: parseInt(productCategory),
				description: productDescription || ''
			};

			await menuStore.create(itemData, productImage);

			await showSuccess(
				`${productName} has been added to the menu and will appear immediately!`,
				'Product Added Successfully'
			);

			productName = '';
			productPrice = '';
			productCategory = '';
			productDescription = '';
			productImage = null;
			imagePreview = '';

			const fileInput = document.querySelector('input[type="file"]') as HTMLInputElement;
			if (fileInput) fileInput.value = '';
		} catch (err) {
			console.error('Error creating product:', err);
			showError('Failed to create product. Please try again.');
		}
	}

	const formattedDate = new Intl.DateTimeFormat('en-US', {
		year: 'numeric',
		month: 'long',
		day: 'numeric'
	});
</script>

<div class="flex min-h-screen min-h-full items-stretch">
	<!-- Black bg -->
	<div class="w-[30%] min-h-full bg-black text-white flex flex-col justify-between text-center">
		<!-- Top: Logo and navigation -->
		<div>
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
		</div>
		<!-- Bottom: Logout -->
		<div class="flex items-center pb-2 pt-0 p-10">
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
	<div class="w-[60%] min-h-screen h-full bg-white">
		{#if selectedTab === 'customize'}
			<!-- Customize Content -->
			<div class="ml-20 flex gap-0 flex-col justify-center items-center p-10 w-full min-h-[400px]">
				<div class="w-full min-h-[400px] justify-center items-center rounded-2xl shadow-lg mt-10">
					<!-- Black Header -->
					<div class="w-full bg-black rounded-t-2xl">
						<!-- Title -->
						<h1 class="text-white text-2xl p-5 text-start">Customize Website</h1>
					</div>
					<!-- White Content with border -->
					<div class="w-full rounded-b-2xl">
						<p class="p-3 text-end text-gray-500">Date: {formattedDate.format(currentDate)}</p>
						<div class="p-10 pt-0">
							<!-- svelte-ignore component_name_lowercase -->
							<form action="">
								<div class="pb-5">
									<label class="flex flex-col">
										<span class="font-bold">Hero 1</span>
										<input
											type="file"
											accept="image/*"
											class="border border-gray-300 rounded-lg px-4 py-2 mt-1"
											onchange={handleImageChange}
											required
										/>
										{#if imagePreview}
											<div class="mt-2">
												<img
													src={imagePreview}
													alt="Preview"
													class="w-full max-h-52 object-cover rounded-lg"
												/>
											</div>
										{/if}
									</label>
								</div>
								<div class="pb-5">
									<label class="flex flex-col">
										<span class="font-bold">Hero 2</span>
										<input
											type="file"
											accept="image/*"
											class="border border-gray-300 rounded-lg px-4 py-2 mt-1"
											onchange={handleImageChange}
											required
										/>
										{#if imagePreview}
											<div class="mt-2">
												<img
													src={imagePreview}
													alt="Preview"
													class="w-full max-h-52 object-cover rounded-lg"
												/>
											</div>
										{/if}
									</label>
								</div>
								<div class="pb-5">
									<label class="flex flex-col">
										<span class="font-bold">Hero 3</span>
										<input
											type="file"
											accept="image/*"
											class="border border-gray-300 rounded-lg px-4 py-2 mt-1"
											onchange={handleImageChange}
											required
										/>
										{#if imagePreview}
											<div class="mt-2">
												<img
													src={imagePreview}
													alt="Preview"
													class="w-full max-h-52 object-cover rounded-lg"
												/>
											</div>
										{/if}
									</label>
								</div>
							</form>
						</div>

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
							{#each ordersList as order (order.id)}
								<div
									class="m-5 mt-0 flex items-center text-center justify-between border-b border-gray-200"
								>
									<div class="w-1/6 flex items-center justify-center">
										<span class="p-3 text-sm text-black">{order.id}</span>
									</div>
									<div class="w-1/6 flex items-center justify-center">
										<span class="p-3 text-sm">{order.customer_name || 'N/A'}</span>
									</div>
									<div class="w-1/6 flex items-center justify-center">
										<span class="p-3 text-sm">₱{order.total_amount}</span>
									</div>
									<div class="w-1/6 flex items-center justify-center">
										<span class="p-3 text-sm">
											{order.order_time
												? new Date(order.order_time.replace(' ', 'T')).toLocaleDateString('en-US', {
														month: 'short',
														day: 'numeric',
														year: 'numeric'
													})
												: 'N/A'}
										</span>
									</div>
									<div class="w-1/6 flex items-center justify-center">
										<span class="p-3 text-sm">
											<select
												name="status"
												id="status-{order.id}"
												class="border border-gray-300 rounded-lg px-2 py-1"
												value={order.status}
												onchange={(e) => {
													const target = e.target as HTMLSelectElement;
													handleStatusChange(order.id, target.value);
												}}
											>
												<option value="pending">Pending</option>
												<option value="preparing">Preparing</option>
												<option value="delivering">Delivering</option>
												<option value="completed">Completed</option>
												<option value="cancelled">Cancelled</option>
											</select>
										</span>
									</div>
									<div class="w-1/6 flex items-center justify-center">
										<div class="p-3 text-sm">
											<button
												class="bg-mabini-yellow text-white px-4 py-2 rounded-lg cursor-pointer"
												onclick={() => {
													// TODO: Implement view order details
													console.log('View order', order.id);
												}}
											>
												View
											</button>
										</div>
									</div>
								</div>
							{:else}
								<div class="m-5 mt-0 text-center p-10">
									<p class="text-gray-500">No orders found</p>
								</div>
							{/each}
						</div>
					</div>
				</div>
			</div>
		{:else if selectedTab === 'products'}
			<div class="ml-20 flex gap-0 flex-col justify-center items-center p-10 w-full h</div>-full">
				<!-- Products Content -->
				<div class="w-full h-full justify-center items-center rounded-2xl shadow-lg mt-10">
					<!-- Black Header -->
					<div class="w-full min-h-[10%] bg-black rounded-t-2xl">
						<!-- Title -->
						<h1 class="text-white text-2xl p-5 text-start">Products Section</h1>
					</div>
					<!-- White Content with border -->
					<div class="w-full rounded-b-2xl flex items-center justify">
						<div class="p-5 m-5">
							<h2 class="text-lg font-bold">
								Fill out the details to create a new product entry for your menu.
							</h2>
							<p class="text-red-600">* indicates required fields</p>
							<p class="text-gray-600 mt-2 text-sm">
								<strong>Note:</strong> Adding a product here will automatically shows up in your Menu
								page so make sure that all the details are correct and accurate.
							</p>
							<!-- Form -->
							<form class="flex flex-col gap-4 mt-4 pb-20" onsubmit={handleProductSubmit}>
								<label class="flex flex-col">
									<span class="font-bold">Product Name *</span>
									<input
										type="text"
										class="border border-gray-300 rounded-lg px-4 py-2 mt-1"
										placeholder="e.g., Caramel Macchiato"
										bind:value={productName}
										required
									/>
								</label>
								<label class="flex flex-col">
									<span class="font-bold">Price (₱) *</span>
									<input
										type="number"
										step="0.01"
										min="0"
										class="border border-gray-300 rounded-lg px-4 py-2 mt-1"
										placeholder="e.g., 150.00"
										bind:value={productPrice}
										required
									/>
								</label>
								<label class="flex flex-col">
									<span class="font-bold">Main Category *</span>
									<div class="flex gap-2 mt-1">
										<select
											class="border border-gray-300 rounded-lg px-4 py-2 w-full"
											bind:value={productCategory}
											required
										>
											<option value="">Select main category</option>
											{#each categories as category}
												<option value={category.id}>{category.name}</option>
											{/each}
										</select>
									</div>
								</label>
								<label class="flex flex-col">
									<span class="font-bold">Subcategory </span>
									<div class="flex gap-2 mt-1">
										<select
											class="border border-gray-300 rounded-lg px-4 py-2 w-full"
											bind:value={productDescription}
										>
											<option value="">None (optional)</option>
											{#each subcategories as sub}
												<option value={sub}>{sub}</option>
											{/each}
										</select>
									</div>
									<p class="text-sm text-gray-500 mt-1">
										Optional: Adds a filter option in the sidebar (e.g., Savory Waffle, Hot, Iced)
									</p>
								</label>
								<label class="flex flex-col">
									<span class="font-bold">Product Photo *</span>
									<input
										type="file"
										accept="image/*"
										class="border border-gray-300 rounded-lg px-4 py-2 mt-1"
										onchange={handleImageChange}
										required
									/>
									{#if imagePreview}
										<div class="mt-2">
											<img
												src={imagePreview}
												alt="Preview"
												class="w-32 h-32 object-cover rounded-lg"
											/>
										</div>
									{/if}
								</label>
								<button
									type="submit"
									class="bg-mabini-yellow text-white px-4 py-2 rounded-lg mt-4 cursor-pointer w-full hover:bg-yellow-600"
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
