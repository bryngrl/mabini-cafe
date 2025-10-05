<!-- Home Page -->
<script lang="ts">
	import { menu, fetchMenu } from '$lib/stores/menu.js';
	import { onMount } from 'svelte';
	let heroImage = '/images/cover-photo-1.png';
	let heroImage2 = '/images/sofa.png';
	let heroImage3 = '/images/cover-photo-2.png';

	let products: { name: string; price: string; temp: string; img: string }[] = [];
	let scrollRef: HTMLDivElement;

	const unsubscribe = menu.subscribe((m) => {
		products = m.items.map((item) => ({
			name: item.name,
			price: `₱${item.price}`,
			temp: item.temp || '',
			img: item.image_path
				? 'http://localhost/mabini-cafe/phpbackend/' + item.image_path.replace(/^\/?/, '')
				: ''
		}));
	});

	onMount(() => {
		fetchMenu();
	});

	function scrollRight() {
		scrollRef.scrollBy({ left: 300, behavior: 'smooth' });
	}
	function scrollLeft() {
		scrollRef.scrollBy({ left: -300, behavior: 'smooth' });
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
		<div class="flex items-center gap-4 justify-center">
			<button on:click={scrollLeft} class="p-2 rounded-full bg-gray-200 hover:bg-mabini-yellow transition-colors">
				<svg width="24" height="24" fill="none">
					<path d="M15 6l-6 6 6 6" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
			<div bind:this={scrollRef} class="flex overflow-x-auto gap-6 py-6 px-2 scrollbar-hide">
				{#each products.slice(0, 10) as product}
					<div class="min-w-[260px] max-w-[260px] min-h-[370px] bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center justify-between transition-transform hover:scale-105 relative">
						<img src={product.img} alt={product.name} class="w-40 h-40 object-contain mb-2 mt-2 drop-shadow-md" />
						<div class="flex flex-col justify-end w-full flex-1">
							<div class="font-extrabold text-lg text-left mb-2 mt-4 w-full" style="letter-spacing:0.5px">{product.name}</div>
							<div class="flex items-center justify-between w-full mt-2">
								<span class="text-gray-400 font-semibold">{product.price}</span>
								<span class="text-gray-500 text-base font-medium">{product.temp}</span>
							</div>
						</div>
					</div>
				{/each}
			</div>
			<button on:click={scrollRight} class="p-2 rounded-full bg-gray-200 hover:bg-mabini-yellow transition-colors">
				<svg width="24" height="24" fill="none">
					<path d="M9 6l6 6-6 6" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
		</div>
	</div>
</section>

<style>
	/* Hide scrollbar for Chrome*/
	.scrollbar-hide::-webkit-scrollbar {
		display: none;
	}
	.scrollbar-hide {
		-ms-overflow-style: none;
		scrollbar-width: none;
	}
	.bg-mabini-yellow { background-color: #FFD600; }
	.text-mabini-yellow { color: #FFD600; }
	.text-mabini-dark-brown { color: #4B2E05; }
</style>
