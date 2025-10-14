<!-- !TO: DO
 ! [] Responsive
 ! [] Hamburger Menu
--->
<script>
	import SearchModal from '../ui/SearchModal.svelte';
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth';
	import { cartCount } from '$lib/stores/cart';
	import CartModal from '../ui/CartModal.svelte';
	import { showSuccess } from '$lib/utils/sweetalert';

	let links = [
		{ name: 'Home', href: '/' },
		{ name: 'Menu', href: '/menu' },
		{ name: 'About', href: '/about' }
	];

	let open = false;
	let accountOpen = false;
	let cartModalOpen = false;
	let searchModalOpen = false;
	let mobileMenuOpen = false;

	function closeSearch() {
		searchModalOpen = false;
	}

	function logout() {
		authStore.logout();
		localStorage.removeItem('token');
		showSuccess('Logged out successfully');
		setTimeout(() => {
			goto('/login');
		}, 2000);
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
</script>

<nav
	class="flex items-center justify-between p-4 bg-black text-white font-medium uppercase drop-shadow-2xl w-full"
>
	<!-- Left links (desktop) -->
	<div class="hidden md:flex flex-1 justify-start ml-[50px]">
		<ul class="flex gap-6 items-center">
			{#each links as link}
				<li class="relative group">
					<a class="text-[16px]" href={link.href}>{link.name}</a>
					<span class="underline-anim"></span>
				</li>
			{/each}
			<div class="relative">
				<button
					on:click={() => (open = !open)}
					class="relative group text-[16px] flex items-center gap-2 cursor-pointer"
				>
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
								on:click={() => {
									open = false;
									mobileMenuOpen = false;
									goto('/orders-and-payment');
								}}
								class="w-full text-left px-4 py-2 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown"
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
			<div class="relative">
				<button
					on:click={() => (accountOpen = !accountOpen)}
					class="relative group text-[16px] flex items-center gap-2 cursor-pointer"
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
									accountOpen = false;
									goto('/account/settings');
								}}
							>
								Settings
							</button>
						</li>
						<li>
							<button
								type="button"
								class="w-full text-left px-4 py-2 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown"
								on:click={logout}
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
										goto('/settings');
									}}
								>
									Settings
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

<!-- 


<div class="fixed inset-0 bg-black flex flex-col">
			<div class="flex justify-center items-center p-4 pb-1 w-full">
				<a href="/">
					<img src="/images/LOGO-4.png" alt="Logo" class="w-[120px]" />
				</a>

				<div class="flex-1">
					<button
						aria-label="Close menu"
						class="focus:outline-none"
						on:click={() => (mobileMenuOpen = false)}
					>
						<svg
							class="w-8 h-8 text-white"
							fill="none"
							stroke="currentColor"
							viewBox="0 0 24 24"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								stroke-linecap="round"
								stroke-linejoin="round"
								stroke-width="2"
								d="M6 18L18 6M6 6l12 12"
							/>
						</svg>
					</button>
				</div>
			</div>
			<ul class="flex flex-col gap-0 pt-0 items-center w-full">
				{#each links as link}
					<li class="w-full">
						<a
							class="text-[20px] font-bold w-full block px-6 py-3 bg-black text-white"
							href={link.href}
							on:click={() => (mobileMenuOpen = false)}>{link.name}</a
						>
					</li>
				{/each}
				<li class="w-full">
					<button
						on:click={() => (open = !open)}
						class="text-[20px] font-bold w-full px-6 py-3 bg-black text-white flex items-center justify-between"
					>
						SUPPORT
						<span class="transition-transform duration-300 {open ? 'rotate-180' : ''}"> ˅ </span>
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
				</li>
				<li class="w-full">
					{#if $authStore.loading}
						<div class="text-[20px] opacity-50 w-full px-6 py-3 bg-black text-white rounded">
							Loading...
						</div>
					{:else if $authStore.isAuthenticated}
						<button
							class="text-[20px] font-bold w-full px-6 py-3 bg-black text-white rounded flex items-center justify-between"
							on:click={() => {
								accountOpen = !accountOpen;
							}}
						>
							ACCOUNT
							<span class="transition-transform duration-300 {accountOpen ? 'rotate-180' : ''}">
								˅
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
											goto('/settings');
										}}
									>
										Settings
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
				</li>
				<li class="bg-black p-4 w-full flex gap-4 justify-center">
					<button
						class="relative group"
						on:click={() => {
							openSearch();
							mobileMenuOpen = false;
						}}
						type="button"
					>
						<img src="/icons/search.png" alt="Search" class="h-7 w-8" />
					</button>
					<button
						class="relative group"
						on:click={() => {
							openCart();
							mobileMenuOpen = false;
						}}
						type="button"
					>
						<img src="/icons/cart.png" alt="Cart" class="h-7 w-8" />
						{#if $cartCount > 0}
							<span
								class="absolute -top-2 -right-2 bg-mabini-yellow text-mabini-dark-brown text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
								>{$cartCount}</span
							>
						{/if}
					</button>
				</li>
			</ul>
		</div>






-->
