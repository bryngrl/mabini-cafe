<script lang="ts">
	import {
		ordersStore,
		orders,
		menuStore,
		menuItems,
		customizeStore,
		customizations
	} from '$lib/stores';
	import { authStore } from '$lib/stores/auth';
	import { goto } from '$app/navigation';
	import { onMount } from 'svelte';
	import { showConfirm, showError, showSuccess } from '$lib/utils/sweetalert';
	import ViewItemModal from '$lib/components/ui/ViewItemModal.svelte';
	import AdminItemModal from '$lib/components/ui/AdminItemModal.svelte';

	interface Order {
		id: number;
		user_id?: number;
		customer_name?: string;
		total_amount: number;
		order_time: string;
		status: string;
		payment_status: string;
		payment_method: string;
		items?: any[];
		shipping_address?: string;
		message?: string;
		shipping_name?: string;
		shipping_fee?: number;
		shipping_fee_id?: number;
	}
	async function isAvailableUpdate(product: any) {
		try {
			// Toggle the availability: if currently available (true/1), make unavailable (false/0), and vice versa
			const currentAvailability = product.isAvailable ?? true;
			const newAvailability = !currentAvailability;

			// Ensure name and price are properly formatted
			if (!product.name || !product.price) {
				console.error('Missing name or price:', { name: product.name, price: product.price });
				await showError('Product data is incomplete. Please refresh the page and try again.');
				return;
			}

			// Update with all required fields (backend needs name, price, etc.)
			const updateData: any = {
				name: String(product.name),
				price: Number(product.price),
				description: product.description || '',
				isAvailable: newAvailability ? 1 : 0 // Convert boolean to 0 or 1 for backend
			};

			// Only include category_id if it exists in the product object
			if (product.category_id) {
				updateData.category_id = Number(product.category_id);
			}

			await menuStore.update(product.id, updateData);

			await showSuccess(
				`${product.name} is now ${newAvailability ? 'Available' : 'Unavailable'}`,
				'Success'
			);
		} catch (err) {
			console.error('Error updating availability:', err);
			await showError('Failed to update product availability. Please try again.');
		}
	}

	let selectedTab = $state('customize');
	let currentDate = $state(new Date());
	let selectedOrder: Order | null = $state(null);
	let showOrderModal = $state(false);
	let selectedProduct: any = $state(null);
	let showProductModal = $state(false);
	let searchQuery = $state('');
	let mobileMenuOpen = $state(false);

	function toggleMobileMenu() {
		mobileMenuOpen = !mobileMenuOpen;
	}

	function selectTab(tab: string) {
		selectedTab = tab;
		mobileMenuOpen = false;
	}

	// Pagination state
	let ordersPage = $state(1);
	let productsPage = $state(1);
	const itemsPerPage = 10;

	// Type-safe orders reference
	let ordersList = $derived($orders as Order[]);

	// Product form data
	let productName = $state('');
	let productPrice = $state('');
	let productCategory = $state('');
	let productDescription = $state('');
	let productImage: File | null = $state(null);
	let imagePreview = $state('');

	// Hero images for customize section
	let hero1Image: File | null = $state(null);
	let hero2Image: File | null = $state(null);
	let hero3Image: File | null = $state(null);
	let hero1Preview = $state('');
	let hero2Preview = $state('');
	let hero3Preview = $state('');

	const categories = [
		{ id: 1, name: 'Pastries' },
		{ id: 2, name: 'Meals' },
		{ id: 3, name: 'Beverages' }
	];

	let subcategories = $derived(
		[...new Set($menuItems.map((item: any) => item.description))].filter(Boolean) as string[]
	);

	// Filtered products for Product Manager
	let filteredProducts = $derived(
		$menuItems
			.filter((item: any) => item.name.toLowerCase().includes(searchQuery.toLowerCase()))
			.sort((a: any, b: any) => {
				// Sort: available items first, unavailable items last
				const aAvailable = a.isAvailable ?? a.is_available ?? true;
				const bAvailable = b.isAvailable ?? b.is_available ?? true;

				if (aAvailable === bAvailable) return 0;
				return aAvailable ? -1 : 1;
			})
	);

	// Paginated orders
	let paginatedOrders = $derived.by(() => {
		const startIndex = (ordersPage - 1) * itemsPerPage;
		const endIndex = startIndex + itemsPerPage;
		return ordersList.slice(startIndex, endIndex);
	});

	let totalOrdersPages = $derived(Math.ceil(ordersList.length / itemsPerPage));

	// Paginated products
	let paginatedProducts = $derived.by(() => {
		const startIndex = (productsPage - 1) * itemsPerPage;
		const endIndex = startIndex + itemsPerPage;
		return filteredProducts.slice(startIndex, endIndex);
	});

	let totalProductsPages = $derived(Math.ceil(filteredProducts.length / itemsPerPage));

	// Helper function to generate page numbers
	function getPageNumbers(currentPage: number, totalPages: number): (number | string)[] {
		if (totalPages <= 7) {
			return Array.from({ length: totalPages }, (_, i) => i + 1);
		}

		if (currentPage <= 3) {
			return [1, 2, 3, 4, '...', totalPages];
		}

		if (currentPage >= totalPages - 2) {
			return [1, '...', totalPages - 3, totalPages - 2, totalPages - 1, totalPages];
		}

		return [1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages];
	}

	onMount(async () => {
		currentDate = new Date();
		try {
			await Promise.all([ordersStore.fetchAll(), menuStore.fetchAll(), customizeStore.fetchAll()]);
		} catch (err) {
			console.error(err);
			showError('Failed to fetch data');
		}
	});

	// Reset to page 1 when search query changes
	$effect(() => {
		searchQuery;
		productsPage = 1;
	});

	// Inside admin.svelte <script> block

	async function handleStatusChange(orderId: number, status: string) {
		try {
			switch (status.toLowerCase()) {
				case 'preparing':
					await ordersStore.updateToPreparing(orderId);
					break;
				case 'delivering':
					await ordersStore.updateToDelivering(orderId);
					break;
				case 'completed':
					await ordersStore.updateToCompleted(orderId);
					break;
				case 'cancelled':
					await ordersStore.updateToCancelled(orderId);
					break;
			}
			await ordersStore.fetchAll();
			await showSuccess(`Order #${orderId} status updated to ${status.toUpperCase()}`, 'Success');
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

	function handleHeroImageChange(e: Event, heroNumber: number) {
		const target = e.target as HTMLInputElement;
		const file = target.files?.[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = (e) => {
				const preview = e.target?.result as string;
				if (heroNumber === 1) {
					hero1Image = file;
					hero1Preview = preview;
				} else if (heroNumber === 2) {
					hero2Image = file;
					hero2Preview = preview;
				} else if (heroNumber === 3) {
					hero3Image = file;
					hero3Preview = preview;
				}
			};
			reader.readAsDataURL(file);
		}
	}

	async function handleHeroImagesSubmit(e: Event) {
		e.preventDefault();
		try {
			const heroImages = [
				{ name: 'hero1', file: hero1Image },
				{ name: 'hero2', file: hero2Image },
				{ name: 'hero3', file: hero3Image }
			];

			for (const { name, file } of heroImages) {
				if (file) {
					// Check if this hero image already exists
					const existingHero = $customizations.find((c: any) => c.image_custom_name === name);

					if (existingHero) {
						// Update existing hero image
						await customizeStore.updateImage(existingHero.id, file);
					} else {
						// Create new hero image
						await customizeStore.create({ image_custom_name: name }, file);
					}
				}
			}

			await showSuccess('Hero images updated successfully!');
			hero1Image = null;
			hero2Image = null;
			hero3Image = null;
			hero1Preview = '';
			hero2Preview = '';
			hero3Preview = '';

			// Clear file inputs
			const fileInputs = document.querySelectorAll('input[type="file"]');
			fileInputs.forEach((input: any) => {
				if (input.accept === 'image/*') input.value = '';
			});
		} catch (err) {
			console.error('Error updating hero images:', err);
			await showError('Failed to update hero images. Please try again.');
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

	async function handleLogout() {
		try {
			await showConfirm('Do you want you to logout?', 'Logout');
			authStore.logout();
			await showSuccess('Logged out successfully', 'Goodbye!');
			setTimeout(() => {
				goto('/login');
			}, 1500);
		} catch (err) {
			console.error('Error logging out:', err);
			await showError('Failed to logout. Please try again.');
		}
	}

	function viewOrderDetails(order: Order) {
		selectedOrder = order;
		showOrderModal = true;
	}
	$effect(() => {
		// 1. Check if the modal is open and an order is selected
		if (showOrderModal && selectedOrder) {
			// 2. Find the *latest* version of this order in the global $orders store
			const latestOrder = ordersList.find((o: Order) => o.id === selectedOrder?.id);

			// 3. If a latest version is found and it's different from what we have, update the state
			if (latestOrder && JSON.stringify(latestOrder) !== JSON.stringify(selectedOrder)) {
				selectedOrder = latestOrder;
			}
		}
	});

	function closeOrderModal() {
		showOrderModal = false;
		selectedOrder = null;
	}

	function openProductModal(product: any) {
		selectedProduct = product;
		showProductModal = true;
	}

	function closeProductModal() {
		showProductModal = false;
		selectedProduct = null;
	}
</script>

<svelte:head>
	<title>Admin - Mabini Cafe</title>
	<meta name="description" content="Admin Page for Mabini Cafe" />
</svelte:head>
<div class="flex flex-col lg:flex-row min-h-screen items-stretch">
	{#if mobileMenuOpen}
		<!-- svelte-ignore a11y_click_events_have_key_events -->
		<!-- svelte-ignore a11y_no_static_element_interactions -->
		<div class="fixed inset-0 bg-opacity-50 z-1 lg:hidden" onclick={toggleMobileMenu}></div>
	{/if}

	<div
		class="fixed lg:static top-0 left-0 w-full sm:w-[80%] lg:w-[30%] h-screen lg:h-auto lg:min-h-screen bg-black text-white flex flex-col justify-between text-center z-50 transform transition-transform duration-300 ease-in-out {mobileMenuOpen
			? 'translate-x-0'
			: '-translate-x-full lg:translate-x-0'}"
	>
		<!-- Top: Logo and navigation -->
		<div>
			<!-- Mobile Exit Button inside sidebar -->
			<div class="lg:hidden flex justify-end p-4">
				<button
					type="button"
					onclick={toggleMobileMenu}
					class="text-white p-2 hover:bg-gray-800 rounded-full transition-colors"
					aria-label="Close menu"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						fill="none"
						viewBox="0 0 24 24"
						stroke-width="2"
						stroke="currentColor"
						class="w-6 h-6"
					>
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
			</div>
			<!-- Logo - Now visible on all screens -->
			<div class="flex justify-center p-6 lg:p-10 pt-2 lg:pt-20">
				<img src="/admin/logo.svg" alt="Logo" class="max-w-full max-h-full w-32 lg:w-auto" />
			</div>
			<!-- Icons -->
			<!-- Icon 1 -->
			<div class="flex justify-start items-center p-10 pb-2">
				<button
					type="button"
					class="w-full justify-start items-center flex flex-row gap-7 cursor-pointer active:border-1 active:border-white active:rounded-md p-2"
					aria-label="Customize"
					onclick={() => selectTab('customize')}
				>
					<img src="/admin/overview.svg" alt="Customize Icon" class="w-10 h-10" />
					<span class="flex flex-col items-center justify-center text-center text-lg"
						>Customize</span
					>
				</button>
			</div>
			<!-- Icon 2 -->
			<div class="flex justify-start items-center pb-2 pt-0 p-10">
				<button
					type="button"
					class="w-full justify-start items-center flex flex-row gap-7 cursor-pointer active:border-1 active:border-white active:rounded-md p-2"
					aria-label="Orders"
					onclick={() => selectTab('orders')}
				>
					<img src="/admin/orders.svg" alt="Users Icon" class="w-10 h-10" />
					<span class="flex flex-col items-center justify-center text-center text-lg">
						Orders
					</span>
				</button>
			</div>
			<!-- Icon 3 -->
			<div class="flex justify-start items-center pb-2 pt-0 p-10">
				<button
					type="button"
					class="w-full justify-start items-center flex flex-row gap-7 cursor-pointer active:border-1 active:border-white active:rounded-md p-2"
					aria-label="Products"
					onclick={() => selectTab('products')}
				>
					<img src="/admin/products.svg" alt="Products Icon" class="w-10 h-10" />
					<span class="flex flex-col items-center justify-center text-center text-lg"
						>Add a Product</span
					>
				</button>
			</div>
			<!-- Icon 4 - Product Manager -->
			<div class="flex justify-start items-center pb-2 pt-0 p-10">
				<button
					type="button"
					class="w-full justify-start items-center flex flex-row gap-7 cursor-pointer active:border-1 active:border-white active:rounded-md p-2"
					aria-label="Product Manager"
					onclick={() => selectTab('product-manager')}
				>
					<img src="/admin/products.svg" alt="Product Manager Icon" class="w-10 h-10" />
					<span class="flex flex-col items-center justify-center text-center text-lg"
						>Product Manager</span
					>
				</button>
			</div>
		</div>
		<!-- Bottom: Logout -->
		<div class="flex items-center pb-2 pt-0 p-10">
			<button
				type="button"
				class="w-full justify-start items-center flex flex-row gap-7 cursor-pointer active:border-1 active:border-white active:rounded-md p-2"
				aria-label="Logout"
				onclick={handleLogout}
			>
				<img src="/admin/logout.svg" alt="Logout Icon" class="w-10 h-10" />
				<span class="flex flex-col items-center justify-center text-center text-lg">Logout</span>
			</button>
		</div>
	</div>
	<!-- white bg -->
	<div
		class="w-full lg:w-[60%] min-h-screen h-full bg-white transition-all durati on-300
    {showOrderModal || showProductModal ? 'blur-sm pointer-events-none' : ''}"
	>
		{#if selectedTab === 'customize'}
			<!-- Customize Content -->
			<div
				class="px-4 sm:px-8 lg:ml-20 flex gap-0 flex-col justify-center items-center p-4 sm:p-10 w-full h-full"
			>
				<div class="w-full min-h-[400px] justify-center items-center rounded-2xl shadow-lg mt-10">
					<!-- Black Header with Hamburger -->
					<div
						class="w-full bg-black rounded-t-2xl flex justify-between items-center
    {mobileMenuOpen ? 'hidden' : 'flex'} lg:flex"
					>
						<!-- Mobile Hamburger Button -->
						<button
							type="button"
							onclick={toggleMobileMenu}
							class="lg:hidden text-white p-5"
							aria-label="Toggle menu"
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
								stroke-width="2"
								stroke="currentColor"
								class="w-6 h-6"
							>
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									d={mobileMenuOpen
										? 'M6 18L18 6M6 6l12 12'
										: 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5'}
								/>
							</svg>
						</button>
						<!-- Title - Hidden when sidebar is open on mobile -->
						<h1
							class="text-white text-2xl p-5 text-start flex-1 {mobileMenuOpen
								? 'lg:block hidden'
								: ''}"
						>
							Customize Website
						</h1>
					</div>
					<!-- White Content with border -->
					<div class="w-full rounded-b-2xl p-5">
						<div class="flex justify-between items-start mb-4">
							<p class="text-gray-600 flex-1">Customize your website's hero images and theme.</p>
						</div>
						<!-- svelte-ignore component_name_lowercase -->
						<form onsubmit={handleHeroImagesSubmit}>
							<div class="pb-5">
								<label class="flex flex-col">
									<span class="font-bold">Hero 1</span>
									<input
										type="file"
										accept="image/*"
										class="border border-gray-300 rounded-lg px-4 py-2 mt-1"
										onchange={(e) => handleHeroImageChange(e, 1)}
									/>
									{#if hero1Preview}
										<div class="mt-2">
											<img
												src={hero1Preview}
												alt="Hero 1 Preview"
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
										onchange={(e) => handleHeroImageChange(e, 2)}
									/>
									{#if hero2Preview}
										<div class="mt-2">
											<img
												src={hero2Preview}
												alt="Hero 2 Preview"
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
										onchange={(e) => handleHeroImageChange(e, 3)}
									/>
									{#if hero3Preview}
										<div class="mt-2">
											<img
												src={hero3Preview}
												alt="Hero 3 Preview"
												class="w-full max-h-52 object-cover rounded-lg"
											/>
										</div>
									{/if}
								</label>
							</div>
							<button
								type="submit"
								class="bg-mabini-yellow text-white px-4 py-2 rounded-lg mt-4 cursor-pointer w-full hover:bg-yellow-600"
							>
								Update Hero Images
							</button>
						</form>
					</div>
				</div>
			</div>
			<!-- </div> -->
		{:else if selectedTab === 'orders'}
			<div
				class="px-4 sm:px-8 lg:ml-20 flex gap-0 flex-col justify-center items-center p-4 sm:p-10 w-full h-full"
			>
				<!-- Orders Content -->
				<div class="w-full h-full justify-center items-center rounded-2xl shadow-lg mt-10">
					<!-- Black Header with Hamburger -->
					<div
						class="w-full h-[10%] bg-black rounded-t-2xl flex justify-between items-center {mobileMenuOpen
							? 'hidden'
							: 'flex'} lg:flex"
					>
						<!-- Mobile Hamburger Button -->
						<button
							type="button"
							onclick={toggleMobileMenu}
							class="lg:hidden text-white p-5"
							aria-label="Toggle menu"
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
								stroke-width="2"
								stroke="currentColor"
								class="w-6 h-6"
							>
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									d={mobileMenuOpen
										? 'M6 18L18 6M6 6l12 12'
										: 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5'}
								/>
							</svg>
						</button>
						<!-- Title - Hidden when sidebar is open on mobile -->
						<h1
							class="text-white text-2xl p-5 text-start flex-1 {mobileMenuOpen
								? 'lg:block hidden'
								: ''}"
						>
							Orders Section
						</h1>
					</div>
					<!-- White Content with border -->
					<div class="w-full h-[90%] rounded-b-2xl overflow-x-auto">
						<div class="px-5 pt-5 pb-4 flex justify-between items-start">
							<p class="text-gray-600 flex-1">
								View and manage all customer orders. Update order status and view detailed
								information.
							</p>
							<p class="text-gray-500 text-sm whitespace-nowrap ml-4">
								Date: {formattedDate.format(currentDate)}
							</p>
						</div>

						<!-- Table Header -->

						<div
							class="m-5 mb-0 mt-0 rounded-lg bg-gray-300 flex items-stretch text-center justify-between min-w-[600px]"
						>
							<div class="w-1/4 flex items-center justify-center">
								<h1 class="font-bold uppercase p-3 text-sm whitespace-nowrap">Order ID</h1>
							</div>
							<div class="w-1/4 flex items-center justify-center">
								<h1 class="font-bold uppercase p-3 text-sm whitespace-nowrap">Customer Name</h1>
							</div>
							<div class="w-1/4 flex items-center justify-center">
								<h1 class="font-bold uppercase p-3 text-sm whitespace-nowrap">Status</h1>
							</div>
							<div class="w-1/4 flex items-center justify-center">
								<h1 class="font-bold uppercase p-3 text-sm whitespace-nowrap">Action</h1>
							</div>
						</div>

						<!-- make a for each for orders -->
						<div class="w-full flex flex-col min-w-[600px]">
							{#each paginatedOrders as order (order.id)}
								<div
									class="m-5 mt-0 flex items-center text-center justify-between border-b border-gray-200"
								>
									<div class="w-1/4 flex items-center justify-center">
										<span class="p-3 text-sm text-black">{order.id}</span>
									</div>
									<div class="w-1/4 flex items-center justify-center">
										<span class="p-3 text-sm">{order.customer_name || 'N/A'}</span>
									</div>
									<div class="w-1/4 flex items-center justify-center">
										<span class="p-3 text-sm">
											<select
												onchange={(e) =>
													handleStatusChange(order.id, (e.target as HTMLSelectElement).value)}
												class="border border-gray-300 rounded-lg px-2 py-1 capitalize text-xs sm:text-sm"
											>
												{#if !['pending', 'preparing', 'delivering', 'completed', 'cancelled'].includes(order.status?.toLowerCase()) && order.status}
													<option value={order.status} selected disabled>{order.status}</option>
												{/if}
												<option value="pending" selected={order.status?.toLowerCase() === 'pending'}
													>Pending</option
												>
												<option
													value="preparing"
													selected={order.status?.toLowerCase() === 'preparing'}>Preparing</option
												>
												<option
													value="delivering"
													selected={order.status?.toLowerCase() === 'delivering'}>Delivering</option
												>
												<option
													value="completed"
													selected={order.status?.toLowerCase() === 'completed'}>Completed</option
												>
												<option
													value="cancelled"
													selected={order.status?.toLowerCase() === 'cancelled'}>Cancelled</option
												>
											</select>
										</span>
									</div>
									<div class="w-1/4 flex items-center justify-center">
										<div class="p-3 text-sm">
											<button
												class="bg-mabini-yellow text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg cursor-pointer text-xs sm:text-sm"
												onclick={() => viewOrderDetails(order)}
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

						<!-- Pagination Controls -->
						{#if totalOrdersPages > 1}
							<div class="flex items-center justify-center gap-2 p-5 border-t">
								<button
									onclick={() => (ordersPage = Math.max(1, ordersPage - 1))}
									disabled={ordersPage === 1}
									class="px-3 py-1 rounded-lg border {ordersPage === 1
										? 'bg-gray-100 text-gray-400 cursor-not-allowed'
										: 'bg-white text-gray-700 hover:bg-gray-50'}"
								>
									Previous
								</button>

								{#each getPageNumbers(ordersPage, totalOrdersPages) as pageNum}
									{#if pageNum === '...'}
										<span class="px-2 text-gray-400">...</span>
									{:else}
										<button
											onclick={() => (ordersPage = pageNum as number)}
											class="px-3 py-1 rounded-lg border {ordersPage === pageNum
												? 'bg-mabini-yellow text-white'
												: 'bg-white text-gray-700 hover:bg-gray-50'}"
										>
											{pageNum}
										</button>
									{/if}
								{/each}

								<button
									onclick={() => (ordersPage = Math.min(totalOrdersPages, ordersPage + 1))}
									disabled={ordersPage === totalOrdersPages}
									class="px-3 py-1 rounded-lg border {ordersPage === totalOrdersPages
										? 'bg-gray-100 text-gray-400 cursor-not-allowed'
										: 'bg-white text-gray-700 hover:bg-gray-50'}"
								>
									Next
								</button>
							</div>
						{/if}
					</div>
				</div>
			</div>
		{:else if selectedTab === 'products'}
			<div
				class="px-4 sm:px-8 lg:ml-20 flex gap-0 flex-col justify-center items-center p-4 sm:p-10 w-full h-full"
			>
				<!-- Products Content -->
				<div class="w-full h-full justify-center items-center rounded-2xl shadow-lg mt-10">
					<!-- Black Header with Hamburger -->
					<div
						class="w-full min-h-[10%] bg-black rounded-t-2xl flex justify-between items-center {mobileMenuOpen
							? 'hidden'
							: 'flex'} lg:flex"
					>
						<!-- Mobile Hamburger Button -->
						<button
							type="button"
							onclick={toggleMobileMenu}
							class="lg:hidden text-white p-5"
							aria-label="Toggle menu"
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
								stroke-width="2"
								stroke="currentColor"
								class="w-6 h-6"
							>
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									d={mobileMenuOpen
										? 'M6 18L18 6M6 6l12 12'
										: 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5'}
								/>
							</svg>
						</button>
						<!-- Title - Hidden when sidebar is open on mobile -->
						<h1
							class="text-white text-2xl p-5 text-start flex-1 {mobileMenuOpen
								? 'lg:block hidden'
								: ''}"
						>
							Products Section
						</h1>
					</div>
					<!-- White Content with border -->
					<div class="w-full rounded-b-2xl p-5">
						<h2 class="text-base sm:text-lg font-bold">
							Fill out the details to create a new product entry for your menu.
						</h2>
						<p class="text-red-600 text-sm sm:text-base">* indicates required fields</p>
						<p class="text-gray-600 mt-2 text-xs sm:text-sm">
							<strong>Note:</strong> Adding a product here will automatically shows up in your Menu page
							so make sure that all the details are correct and accurate.
						</p>
						<!-- Form -->
						<form
							class="flex flex-col gap-3 sm:gap-4 mt-4 pb-10 sm:pb-20"
							onsubmit={handleProductSubmit}
						>
							<label class="flex flex-col">
								<span class="font-bold text-sm sm:text-base">Product Name *</span>
								<input
									type="text"
									class="border border-gray-300 rounded-lg px-3 sm:px-4 py-2 mt-1 text-sm sm:text-base"
									placeholder="e.g., Caramel Macchiato"
									bind:value={productName}
									required
								/>
							</label>
							<label class="flex flex-col">
								<span class="font-bold text-sm sm:text-base">Price (₱) *</span>
								<input
									type="number"
									step="0.01"
									min="0"
									class="border border-gray-300 rounded-lg px-3 sm:px-4 py-2 mt-1 text-sm sm:text-base"
									placeholder="e.g., 150.00"
									bind:value={productPrice}
									required
								/>
							</label>
							<label class="flex flex-col">
								<span class="font-bold text-sm sm:text-base">Main Category *</span>
								<div class="flex gap-2 mt-1">
									<select
										class="border border-gray-300 rounded-lg px-3 sm:px-4 py-2 w-full text-sm sm:text-base"
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
								<span class="font-bold text-sm sm:text-base">Subcategory </span>
								<div class="flex gap-2 mt-1">
									<select
										class="border border-gray-300 rounded-lg px-3 sm:px-4 py-2 w-full text-sm sm:text-base"
										bind:value={productDescription}
									>
										<option value="">None (optional)</option>
										{#each subcategories as sub}
											<option value={sub}>{sub}</option>
										{/each}
									</select>
								</div>
								<p class="text-xs sm:text-sm text-gray-500 mt-1">
									Optional: Adds a filter option in the sidebar (e.g., Savory Waffle, Hot, Iced)
								</p>
							</label>
							<label class="flex flex-col">
								<span class="font-bold text-sm sm:text-base">Product Photo *</span>
								<input
									type="file"
									accept="image/*"
									class="border border-gray-300 rounded-lg px-3 sm:px-4 py-2 mt-1 text-sm sm:text-base"
									onchange={handleImageChange}
									required
								/>
								{#if imagePreview}
									<div class="mt-2">
										<img
											src={imagePreview}
											alt="Preview"
											class="w-24 h-24 sm:w-32 sm:h-32 object-cover rounded-lg"
										/>
									</div>
								{/if}
							</label>
							<button
								type="submit"
								class="bg-mabini-yellow text-white px-4 py-2 sm:py-3 rounded-lg mt-4 cursor-pointer w-full hover:bg-yellow-600 text-sm sm:text-base font-semibold"
							>
								Add Product
							</button>
						</form>
					</div>
				</div>
			</div>
		{:else if selectedTab === 'product-manager'}
			<div
				class="px-4 sm:px-8 lg:ml-20 flex gap-0 flex-col justify-center items-center p-4 sm:p-10 w-full h-full"
			>
				<!-- Product Manager Content -->
				<div class="w-full h-full justify-center items-center rounded-2xl shadow-lg mt-10">
					<!-- Black Header with Hamburger -->
					<div
						class="w-full min-h-[10%] bg-black rounded-t-2xl flex justify-between items-center {mobileMenuOpen
							? 'hidden'
							: 'flex'} lg:flex"
					>
						<!-- Mobile Hamburger Button -->
						<button
							type="button"
							onclick={toggleMobileMenu}
							class="lg:hidden text-white p-5"
							aria-label="Toggle menu"
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
								stroke-width="2"
								stroke="currentColor"
								class="w-6 h-6"
							>
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									d={mobileMenuOpen
										? 'M6 18L18 6M6 6l12 12'
										: 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5'}
								/>
							</svg>
						</button>
						<!-- Title - Hidden when sidebar is open on mobile -->
						<h1
							class="text-white text-2xl p-5 text-start flex-1 {mobileMenuOpen
								? 'lg:block hidden'
								: ''}"
						>
							Product Manager
						</h1>
					</div>
					<!-- White Content with border -->
					<div class="w-full rounded-b-2xl p-5">
						<p class="text-gray-600 mb-4">Manage product availability and view all menu items.</p>

						<!-- Search Bar -->
						<div class="mb-5">
							<input
								type="text"
								bind:value={searchQuery}
								placeholder="Search products..."
								class="w-full border border-gray-300 rounded-lg px-4 py-2"
							/>
						</div>

						<!-- Product List -->
						<div class="space-y-3 pb-5">
							{#each paginatedProducts as product (product.id)}
								{@const isUnavailable = product.isAvailable === false || product.isAvailable === 0}
								<div
									class="flex items-center justify-between border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-all cursor-pointer"
									class:unavailable-product={isUnavailable}
									onclick={() => openProductModal(product)}
								>
									<div class="flex items-center gap-4 flex-1">
										{#if product.image_path || product.image_url}
											<img
												src={`https://mabini-cafe.bscs3a.com/phpbackend/${product.image_path || product.image_url}`}
												alt={product.name}
												class:unavailable-image={isUnavailable}
												class="w-16 h-16 object-cover rounded-lg transition-all"
											/>
										{/if}
										<div class="flex-1">
											<h3 class="font-bold text-lg" class:strikethrough-text={isUnavailable}>
												{product.name}
											</h3>
											<p class="text-sm text-gray-600" class:strikethrough-text={isUnavailable}>
												₱{product.price}
											</p>

											<p class="text-xs text-gray-500">{product.description || 'No category'}</p>

											{#if isUnavailable}
												<span class="text-xs text-red-600 font-semibold mt-1 inline-block"
													>Currently Unavailable</span
												>
											{/if}
										</div>
									</div>
									<div class="flex items-center gap-3">
										<div class="flex items-center gap-2">
											<span
												class="text-sm font-medium {isUnavailable
													? 'text-red-600'
													: 'text-green-600'}"
											>
												{isUnavailable ? 'Unavailable' : 'Available'}
											</span>
											<svg
												xmlns="http://www.w3.org/2000/svg"
												class="h-5 w-5 text-gray-400"
												fill="none"
												viewBox="0 0 24 24"
												stroke="currentColor"
											>
												<path
													stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
													d="M9 5l7 7-7 7"
												/>
											</svg>
										</div>
									</div>
								</div>
							{:else}
								<div class="text-center p-10">
									<p class="text-gray-500">No products found</p>
								</div>
							{/each}
						</div>

						<!-- Pagination Controls -->
						{#if totalProductsPages > 1}
							<div class="flex flex-wrap items-center justify-center gap-2 p-3 sm:p-5 border-t">
								<button
									onclick={() => (productsPage = Math.max(1, productsPage - 1))}
									disabled={productsPage === 1}
									class="px-2 sm:px-3 py-1 rounded-lg border text-xs sm:text-sm {productsPage === 1
										? 'bg-gray-100 text-gray-400 cursor-not-allowed'
										: 'bg-white text-gray-700 hover:bg-gray-50'}"
								>
									Previous
								</button>

								{#each getPageNumbers(productsPage, totalProductsPages) as pageNum}
									{#if pageNum === '...'}
										<span class="px-1 sm:px-2 text-gray-400 text-xs sm:text-sm">...</span>
									{:else}
										<button
											onclick={() => (productsPage = pageNum as number)}
											class="px-2 sm:px-3 py-1 rounded-lg border text-xs sm:text-sm {productsPage ===
											pageNum
												? 'bg-mabini-yellow text-white'
												: 'bg-white text-gray-700 hover:bg-gray-50'}"
										>
											{pageNum}
										</button>
									{/if}
								{/each}

								<button
									onclick={() => (productsPage = Math.min(totalProductsPages, productsPage + 1))}
									disabled={productsPage === totalProductsPages}
									class="px-2 sm:px-3 py-1 rounded-lg border text-xs sm:text-sm {productsPage ===
									totalProductsPages
										? 'bg-gray-100 text-gray-400 cursor-not-allowed'
										: 'bg-white text-gray-700 hover:bg-gray-50'}"
								>
									Next
								</button>
							</div>
						{/if}
					</div>
				</div>
			</div>
		{/if}
		<!-- </div> -->
	</div>
</div>

<!-- Order Details Modal -->
{#if showOrderModal && selectedOrder}
	<ViewItemModal order={selectedOrder} onClose={closeOrderModal} />
{/if}

<!-- Product Edit Modal -->
{#if showProductModal && selectedProduct}
	<AdminItemModal product={selectedProduct} onClose={closeProductModal} />
{/if}
