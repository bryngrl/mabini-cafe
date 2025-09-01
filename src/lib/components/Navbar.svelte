<script>
	let links = [
		{ name: 'Home', href: '/' },
		{ name: 'Menu', href: '/menu' },
		{ name: 'About', href: '/about' }
	];
</script>

<nav
	class="bg-black text-white px-15 py-5 flex justify-between items-center font-creato font-normal uppercase"
>
	<ul class="flex gap-6">
		{#each links as link}
			<li>
				<a class="hover:text-mabini-beige" href={link.href}>{link.name}</a>
			</li>
		{/each}
	</ul>
	<!-- ! Support dropdown ON HOLD -->

	<img src="src\lib\assets\logo\LOGO-4.png" alt="Logo" class="logo-4" />

	<!-- Search and Cart -->
	<div class="flex items-center gap-4">
		<a href="/account" class="hover:text-mabini-beige">Account</a>
		<a href="/search" class="relative group">
			<img src="src\lib\assets\icons\search.png" alt="Search" class="h-4 w-5" />
			<span class="icon-hover"></span>
		</a>
		<a href="/cart" class="relative group mr">
			<img src="src\lib\assets\icons\cart.png" alt="Cart" class="h-4 w-5" />
			<span class="icon-hover"> </span>
		</a>
	</div>
</nav>
