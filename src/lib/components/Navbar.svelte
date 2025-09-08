<!-- !TO: DO
 ! [] Dropdown Icon
 ! [] Responsive
 ! [] Hamburge Menu
-->

<script>
	let links = [
		{ name: 'Home', href: '/' },
		{ name: 'Menu', href: '/menu' },
		{ name: 'About', href: '/about' }
	];
	let open = false;
</script>

<nav
	class="flex items-center justify-between p-4 bg-black text-white px-15 py-5 font-medium uppercase drop-shadow-2xl"
>
	<!-- Left-aligned links -->
	<div class="flex-1 flex justify-start">
		<ul class="flex gap-6 items-center">
			{#each links as link}
				<li class="relative group">
					<a class="hover:text-mabini-beige" href={link.href}>
						{link.name}
					</a>
					<span class="underline-anim"></span>
				</li>
			{/each}

			<!-- !No dropdown Icon -->
			<li class="relative">
				<button on:click={() => (open = !open)} class="focus:outline-none relative group">
					SUPPORT
					<span class="underline-anim"></span>
				</button>
				<!-- <span class="underline-anim"></span> -->

				{#if open}
					<ul
						class="absolute left-0 mt-10 w-50 bg-mabini-black text-mabini-white rounded shadow text-[16px]"
					>
						<li class="px-4 py-2 pb-5 cursor-pointer"><a href="/payment">ORDERS & PAYMENT</a></li>
						<li class="px-4 py-2 pb-5 cursor-pointer"><a href="/shipping">SHIPPING</a></li>
					</ul>
				{/if}
			</li>
		</ul>
	</div>

	<!-- Centered logo -->
	<div class="flex-grow flex justify-center">
		<a href="#">
			<img src="src/lib/assets/logo/LOGO-4.png" alt="Logo" class="logo-4" />
		</a>
	</div>

	<!-- Right-aligned links -->
	<div class="flex-1 flex justify-end gap-4">
		<a href="/account" class="relative group">
			Account
			<span class="underline-anim"></span>
		</a>
		<a href="/search" class="relative group">
			<img src="src/lib/assets/icons/search.png" alt="Search" class="h-5 w-6" />
			<span class="underline-anim"></span>
		</a>
		<a href="/cart" class="relative group">
			<img src="src/lib/assets/icons/cart.png" alt="Cart" class="h-5 w-6" />
			<span class="underline-anim"></span>
		</a>
	</div>
</nav>
