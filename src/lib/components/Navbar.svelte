<script>
	let links = [
		{ name: 'Home', href: '/' },
		{ name: 'Menu', href: '/menu' },
		{ name: 'About', href: '/about' },
		{ name: 'Contact', href: '/contact' }
	];
</script>

<nav class="bg-black text-white px-6 py-4 flex justify-between items-center font-creato uppercase font-bold">
	<ul class="flex gap-6">
		{#each links as link}
			<li>
				<a class="hover:text-mabini-beige" href={link.href}>{link.name}</a>
			</li>
		{/each}
	</ul>

	<img src="/logo.png" alt="Logo" class="h-10" />

	<div class="flex items-center gap-4">
		<a href="/account" class="hover:text-mabini-beige">Account</a>
		<a href="/search" class="hover:text-mabini-beige">
			<img src="/search-icon.png" alt="Search" class="h-6" />
		</a>
		<a href="/cart" class="hover:text-mabini-beige">
			<img src="/cart-icon.png" alt="Cart" class="h-6" />
		</a>
	</div>
</nav>
