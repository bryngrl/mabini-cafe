<!-- Home Page -->
<script lang="ts">
	let heroImage = '/images/cover-photo-1.png';
	let heroImage2 = '/images/sofa.png';
	let heroImage3 = '/images/cover-photo-2.png';

	// Fetch to sa database yung products
	let products = [
    { name: "Strawberry Frappe", price: "₱100.00", temp: "cold", img: "/images/frappe.png" },
    { name: "Mango Frappe", price: "₱120.00", temp: "cold", img: "/images/frappe.png" },
    { name: "Chocolate Frappe", price: "₱110.00", temp: "cold", img: "/images/frappe.png" },
	{ name: "Vanilla Frappe", price: "₱115.00", temp: "cold", img: "/images/frappe.png" },
	{ name: "Coffee Frappe", price: "₱130.00", temp: "cold", img: "/images/frappe.png" },
	{ name: "Matcha Frappe", price: "₱125.00", temp: "cold", img: "/images/frappe.png" },
	{ name: "Mocha Frappe", price: "₱135.00", temp: "cold", img: "/images/frappe.png" },
	{ name: "Caramel Frappe", price: "₱140.00", temp: "cold", img: "/images/frappe.png" },
	{ name: "Hazelnut Frappe", price: "₱145.00", temp: "cold", img: "/images/frappe.png" },
	{ name: "Cappuccino Frappe", price: "₱150.00", temp: "cold", img: "/images/frappe.png" },

  ];
  let scrollRef: HTMLDivElement;

  function scrollRight() {
    scrollRef.scrollBy({ left: 300, behavior: "smooth" });
  }
  function scrollLeft() {
    scrollRef.scrollBy({ left: -300, behavior: "smooth" });
  }
</script>

<svelte:head>
	<title>Mabini Cafe</title>
	<meta name="description" content="Welcome to Mabini Cafe" />
</svelte:head>

<link rel="stylesheet" href="src/styles/home.css" />
<section class="hero" style="background-image: url('{heroImage}')">
	<!-- The three circles here -->
</section>

<section class="hero-2" style="background-image: url('{heroImage2}')">
	<div class="hero-content">
		<h1 class="text-[30px] !text-mabini-black uppercase font-bold leading-tight pt-[10px]">
			Choose Your
			<span class="!text-mabini-yellow"> Refresher </span>
			<br />
			<span class="!text-[60px] !text-mabini-dark-brown leading-none">
				Cucumber <br />
				Lemonade
			</span>
		</h1>
	</div>
</section>

<section class="hero-3" style="background-image: url('{heroImage3}')"></section>
<section class="paper">
	<div class="content">
		<h1 class="h1-heading !text-mabini-white pt-[50px] pb-[50px] text-center uppercase">
			Receipt of
			<span class="!text-mabini-yellow"> Gratitude </span>
		</h1>
		<img src="/images/Paper-1.png" alt="Paper" class="mx-auto object-center" />
	</div>
</section>
<section class="featured-products">
	<div class="content">
		<h1 class="h1-heading pt-[70px] text-center uppercase mb-30">
			Featured
			<span class="!text-mabini-yellow"> Products </span>
		</h1>

		
		<!-- Featured Products -->
		<div class="flex items-center gap-2">
			<button on:click={scrollLeft} class="p-2 rounded-full bg-gray-200 hover:bg-gray-300">
				<!-- SVG ng arrows -->
				<svg width="24" height="24" fill="none"
					><path
						d="M15 6l-6 6 6 6"
						stroke="#333"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					/></svg
				>
			</button>
			<div bind:this={scrollRef} class="flex overflow-x-auto gap-4 py-4 px-2 scrollbar-hide">
				{#each products as product}
					<div class="min-w-[180px] bg-white rounded-xl shadow-md p-4 flex flex-col items-center">
						<img src={product.img} alt={product.name} class="w-24 h-24 object-contain mb-2" />
						<div class="font-bold text-sm mb-1">{product.name}</div>
						<div class="text-xs mb-1">{product.price}</div>
						<div class="text-xs text-gray-500">{product.temp}</div>
					</div>
				{/each}
			</div>
			<button on:click={scrollRight} class="p-2 rounded-full bg-gray-200 hover:bg-gray-300">
				<!-- SVG ng arrows -->
				<svg width="24" height="24" fill="none"
					><path
						d="M9 6l6 6-6 6"
						stroke="#333"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					/></svg
				>
			</button>
		</div>
	</div>
</section>
<style>
  /* Hide scrollbar for Chrome, Safari and Opera */
  .scrollbar-hide::-webkit-scrollbar {
    display: none;
  }
  /* Hide scrollbar for IE, Edge and Firefox */
  .scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
  }
</style>
