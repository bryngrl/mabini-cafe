<!-- !TO: DO
 ! [] Responsive
 ! [] Hamburger Menu
--->
<script>
	import { auth } from '$lib/stores/auth';

	let links = [
		{ name: 'Home', href: '/' },
		{ name: 'Menu', href: '/menu' },
		{ name: 'About', href: '/about' }
	];

	let open = false;
	let accountOpen = false;

	function logout() {
		auth.set({ isLoggedIn: false, token: null, user: null });
		localStorage.removeItem('token');
		window.location.href = '/login';
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
		{#if $auth.isLoggedIn}
			<div class="relative">
				<button on:click={() => (accountOpen = !accountOpen)} class="relative group text-[16px] flex items-center gap-2">
					ACCOUNT
					<span class="transition-transform duration-300 {accountOpen ? 'rotate-180' : ''}"> ˅ </span>
				</button>
				{#if accountOpen}
					<ul class="absolute right-0 mt-2 w-40 bg-mabini-black text-mabini-white rounded shadow text-[16px] z-50">
						<li class="px-4 py-2 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown" on:click={() => { accountOpen = false; window.location.href = '/settings'; }}>
							Settings
						</li>
						<li class="px-4 py-2 cursor-pointer hover:bg-mabini-yellow hover:text-mabini-dark-brown" on:click={logout}>
							Logout
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
		<a href="/cart" class="relative group">
			<img src="/icons/cart.png" alt="Cart" class="h-5 w-6" />
			<span class="underline-anim"></span>
		</a>
	</div>
</nav>
