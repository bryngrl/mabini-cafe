<!-- !TO: DO
 ! [] Responsive
 ! [] Hamburger Menu
--->
<script>
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth';
	import { cartCount } from '$lib/stores/cart';
	import CartModal from '../ui/CartModal.svelte';

	let links = [
		{ name: 'Home', href: '/' },
		{ name: 'Menu', href: '/menu' },
		{ name: 'About', href: '/about' }
	];

	let open = false;
	let accountOpen = false;
	let cartModalOpen = false;
	
	function logout() {
		authStore.logout();
		localStorage.removeItem('token');
		goto('/login');
	}

	function openCart() {
		if ($authStore.isAuthenticated) {
			cartModalOpen = true;
		} else {
			goto('/login');
		}
	}

	function closeCart() {
		cartModalOpen = false;
	}
</script>

<nav
	class="flex items-center justify-between p-4 bg-black text-white px-15 py-5 font-medium uppercase drop-shadow-2xl"
>
	<!-- Left links -->
	<div class="flex-1 flex justify-start ml-[50px]">
		<ul class="flex gap-6 items-center">
			{#each links as link}
				<li class="relative group">
					<a class=" text-[16px]" href={link.href}>
						{link.name}
					</a>
					<span class="underline-anim"></span>
				</li>
			{/each}

			<li class="relative">
				<button on:click={() => (open = !open)} class="flex items-center gap-2">
					SUPPORT
					<span class="transition-transform duration-300 {open ? 'rotate-180' : ''}"> ˅ </span>
				</button>

				{#if open}
					<ul
						class="absolute left-0 mt-10 w-50 bg-mabini-black text-mabini-white rounded shadow text-[16px]"
					>
						<li class="px-4 py-2 pb-5 cursor-pointer text-[16px]">
							<a href="/orders-and-payment">ORDERS & PAYMENT</a>
						</li>
						<li class="px-4 py-2 pb-5 cursor-pointer text-[16px]">
							<a href="/shipping">SHIPPING</a>
						</li>
					</ul>
				{/if}
			</li>
		</ul>
	</div>

	<!-- Centered logo -->
	<div class="flex-grow flex justify-center">
		<a href="/">
			<img src="/images/LOGO-4.png" alt="Logo" class="logo-4" />
		</a>
	</div>

	<!-- Right-aligned links -->
	<div class="flex-1 flex justify-end gap-4 mr-[50px]">
		{#if $authStore.loading}
			<!-- Show loading -->
			<div class="relative group text-[16px] opacity-50">
				Loading...
			</div>
		{:else if $authStore.isAuthenticated}
			<div class="relative">
				<button on:click={() => (accountOpen = !accountOpen)} class="relative group text-[16px] flex items-center gap-2">
					ACCOUNT
					<span class="transition-transform duration-300 {accountOpen ? 'rotate-180' : ''}"> ˅ </span>
				</button>
				{#if accountOpen}
					<ul class="absolute right-0 mt-2 w-40 bg-mabini-black text-mabini-white rounded shadow text-[16px] z-50">
						<li>
							<button
								type="button"
								class="w-full text-left px-4 py-2 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown"
								on:click={() => { accountOpen = false; goto('/settings'); }}
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
			<a href="/login" class="relative group text-[16px]">
				Login
				<span class="underline-anim"></span>
			</a>
		{/if}
		<a href="/search" class="relative group">
			<img src="/icons/search.png" alt="Search" class="h-5 w-6" />
			<span class="underline-anim"></span>
		</a>
		<button class="relative group" on:click={openCart} type="button">
			<img src="/icons/cart.png" alt="Cart" class="h-5 w-6" />
			{#if $cartCount > 0}
				<span class="absolute -top-2 -right-2 bg-mabini-yellow text-mabini-dark-brown text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
					{$cartCount}
				</span>
			{/if}
			<span class="underline-anim"></span>
		</button>
	</div>
</nav>

<CartModal isOpen={cartModalOpen} onClose={closeCart} />
