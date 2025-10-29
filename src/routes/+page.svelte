<script lang="ts">
	import { goto } from '$app/navigation';
	import { menuStore, customizeStore } from '$lib/stores';
	import { onMount } from 'svelte';

	let heroImage = '/images/cover-photo-1.png';
	let sofaHeroImage = '/home/sofa.svg';
	let heroImage3 = '/images/cover-photo-2.svg';
	let table = 'home/table.svg';
	let products: { name: string; price: string; temp: string; img: string; isAvailable: boolean }[] =
		[];
	let scrollRef: HTMLDivElement;
	let scrollRef2: HTMLDivElement;
	let loading = true;
	let reviewIndex = 1;
	let showDots = false;
	let isProductScrollAtStart = true;
	let isProductScrollAtEnd = false;

	const reviews = [
		{ img: '/reviews/review-1.svg', name: 'Review 1' },
		{ img: '/reviews/review-2.svg', name: 'Review 2' },
		{ img: '/reviews/review-3.svg', name: 'Review 3' },
		{ img: '/reviews/review-4.svg', name: 'Review 4' },
		{ img: '/reviews/review-5.svg', name: 'Review 5' },
		{ img: '/reviews/review-6.svg', name: 'Review 6' },
		{ img: '/reviews/review-7.svg', name: 'Review 7' }
	];

	onMount(async () => {
		try {
			const heroImages = await customizeStore.fetchAll();
			if (heroImages && heroImages.length > 0) {
				if (heroImages[0]?.image_path) {
					heroImage = `https://mabini-cafe.bscs3a.com/api/${heroImages[0].image_path.replace(/^\/?/, '')}`;
				}
				if (heroImages[1]?.image_path) {
					sofaHeroImage = `https://mabini-cafe.bscs3a.com/api/${heroImages[1].image_path.replace(/^\/?/, '')}`;
				}
				if (heroImages[2]?.image_path) {
					heroImage3 = `https://mabini-cafe.bscs3a.com/api/${heroImages[2].image_path.replace(/^\/?/, '')}`;
				}
			}

			const items = await menuStore.fetchAll();
			const filteredItems = items.filter(
				(item: any) =>
					item.isAvailable === 1 || item.isAvailable === '1' || item.isAvailable === true
			);
			products = filteredItems.map((item: any) => ({
				name: item.name,
				price: `₱${parseFloat(item.price).toFixed(2)}`,
				temp: item.category,
				img: item.image_path
					? `https://mabini-cafe.bscs3a.com/api/${item.image_path.replace(/^\/?/, '')}`
					: '',
				isAvailable: item.isAvailable
			}));
		} catch (error) {
			console.error('Failed to load data:', error);
		} finally {
			loading = false;
			setTimeout(() => handleProductScroll(), 0);
		}
	});

	function showDotRow() {
		showDots = true;
		setTimeout(() => (showDots = false), 1000);
	}

	function scrollRight() {
		if (reviewIndex < reviews.length - 1) {
			reviewIndex = Math.min(reviews.length - 1, reviewIndex + 1);
			showDotRow();
		}
	}

	function scrollLeft() {
		if (reviewIndex > 0) {
			reviewIndex = Math.max(0, reviewIndex - 1);
			showDotRow();
		}
	}

	function scrollRight2() {
		if (scrollRef) {
			scrollRef.scrollBy({ left: 300, behavior: 'smooth' });
		}
	}

	function scrollLeft2() {
		if (scrollRef) {
			scrollRef.scrollBy({ left: -300, behavior: 'smooth' });
		}
	}

	function handleProductScroll() {
		if (scrollRef) {
			isProductScrollAtStart = scrollRef.scrollLeft === 0;
			const maxScroll = scrollRef.scrollWidth - scrollRef.clientWidth;
			isProductScrollAtEnd = Math.abs(scrollRef.scrollLeft - maxScroll) < 2;
		}
	}
</script>

<svelte:head>
	<title>Mabini Cafe</title>
	<meta name="description" content="Welcome to Mabini Cafe" />
</svelte:head>

<section
	class="hero h-[50vh] sm:h-[60vh] md:h-[70vh] lg:h-[80vh] w-full flex justify-center items-center bg-cover bg-center text-white text-center"
	style="background-image: url('{heroImage}')"
></section>

<section
	class="hero-2 mt-10 sm:mt-16 md:mt-20 lg:mt-24 h-auto min-h-[60vh] sm:min-h-[70vh] md:min-h-[80vh] lg:min-h-[100vh] w-full flex justify-center items-start bg-cover bg-center text-center px-4 sm:px-6 md:px-8 lg:px-4 relative overflow-hidden"
>
	<div class="hero-content px-4 sm:px-8 md:px-16 lg:px-20 z-10">
		<h1
			class="text-xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl text-mabini-black uppercase font-sans-boldie font-extrabold leading-tight"
		>
			Choose Your
			<span class="text-mabini-yellow">Refresher</span>
		</h1>
		<h1
			class="block text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl text-mabini-dark-brown leading-none mt-0 font-black uppercase"
		>
			Cucumber Lemonade
		</h1>
	</div>
	<img
		src={sofaHeroImage}
		alt="Hero 2"
		class="absolute right-0 bottom-6 sm:bottom-8 md:bottom-10 lg:bottom-10 pointer-events-none select-none w-[350px] sm:w-[450px] md:w-[550px] lg:w-[800px]"
		style="z-index:1; max-width:none; transform:scale(1.2) sm:scale(1.3) md:scale(1.4) lg:scale(1.5);"
	/>
	<img
		src={table}
		alt="Hero 2"
		class="absolute left-0 right-0 bottom-0 m-auto pointer-events-none select-none w-[300px] sm:w-[350px] md:w-[420px] lg:w-[500px]"
		style="z-index:1; max-width:none; transform:scale(1.2) sm:scale(1.3) md:scale(1.4) lg:scale(1.5);"
	/>
</section>

<section
	class="hero-3 w-full min-h-[60vh] sm:min-h-[70vh] md:min-h-[80vh] flex items-center justify-start bg-cover bg-center text-white"
	style="background-image: url('{heroImage3}')"
>
	<div
		class="mr-12 ml-12 sm:mr-16 sm:ml-16 md:mr-20 md:ml-20 lg:mr-24 lg:ml-24 xl:mr-32 xl:ml-32 p-6 sm:p-8 md:p-12 lg:p-16 xl:p-20 w-full max-w-full sm:max-w-xl md:max-w-2xl lg:max-w-4xl"
	>
		<div class="flex flex-col justify-start items-start">
			<h1
				class="text-left font-black uppercase text-xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl leading-tight"
			>
				Discover Your <br />
				Favorite
			</h1>
			<h1
				class="tracking-wider text-left text-5xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-[10rem] font-cond text-mabini-yellow leading-none mt-2 font-black"
			>
				Croffle
			</h1>
			<p
				class="text-xs sm:text-sm md:text-base lg:text-lg pt-4 sm:pt-6 font-light text-white max-w-full sm:max-w-md md:max-w-lg lg:max-w-2xl"
			>
				Crispy, buttery, and topped with goodness.<br />
				Explore our wide variety of croffles made fresh daily.
			</p>
			<button
				on:click={() => goto('/menu?category=Croffle')}
				class="uppercase mt-6 sm:mt-6 font-semibold text-xs sm:text-sm md:text-base cursor-pointer w-full sm:w-auto min-w-[160px] sm:min-w-[180px] md:min-w-[200px] p-3 sm:p-3.5 md:p-4 rounded-full border-2 border-white hover:bg-mabini-dark-brown hover:border-transparent transition-all duration-300"
			>
				Explore Menu
			</button>
		</div>
	</div>
</section>

<section class="relative bg-black min-h-[70vh] sm:min-h-[80vh] md:min-h-[90vh] lg:min-h-[100vh]">
	<h1
		class="pt-8 sm:pt-10 md:pt-12 lg:pt-16 pb-8 sm:pb-10 md:pb-12 lg:pb-16 text-center uppercase font-black text-5xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl relative z-10 !text-mabini-white"
	>
		Receipt of <span class="!text-mabini-yellow"> Gratitude </span>
	</h1>
	<div class="paper">
		<!-- <img src="/images/Paper-1.svg" alt="" class="w-auto h-auto max-w-full max-h-full" /> -->
	</div>
	{#if reviews.length > 0}
		<div
			class="w-full flex justify-center items-center p-10 sm:p-8 md:p-10 lg:p-12 xl:p-16 pb-0 lg:pb-12"
		>
			<div class="flex justify-center items-center gap-2 h-5">
				{#each reviews as _, i}
					<div
						class="rounded-full border transition-all duration-300"
						style="
						width: {i === reviewIndex
							? window.innerWidth < 640
								? '24px'
								: '36px'
							: window.innerWidth < 640
								? '8px'
								: '12px'};
						height: {window.innerWidth < 640 ? '8px' : '12px'};
						background: {i === reviewIndex ? 'var(--color-mabini-dark-brown)' : '#fff'};
						border-color: transparent;
						margin: 0 3px;
					"
					></div>
				{/each}
			</div>
		</div>
	{/if}

	<div class="absolute inset-0 flex items-center justify-center pt-16 sm:pt-20 md:pt-24 lg:pt-0">
		<div
			class="w-full flex justify-center items-center gap-3 sm:gap-4 md:gap-6 lg:gap-8 xl:gap-12 px-2 sm:px-4 md:px-6 lg:px-8 relative z-10 max-w-7xl mx-auto"
		>
			<button
				on:click={() => {
					reviewIndex = Math.max(0, reviewIndex - 1);
					showDotRow();
				}}
				class="p-2 sm:p-3 md:p-3.5 lg:p-4 rounded-full bg-gray-200 hover:bg-mabini-yellow transition disabled:opacity-50 flex-shrink-0 flex justify-center items-center"
				aria-label="Scroll left"
				disabled={reviewIndex === 0}
			>
				<svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" fill="none" viewBox="0 0 24 24">
					<path
						d="M15 6l-6 6 6 6"
						stroke="#333"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					/>
				</svg>
			</button>

			<div
				class="mt-0 flex overflow-x-auto gap-4 sm:gap-6 py-4 sm:py-6 md:py-8 px-2 scrollbar-hide justify-center flex-1"
			>
				{#if reviews.length > 0}
					{#key reviewIndex}
						<div
							class="bg-transparent rounded-2xl p-2 sm:p-3 md:p-4 lg:p-5 xl:p-6 flex flex-col items-center justify-center transition-transform relative"
						>
							<img
								src={reviews[reviewIndex].img}
								alt={reviews[reviewIndex].name}
								class="w-[180px] h-[180px] sm:w-[220px] sm:h-[220px] md:w-[240px] md:h-[240px] lg:w-[350px] lg:h-[350px] xl:w-[320px] xl:h-[320px] object-contain mb-2 mt-2 drop-shadow-md"
								style="max-width:100%; max-height:400px;"
							/>
						</div>
					{/key}
				{/if}
			</div>

			<button
				on:click={() => {
					reviewIndex = Math.min(reviews.length - 1, reviewIndex + 1);
					showDotRow();
				}}
				class="p-2 sm:p-3 md:p-3.5 lg:p-4 rounded-full bg-gray-200 hover:bg-mabini-yellow transition disabled:opacity-50 flex-shrink-0 flex justify-center items-center"
				aria-label="Scroll right"
				disabled={reviewIndex === reviews.length - 1}
			>
				<svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" fill="none" viewBox="0 0 24 24">
					<path
						d="M9 6l6 6-6 6"
						stroke="#333"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					/>
				</svg>
			</button>
		</div>
	</div>
</section>

<section
	class="featured-products bg-white min-h-[70vh] sm:min-h-[80vh] md:min-h-[90vh] w-full py-8 sm:py-10 md:py-12 lg:py-16 px-4"
>
	<h1
		class=" uppercase font-black text-5xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl text-center mb-6 sm:mb-8 md:mb-10 lg:mb-12"
	>
		Featured <span class="text-mabini-yellow">Products</span>
	</h1>

	{#if loading}
		<p
			class="text-center text-gray-500 text-base sm:text-lg md:text-xl lg:text-2xl py-6 sm:py-8 md:py-10 lg:py-12"
		>
			Loading products...
		</p>
	{:else if products.length === 0}
		<p
			class="text-center text-gray-500 text-base sm:text-lg md:text-xl lg:text-2xl py-6 sm:py-8 md:py-10 lg:py-12"
		>
			No products available
		</p>
	{:else}
		<div
			class="flex items-center justify-center gap-2 sm:gap-3 md:gap-4 lg:gap-6 max-w-7xl mx-auto"
		>
			<button
				on:click={scrollLeft2}
				class="p-2 sm:p-3 md:p-3.5 lg:p-4 rounded-full bg-gray-200 hover:bg-mabini-yellow transition disabled:opacity-50 flex-shrink-0 flex justify-center items-center"
				disabled={isProductScrollAtStart}
				aria-label="Scroll left"
			>
				<svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" fill="none" viewBox="0 0 24 24">
					<path
						d="M15 6l-6 6 6 6"
						stroke="#333"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					/>
				</svg>
			</button>

			<div
				bind:this={scrollRef}
				on:scroll={handleProductScroll}
				class="flex overflow-x-auto gap-3 sm:gap-4 md:gap-5 lg:gap-6 py-4 sm:py-5 md:py-6 lg:py-8 px-2 scrollbar-hide flex-1"
			>
				{#each products.slice(0, 10) as product}
					<div
						class="min-w-[160px] sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] xl:min-w-[240px] bg-white rounded-2xl shadow-xl p-4 sm:p-5 md:p-5 lg:p-6 flex flex-col items-center justify-between hover:scale-105 transition flex-shrink-0"
					>
						<img
							src={product.img}
							alt={product.name}
							class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 lg:w-36 lg:h-36 xl:w-40 xl:h-40 object-contain mb-2 sm:mb-3 md:mb-3 lg:mb-4 drop-shadow-md"
						/>
						<div class="text-center w-full">
							<h2
								class="font-extrabold text-xs sm:text-sm md:text-base lg:text-lg mb-1 line-clamp-2"
							>
								{product.name}
							</h2>
							<p class="text-gray-400 font-bold text-xs sm:text-sm md:text-base lg:text-lg">
								{product.price}
							</p>
							<p class="text-gray-500 font-medium text-[10px] sm:text-xs md:text-sm lg:text-base">
								{product.temp}
							</p>
						</div>
					</div>
				{/each}
			</div>

			<button
				on:click={scrollRight2}
				class="p-2 sm:p-3 md:p-3.5 lg:p-4 rounded-full bg-gray-200 hover:bg-mabini-yellow transition disabled:opacity-50 flex-shrink-0 flex justify-center items-center"
				disabled={isProductScrollAtEnd}
				aria-label="Scroll right"
			>
				<svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" fill="none" viewBox="0 0 24 24">
					<path
						d="M9 6l6 6-6 6"
						stroke="#333"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					/>
				</svg>
			</button>
		</div>
	{/if}
</section>

<style>
	.scrollbar-hide::-webkit-scrollbar {
		display: none;
	}
	.scrollbar-hide {
		-ms-overflow-style: none;
		scrollbar-width: none;
	}

	.paper {
		background-image: url('/images/Paper-1.svg');
		background-repeat: no-repeat;
		background-position: center;
		background-size: contain;

		width: 35vw;
		aspect-ratio: 1 / 1.414;
		height: auto;
		margin: 0 auto;
	}

	/* Small screens */
	@media (max-width: 640px) {
		.paper {
			width: 50vw;
		}
	}

	/* Medium screens */
	@media (min-width: 641px) and (max-width: 1024px) {
		.paper {
			width: 35vw;
		}
	}

	/* Large screens */
	@media (min-width: 1025px) {
		.paper {
			width: 35vw;
		}
	}
</style>
