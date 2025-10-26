<!-- Home Page -->
<script lang="ts">
	import { goto } from '$app/navigation';
	import { menuStore } from '$lib/stores';
	import { redirect } from '@sveltejs/kit';
	import { onMount } from 'svelte';

	let heroImage = '/images/cover-photo-1.png';
	let heroImage2 = '/images/sofa.png';
	let heroImage3 = '/images/cover-photo-2.svg';
	let products: { name: string; price: string; temp: string; img: string }[] = [];
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
			const items = await menuStore.fetchAll();
			products = items.map((item: any) => ({
				name: item.name,
				price: `₱${parseFloat(item.price).toFixed(2)}`,
				temp: item.category,
				img: item.image_path
					? `http://localhost/mabini-cafe/phpbackend/${item.image_path.replace(/^\/?/, '')}`
					: ''
			}));
		} catch (error) {
			console.error('Failed to load menu items:', error);
		} finally {
			loading = false;
			// Ensure scroll button state is correct after products load
			setTimeout(() => handleProductScroll(), 0);
		}
		// Also check on mount
		setTimeout(() => handleProductScroll(), 0);
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

<section class="hero-3" style="background-image: url('{heroImage3}')">
	<div class="p-30">
		<div class="flex flex-col justify-start items-start p-0 gap-0 m-0">
			<h1 class="text-left font-[1000] uppercase font-sans text-[2rem] m-0 p-0 leading-none">
				Discover Your <br />
				<span class="text-left font-[1000] uppercase font-sans text-[2rem] m-0 p-0 leading-none"
					>Favorite</span
				>
			</h1>
			<div class="m-0 p-0">
				<h1 class="text-left text-[10rem] font-cond text-mabini-yellow m-0 p-0 leading-none">
					Croffle
				</h1>
				<p class="font-sans text-lg pt-20 font-semibold pb-10">
					CRISPY, BUTTERY, AND TOPPED <br /> WITH GOODNESS — EXPLORE OUR WIDE VARIETY <br /> OF CROFFLES
					MADE FRESH DAILY
				</p>

				<button
					class="uppercase font-bold text-md cursor-pointer w-full sm:w-[200px] p-5 pt-2 pb-2 rounded-full border-white border-1 hover:bg-mabini-dark-brown hover:border-transparent"
				>
					Explore Menu
				</button>
			</div>
		</div>
	</div>
</section>

<section class="paper">
	<div class="content justify-center">
		<h1 class="h1-heading !text-mabini-white pt-[50px] pb-[50px] text-center uppercase">
			Receipt of
			<span class="!text-mabini-yellow"> Gratitude </span>
		</h1>
		<div class="flex items-center gap-30 justify-center">
			<!-- Button for Left -->
			<button
				on:click={() => { reviewIndex = Math.max(0, reviewIndex - 1); showDotRow(); }}
				class="p-2 mr-10 rounded-full bg-gray-200 hover:bg-mabini-yellow transition-colors {reviewIndex === 0 ? 'cursor-not-allowed opacity-50' : ''}"
				aria-label="Scroll left"
				disabled={reviewIndex === 0}
			>
				<svg width="24" height="24" fill="none">
					<path
						d="M15 6l-6 6 6 6"
						stroke="#333"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					/>
				</svg>
			</button>
			<div class="flex overflow-x-auto gap-6 py-6 px-2 scrollbar-hide justify-center">
				<!-- Show only one review at a time -->
				{#if reviews.length > 0}
					{#key reviewIndex}
						<div
							class="bg-transparent rounded-2xl p-6 flex flex-col items-center justify-between transition-transform relative"
						>
							<img
								src={reviews[reviewIndex].img}
								alt={reviews[reviewIndex].name}
								class="w-[300px] h-[300px] object-contain mb-2 mt-2 drop-shadow-md"
								style="max-width:400px; max-height:400px;"
							/>
							<!-- Dot pagination row, only show when showDots is true -->
							<div class="flex justify-center items-center gap-3 mt-4" style="height: 24px;">
								{#if showDots}
									{#each reviews as _, i}
										<div
											class="rounded-full border transition-all duration-300"
											style="width: {i === reviewIndex ? '36px' : '12px'}; height: 12px; background: {i === reviewIndex ? 'var(--color-mabini-dark-brown)' : '#fff'}; border-color: transparent; margin: 0 2px;"
										></div>
									{/each}
								{:else}
									<!-- Empty space to reserve height -->
								{/if}
							</div>
						</div>
					{/key}
				{/if}
			</div>
			<!-- Button for Right -->
			<button
				on:click={() => { reviewIndex = Math.min(reviews.length - 1, reviewIndex + 1); showDotRow(); }}
				class="p-2 ml-10 rounded-full bg-gray-200 hover:bg-mabini-yellow transition-colors {reviewIndex === reviews.length - 1 ? 'cursor-not-allowed opacity-50' : ''}"
				aria-label="Scroll right"
				disabled={reviewIndex === reviews.length - 1}
			>
				<svg width="24" height="24" fill="none">
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
<section class="featured-products">
	<div class="content">
		<h1 class="h1-heading pt-[70px] text-center uppercase mb-30">
			Featured
			<span class="!text-mabini-yellow"> Products </span>
		</h1>

		<!-- Featured Products -->
		{#if loading}
			<div class="flex justify-center py-12">
				<p class="text-gray-500 text-lg">Loading products...</p>
			</div>
		{:else if products.length === 0}
			<div class="flex justify-center py-12">
				<p class="text-gray-500 text-lg">No products available</p>
			</div>
		{:else}
			<div class="flex items-center gap-4 justify-center">
				<button
					on:click={scrollLeft2}
					class="p-2 rounded-full bg-gray-200 hover:bg-mabini-yellow transition-colors {isProductScrollAtStart ? 'cursor-not-allowed opacity-50' : ''}"
					aria-label="Scroll left"
					disabled={isProductScrollAtStart}
				>
					<svg width="24" height="24" fill="none">
						<path
							d="M15 6l-6 6 6 6"
							stroke="#333"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
						/>
					</svg>
				</button>
				<div bind:this={scrollRef} class="flex overflow-x-auto gap-6 py-6 px-2 scrollbar-hide" on:scroll={handleProductScroll}>
					{#each products.slice(0, 10) as product}
						<div
							class="min-w-[260px] max-w-[260px] min-h-[370px] bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center justify-between transition-transform hover:scale-105 relative"
						>
							<img
								src={product.img}
								alt={product.name}
								class="w-40 h-40 object-contain mb-2 mt-2 drop-shadow-md"
							/>
							<div class="flex flex-col justify-end w-full flex-1">
								<div
									class="font-extrabold text-lg text-left mb-2 mt-4 w-full"
									style="letter-spacing:0.5px"
								>
									{product.name}
								</div>
								<div class="flex items-center justify-between w-full mt-2">
									<span class="text-gray-400 font-semibold">{product.price}</span>
									<span class="text-gray-500 text-base font-medium">{product.temp}</span>
								</div>
							</div>
						</div>
					{/each}
				</div>
				<button
					on:click={scrollRight2}
					class="p-2 rounded-full bg-gray-200 hover:bg-mabini-yellow transition-colors {isProductScrollAtEnd ? 'cursor-not-allowed opacity-50' : ''}"
					aria-label="Scroll right"
					disabled={isProductScrollAtEnd}
				>
					<svg width="24" height="24" fill="none">
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
	.hero {
		height: 70vh;
		width: 100%;
		background-size: cover;
		display: flex;
		justify-content: center;
		align-items: center;
		color: white;
		text-align: center;
	}
	.hero-2 {
		margin-top: 100px;
		height: 100vh;
		width: 100%;
		background-size: cover;
		display: flex;
		justify-content: center;
		align-items: flex-start;
		color: white;
		text-align: center;
		background-position: center 15%;
	}

	.hero-3 {
		margin-top: 20px;
		height: 753px;
		width: 100%;
		background-size: cover;
		display: flex;
		align-items: top;
		color: white;
	}
	.paper {
		background-color: black;
		height: 100vh;
		width: 100%;
		background-image: url('/images/Paper-1.png');
		background-repeat: no-repeat;
		background-position: center;
		background-size: 80vh;
	}
	.featured-products {
		background-color: white;
		height: 100vh;
		width: 100%;
	}
</style>
