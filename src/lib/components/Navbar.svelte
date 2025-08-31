<script>
	let links = [
		{ name: 'Home', href: '/' },
		{ name: 'Menu', href: '/menu' },
		{ name: 'About', href: '/about' }
	];
	// Drop down wala pa
	let showDropdown = false;
	
</script>

<nav
	class="bg-black text-white px-6 py-4 flex justify-between items-center font-creato uppercase font-bold text-base"
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

	<div class="flex items-center gap-4">
		<a href="/account" class="hover:text-mabini-beige">Account</a>
		<a href="/search" class="relative group">
			<img src="src\lib\assets\icons\search.png" alt="Search" class="h-6" />
			<span class="icon-hover"></span>
		</a>
		<a href="/cart" class="relative group mr">
			<img src="src\lib\assets\icons\cart.png" alt="Cart" class="h-6" />
			<span class="icon-hover"> </span>
		</a>
	</div>
</nav>
