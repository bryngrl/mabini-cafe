<script lang="ts">
	import { goto } from '$app/navigation';
	import { menuStore, customizeStore } from '$lib/stores';
	import { onMount } from 'svelte';
	import { cartStore } from '$lib/stores';
	import { authStore } from '$lib/stores';
	import ItemModal from '$lib/components/ui/ItemModal.svelte';

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
	let showDots = true;
	let isProductScrollAtStart = true;
	let isProductScrollAtEnd = false;
	let selectedItem = null;
	let modalOpen = false;

	function openModal(product) {
		selectedItem = {
			...product,
			image_path: product.image_path || product.img
		};
		modalOpen = true;
	}
	function closeModal() {
		modalOpen = false;
		selectedItem = null;
	}

	async function handleAddToCart(product) {
		try {
			// Check if product is available
			if (!product.isAvailable || product.isAvailable === 0 || product.isAvailable === '0') {
				await showError('This item is currently unavailable.', 'Item Unavailable');
				return;
			}

			const quantity = 1;
			const subtotal = parseFloat(product.price.replace('₱', '')) * quantity;

			await cartStore.add({
				user_id: $authStore.user?.id,
				menu_item_id: product.id,
				quantity: quantity,
				subtotal: subtotal
			});

			// Show success message first, then open cart modal
			await showSuccess(`${product.name} has been added to your cart!`, 'Added to Cart');
			cartModalOpen = true;
		} catch (err: any) {
			if (err.type === 'LOGIN_REQUIRED') {
				const result = await showLoginRequired();
				if (result.isConfirmed) {
					goto('/login');
				}
				return;
			}

			await showError(err.message || 'Failed to add item to cart', 'Error');
			console.error('Error adding to cart:', err);
		}
	}

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
					heroImage = `http://localhost/mabini-cafe/phpbackend/${heroImages[0].image_path.replace(/^\/?/, '')}`;
				}
				if (heroImages[1]?.image_path) {
					sofaHeroImage = `http://localhost/mabini-cafe/phpbackend/${heroImages[1].image_path.replace(/^\/?/, '')}`;
				}
				if (heroImages[2]?.image_path) {
					heroImage3 = `https://mabini-cafe.bscs3a.com/phpbackend/${heroImages[2].image_path.replace(/^\/?/, '')}`;
				}
			}

			const items = await menuStore.fetchAll();
			const filteredItems = items.filter(
				(item: any) =>
					item.isAvailable === 1 || item.isAvailable === '1' || item.isAvailable === true
			);
			products = filteredItems.map((item: any) => ({
				id: item.id,
				name: item.name,
				price: `${parseFloat(item.price).toFixed(2)}`,
				temp: item.category,
				img: item.image_path
					? `https://mabini-cafe.bscs3a.com/phpbackend/${item.image_path.replace(/^\/?/, '')}`
					: '',
				isAvailable: item.isAvailable,
				description: item.description // if needed
			}));
		} catch (error) {
			console.error('Failed to load data:', error);
		} finally {
			loading = false;
			setTimeout(() => handleProductScroll(), 0);
		}
	});

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

	let drinks = [
		{ src: '/home/drinks-1.svg', name: 'Cucumber Lemonade' },
		{ src: '/home/drinks-2.svg', name: 'Blueberry Lemonade Fizz' },
		{ src: '/home/drinks-3.svg', name: 'Strawberry Passion Fruit Ade' },
		{ src: '/home/drinks-4.svg', name: 'Dragon Breath Ade' },
		{ src: '/home/drinks-5.svg', name: 'Peach Strawberry Lemon Tea' }
	];
	let drinksIndex = 0;
</script>
<svelte:head>
	<title>Mabini Cafe</title>
	<meta name="description" content="Welcome to Mabini Cafe" />
</svelte:head>

<!-- Cover Image -->
<section
class="main-hero"	
style="background-image: url('{heroImage}')"
></section>	
<!-- Choose your Refresher -->
<section
	class="hero-2 mt-10 sm:mt-16 md:mt-20 lg:mt-24 h-auto min-h-[60vh] sm:min-h-[70vh] md:min-h-[80vh] lg:min-h-[100vh] w-full flex justify-center items-start bg-cover bg-center text-center px-4 sm:px-6 md:px-8 lg:px-4 relative overflow-hidden"
>
	<div class="hero-content px-4 sm:px-8 md:px-16 lg:px-20 z-10">
		<h1 class="drinks-header leading-tight">
			Choose Your
			<span class="text-mabini-yellow">Refresher</span>
		</h1>
		<h1 class="drinks-header block text-mabini-dark-brown leading-none mt-0">
			{drinks[drinksIndex].name}
		</h1>
	</div>
	<img
		src={sofaHeroImage}
		alt="Hero 2"
		class="absolute right-0 bottom-6 sm:bottom-8 md:bottom-10 lg:bottom-10 pointer-events-none select-none w-[350px] sm:w-[450px] md:w-[550px] lg:w-[800px]"
		style="z-index:1; max-width:none; transform:scale(1.2);"
	/>
	<img
		src={table}
		alt="Hero 2"
		class="absolute left-0 right-0 bottom-0 m-auto pointer-events-none select-none w-[300px] sm:w-[350px] md:w-[420px] lg:w-[500px]"
		style="z-index:1; max-width:none; transform:scale(1.2);"
	/>

	{#if drinks.length > 0}
		<div class="flex items-center justify-center gap-4 mt-6">
			<!-- Left Arrow Button -->
			<button
				class="button-left-arrow"
				aria-label="Scroll left"
				on:click={() => (drinksIndex = (drinksIndex - 1 + drinks.length) % drinks.length)}
			>
				<svg width="20" height="20" class="sm:w-6 sm:h-6" fill="none">
					<path
						d="M15 6l-6 6 6 6"
						stroke="#333"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					/>
				</svg>
			</button>
			{#key drinksIndex}
				<img
					src={drinks[drinksIndex].src}
					alt={drinks[drinksIndex].name}
					class=" absolute top-1/4 left-1/2 transform -translate-x-1/2 pointer-events-none select-none z-10 w-37 sm:w-37 md:w-45 lg:w-60"
				/>
			{/key}
			<div class="absolute top-[60%] left-1/2 transform -translate-x-1/2 flex gap-2 z-20">
				{#each drinks as _, i}
					<div class="indicator {i === drinksIndex ? 'indicator-active' : ''}"></div>
				{/each}
			</div>

			<!-- Right Arrow Button -->
			<button
				class="button-right-arrow"
				aria-label="Scroll right"
				on:click={() => (drinksIndex = (drinksIndex + 1) % drinks.length)}
			>
				<svg width="20" height="20" class="sm:w-6 sm:h-6" fill="none">
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
<!-- Discovery -->
<section
	class="hero-3 div-layout flex items-center justify-start bg-cover bg-center text-white"
	style="background-image: url('{heroImage3}')"
>
	<div
		class=" p-6 sm:p-8 md:p-12 lg:p-16 xl:p-20 w-full max-w-full sm:max-w-xl md:max-w-2xl lg:max-w-4xl"
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
				class="text-sm sm:text-sm md:text-base lg:text-lg pt-4 sm:pt-6 font-light text-white max-w-full sm:max-w-md md:max-w-lg lg:max-w-2xl"
			>
				Crispy, buttery, and topped with goodness.<br />
				Explore our wide variety of croffles made fresh daily.
			</p>
			<button
				on:click={() => goto('/menu?category=Croffle')}
				class="uppercase mt-6 sm:mt-6 button-1"
			>
				Explore Menu
			</button>
		</div>
	</div>
</section>
<!-- Receipt of Gratitude -->
<section class=" bg-black div-layout">
	<h1 class="header-text pb-8 sm:pb-10 md:pb-12 lg:pb-0 relative z-10 text-mabini-white">
		Receipt of <span class="!text-mabini-yellow !font-black font-sans-boldie"> Gratitude </span>
	</h1>
	<div class="flex items-center justify-center">
		<div
			class="flex items-center gap-3 sm:gap-4 md:gap-6 lg:gap-8 xl:gap-12 justify-center px-2 sm:px-4 md:px-6 lg:px-8 relative z-10 w-full max-w-7xl"
		>
			<div class="paper relative"></div>
			<!-- Button Left -->
			<div>
				<button
					on:click={() => {
						reviewIndex = Math.max(0, reviewIndex - 1);
					}}
					class="button-left-arrow bg-opacity-70 hover:bg-opacity-100 flex-shrink-0"
					aria-label="Scroll left"
					disabled={reviewIndex === 0}
				>
					<svg width="20" height="20" class="sm:w-6 sm:h-6" fill="none">
						<path
							d="M15 6l-6 6 6 6"
							stroke="#333"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
						/>
					</svg>
				</button>
			</div>
			<!-- Images -->
			<div>
				<div
					class="mt-10 flex overflow-x-auto gap-4 sm:gap-6 py-4 sm:py-6 md:py-8 px-2 scrollbar-hide justify-center flex-1"
				>
					{#if reviews.length > 0}
						{#key reviewIndex}
							<div
								class="bg-transparent rounded-2xl p-2 sm:p-3 md:p-4 lg:p-5 xl:p-6 flex flex-col items-center justify-center transition-transform relative"
							>
								<img
									src={reviews[reviewIndex].img}
									alt={reviews[reviewIndex].name}
									class="w-[280px] h-[280px] sm:w-[320px] sm:h-[320px] md:w-[360px] md:h-[360px] lg:w-[400px] lg:h-[400px] xl:w-[440px] xl:h-[440px] object-contain mb-2 mt-2 drop-shadow-md"
									style="max-width:100%; max-height:600px;"
								/>

								<div
									class="flex justify-center items-center gap-1.5 sm:gap-2 md:gap-2.5 mt-2 sm:mt-3 md:mt-4 h-4 sm:h-5 md:h-6"
								>
									{#if showDots}
										{#each reviews as _, i}
											<div class="indicator {i === reviewIndex ? 'indicator-active' : ''}"></div>
										{/each}
									{/if}
								</div>
							</div>
						{/key}
					{/if}
				</div>
			</div>
			<!-- Button Right -->
			<div>
				<button
					on:click={() => {
						reviewIndex = Math.min(reviews.length - 1, reviewIndex + 1);
					}}
					class="button-right-arrow bg-opacity-70 hover:bg-opacity-100"
					aria-label="Scroll right"
					disabled={reviewIndex === reviews.length - 1}
				>
					<svg width="20" height="20" class="sm:w-6 sm:h-6" fill="none">
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
	</div>
</section>
<!-- Featured Products -->
<section class=" bg-white div-layout">
	<h1 class="header-text">
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
			class="relative flex items-center justify-center gap-2 sm:gap-3 md:gap-4 lg:gap-6 max-w-7xl mx-auto"
		>
			<!-- Button Left -->
			<div
				class="absolute left-0 top-0 h-full w-1/4 bg-gradient-to-r from-white via-white/80 to-transparent z-10 transition-opacity duration-300 pointer-events-none"
				class:bg-none={isProductScrollAtStart}
				class:z-0={isProductScrollAtStart}
			>
				<button
					on:click={scrollLeft2}
					class="button-left-arrow disabled:!opacity-0 pointer-events-auto"
					disabled={isProductScrollAtStart}
					aria-label="Scroll left"
				>
					<svg width="20" height="20" class="sm:w-6 sm:h-6" fill="none">
						<path
							d="M15 6l-6 6 6 6"
							stroke="#333"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
						/>
					</svg>
				</button>
			</div>
			<!-- Items -->
			<div
				bind:this={scrollRef}
				on:scroll={handleProductScroll}
				class="cursor-pointer flex overflow-x-auto gap-3 py-4 px-2 scrollbar-hide flex-1 p-[40px] sm:p-[60px] md:p-[80px] lg:p-[90px]"
			>
				{#each products.slice(0, 10) as product}
					<!-- svelte-ignore a11y_click_events_have_key_events -->
					<!-- svelte-ignore a11y_no_static_element_interactions -->
					<div
						class="min-w-[160px] sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] xl:min-w-[240px] bg-white rounded-2xl shadow-xl p-4 sm:p-5 md:p-5 lg:p-6 flex flex-col items-center justify-between hover:scale-105 transition flex-shrink-0"
						on:click={() => {
							openModal(product);
						}}
						role="button"
						tabindex="0"
						on:keydown={(e) => e.key === 'Enter' && openModal(product)}
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

			<ItemModal
				{selectedItem}
				{modalOpen}
				on:close={closeModal}
				on:addToCart={() => selectedItem && handleAddToCart(selectedItem)}
			/>
			<!-- Button Right -->
			<div
				class="absolute right-0 top-0 h-full w-1/4 bg-gradient-to-l from-white to-transparent pointer-events-none"
			>
				<button
					on:click={scrollRight2}
					class=" button-right-arrow pointer-events-auto"
					disabled={isProductScrollAtEnd}
					aria-label="Scroll right"
				>
					<svg width="20" height="20" class="sm:w-6 sm:h-6" fill="none">
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
	{/if}
</section>
