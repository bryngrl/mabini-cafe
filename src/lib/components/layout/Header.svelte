<script>
	import SearchModal from '../ui/SearchModal.svelte';
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth';
	import { cartCount } from '$lib/stores/cart';
	import CartModal from '../ui/CartModal.svelte';
	import { showConfirm, showSuccess } from '$lib/utils/sweetalert';

	let links = [
		{ name: 'Home', href: '/' },
		{ name: 'Menu', href: '/menu' },
		{ name: 'About', href: '/about' }
	];

	let open = false; // Controls desktop/mobile SUPPORT dropdown
	let accountOpen = false;
	let cartModalOpen = false;
	let searchModalOpen = false;
	let mobileMenuOpen = false;

	// --- START: Hover Delay Logic ---
	let hoverTimeout;
	const CLOSE_DELAY = 200; // 200 milliseconds (adjust as needed)

	function handleMouseEnter() {
		// When entering, immediately cancel any pending close timer
		clearTimeout(hoverTimeout);
		open = true; // Open the dropdown instantly
	}

	function handleMouseLeave() {
		// When leaving, START the close timer instead of closing instantly
		hoverTimeout = setTimeout(() => {
			open = false; // Close the dropdown after the delay
		}, CLOSE_DELAY);
	}
	// --- END: Hover Delay Logic ---

	function closeSearch() {
		searchModalOpen = false;
	}
	let accountHoverTimeout;
	function handleAccountMouseEnter() {
		clearTimeout(accountHoverTimeout);
		accountOpen = true; // Open the account dropdown instantly
	}

	function handleAccountMouseLeave() {
		// Start the close timer for the account menu
		accountHoverTimeout = setTimeout(() => {
			accountOpen = false;
		}, CLOSE_DELAY);
	}
	async function logout() {
		await showConfirm('Do you want you to logout?', 'Logout');
		authStore.logout();
		localStorage.removeItem('token');
		clearTimeout(accountHoverTimeout);
		accountOpen = false;
		showSuccess('Logged out successfully');
		setTimeout(() => {
			goto('/login');
		}, 2000);
	}

	function selectAndNavigateAccount(path, isAdmin) {
		clearTimeout(accountHoverTimeout); // Crucial: Clear the close timer
		accountOpen = false;
		// The mobileMenuOpen cleanup is handled below in the markup for the mobile menu.

		if (path === '/logout') {
			logout();
		} else if (path === '/admin') {
			goto('/admin');
		} else if (path === '/settings') {
			goto('/account/settings');
		}
	}

	function openCart() {
		if ($authStore.isAuthenticated) {
			cartModalOpen = true;
		} else {
			goto('/login');
		}
	}

	function openSearch() {
		searchModalOpen = true;
	}
	function closeCart() {
		cartModalOpen = false;
	}

	// Updated to clear the hover timeout
	function selectAndNavigate(path) {
		// Clear any pending desktop close timer when an item is clicked
		clearTimeout(hoverTimeout);

		open = false;
		mobileMenuOpen = false;
		goto(path);
	}
</script>

<nav
	class="flex items-center justify-between p-4 bg-black text-white font-medium uppercase drop-shadow-2xl w-full relative z-[30]"
>
	<!-- Left links (desktop) -->
	<!-- svelte-ignore a11y_no_static_element_interactions -->
	<div class="hidden md:flex flex-1 justify-start ml-[50px]">
		<ul class="flex gap-6 items-center">
			{#each links as link}
				<li class="relative group">
					<a class="text-[16px]" href={link.href}>{link.name}</a>
					<span class="underline-anim"></span>
				</li>
			{/each}
			<div class="relative" on:mouseenter={handleMouseEnter} on:mouseleave={handleMouseLeave}>
				<button class="relative group text-[16px] flex items-center gap-2 cursor-pointer">
					SUPPORT
					<span class="transition-transform duration-300 {open ? 'rotate-180' : ''}">
						<i class="fa-solid fa-chevron-down"></i>
					</span>
				</button>
				{#if open}
					<ul
						class="absolute left-0 mt-2 w-40 bg-mabini-black text-mabini-white rounded shadow text-[16px] z-50"
					>
						<li>
							<button
								type="button"
								on:click={() => selectAndNavigate('/orders-and-payment')}
								class="w-full text-left px-4 py-2 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown"
								>Orders & Payment</button
							>
						</li>
						<li>
							<button
								type="button"
								on:click={() => selectAndNavigate('/shipping')}
								class="w-full text-left px-4 py-2 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown"
								>Shipping</button
							>
						</li>
					</ul>
				{/if}
			</div>
		</ul>
	</div>

	<!-- Centered logo -->
	<div class="flex-grow flex items-center justify-center relative">
		<a href="/" class="mx-auto">
			<img
				src="/images/LOGO-4.png"
				alt="Logo"
				class="logo-4 w-[120px] md:w-[180px] justify-self-center"
			/>
		</a>
		<!-- hamburger -->
		<div class="md:hidden flex items-center absolute right-0 p-2">
			<button
				aria-label={mobileMenuOpen ? 'Close menu' : 'Open menu'}
				class="focus:outline-none"
				on:click={() => (mobileMenuOpen = !mobileMenuOpen)}
			>
				<svg
					class="w-8 h-8 text-white transition-all duration-300"
					fill="none"
					stroke="currentColor"
					viewBox="0 0 24 24"
					xmlns="http://www.w3.org/2000/svg"
				>
					{#if mobileMenuOpen}
						<!-- X icon -->
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M6 18L18 6M6 6l12 12"
						/>
					{:else}
						<!-- Hamburger icon -->
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M4 6h16M4 12h16M4 18h16"
						/>
					{/if}
				</svg>
			</button>
		</div>
	</div>
	<!-- Mobile Hamburger -->

	<!-- Right-aligned links (desktop) -->
	<div class="hidden md:flex flex-1 justify-end gap-4 mr-[50px]">
		{#if $authStore.loading}
			<div class="relative group text-[16px] opacity-50">Loading...</div>
		{:else if $authStore.isAuthenticated}
			<div
				class="relative"
				on:mouseenter={handleAccountMouseEnter}
				on:mouseleave={handleAccountMouseLeave}
			>
				<button
					class="relative group text-[16px] flex items-center gap-2 cursor-pointer"
					type="button"
				>
					ACCOUNT
					<span class="transition-transform duration-300 {accountOpen ? 'rotate-180' : ''}">
						<i class="fa-solid fa-chevron-down"></i>
					</span>
				</button>
				{#if accountOpen}
					<ul
						class="absolute right-0 mt-2 w-40 bg-mabini-black text-mabini-white rounded shadow text-[16px] z-50"
					>
						<li>
							<button
								type="button"
								class="w-full text-left px-4 py-2 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown"
								on:click={() => {
									// Close logic is now inside a reusable function to clear the timer
									if ($authStore.isAdmin) {
										selectAndNavigateAccount('/admin', true);
									} else {
										selectAndNavigateAccount('/settings', false);
									}
								}}
							>
								{$authStore.isAdmin ? 'Dashboard' : 'Settings'}
							</button>
						</li>
						<li>
							<button
								type="button"
								class="w-full text-left px-4 py-2 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown"
								on:click={() => selectAndNavigateAccount('/logout')}
							>
								Logout
							</button>
						</li>
					</ul>
				{/if}
			</div>
		{:else}
			<a href="/login" class="relative group text-[16px]"
				>Login<span class="underline-anim"></span></a
			>
		{/if}
		<button class="relative group" on:click={openSearch} type="button">
			<img src="/icons/search.png" alt="Search" class="h-5 w-6 cursor-pointer" />
			<span class="underline-anim"></span>
		</button>
		<button class="relative group" on:click={openCart} type="button">
			<img src="/icons/cart.png" alt="Cart" class="h-5 w-6 cursor-pointer" />
			{#if $cartCount > 0}
				<span
					class="absolute -top-2 -right-2 bg-mabini-yellow text-mabini-dark-brown text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
					>{$cartCount}</span
				>
			{/if}
			<span class="underline-anim"></span>
		</button>
	</div>
	<!-- Mobile menu -->
	{#if mobileMenuOpen}
		<div
			class="flex gap-0 flex-col items-start bg-black text-white w-full absolute top-[64px] left-0 z-50"
		>
			<!-- Home Menu and About -->
			<div class="w-[250px] grow">
				<ul class="list-none p-0 m-0">
					{#each links as link}
						<li class="w-full">
							<a
								class="text-[20px] font-bold w-full block px-6 py-3 bg-black text-white"
								href={link.href}
								on:click={() => (mobileMenuOpen = false)}>{link.name}</a
							>
						</li>
					{/each}
				</ul>
			</div>
			<!-- Support Menu -->
			<div class="w-full grow">
				<button
					on:click={() => (open = !open)}
					class="text-[20px] font-bold w-full px-6 py-3 pr-10 bg-black text-white flex items-center justify-between"
				>
					<span class="flex-1 text-left cursor-pointer">SUPPORT</span>
					<span class="transition-transform duration-300 {open ? 'rotate-180' : ''}">
						<i class="fa-solid fa-chevron-down"></i>
					</span>
				</button>

				{#if open}
					<ul class=" bg-black text-white rounded shadow text-[18px] w-full">
						<li>
							<button
								type="button"
								on:click={() => {
									open = false;
									mobileMenuOpen = false;
									goto('/orders-and-payment');
								}}
								class="w-full text-left px-6 py-3 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown"
								>Orders & Payment</button
							>
						</li>
						<li>
							<button
								type="button"
								on:click={() => {
									open = false;
									mobileMenuOpen = false;
									goto('/shipping');
								}}
								class="w-full text-left px-6 py-3 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown"
								>Shipping</button
							>
						</li>
					</ul>
				{/if}
			</div>
			<!-- Login -->
			<div class="w-full grow">
				{#if $authStore.loading}
					<div class="text-[20px] opacity-50 w-full px-6 py-3 bg-black text-white rounded">
						Loading...
					</div>
				{:else if $authStore.isAuthenticated}
					<button
						class="text-[20px] font-bold w-full px-6 py-3 pr-10 bg-black text-white rounded flex items-center justify-between"
						on:click={() => {
							accountOpen = !accountOpen;
						}}
					>
						<span class="flex-1 text-left">ACCOUNT</span>
						<span class="transition-transform duration-300 {accountOpen ? 'rotate-180' : ''}">
							<i class="fa-solid fa-chevron-down"></i>
						</span>
					</button>
					{#if accountOpen}
						<ul class="mt-2 bg-black text-white rounded shadow text-[18px] w-full">
							<li>
								<button
									type="button"
									class="w-full text-left px-6 py-3 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown"
									on:click={() => {
										accountOpen = false;
										mobileMenuOpen = false;
										if ($authStore.isAdmin) {
											goto('/admin');
										} else {
											goto('/account/settings');
										}
									}}
								>
									{$authStore.isAdmin ? 'Dashboard' : 'Settings'}
								</button>
							</li>
							<li>
								<button
									type="button"
									class="w-full text-left px-6 py-3 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown"
									on:click={() => {
										accountOpen = false;
										mobileMenuOpen = false;
										logout();
									}}
								>
									Logout
								</button>
							</li>
						</ul>
					{/if}
				{:else}
					<a
						href="/login"
						class="text-[20px] font-bold w-full block px-6 py-3 bg-black text-white"
						on:click={() => (mobileMenuOpen = false)}>Login</a
					>
				{/if}
			</div>
			<!-- Search and Cart -->
			<div class="grow w-full px-6 py-3 gap-4 pb-5 items-center flex justify-center">
				<button
					class="relative group"
					on:click={() => {
						openSearch();
						mobileMenuOpen = false;
					}}
					type="button"
				>
					<img src="/icons/search.png" alt="Search" class="h-7 w-8 cursor-pointer" />
				</button>
				<button
					class="relative group"
					on:click={() => {
						openCart();
						mobileMenuOpen = false;
					}}
					type="button"
				>
					<img src="/icons/cart.png" alt="Cart" class="h-7 w-8 cursor-pointer" />
					{#if $cartCount > 0}
						<span
							class="absolute -top-2 -right-2 bg-mabini-yellow text-mabini-dark-brown text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
							>{$cartCount}</span
						>
					{/if}
				</button>
			</div>
		</div>
	{/if}
</nav>

<CartModal isOpen={cartModalOpen} onClose={closeCart} />
<SearchModal isOpen={searchModalOpen} onClose={closeSearch} />
