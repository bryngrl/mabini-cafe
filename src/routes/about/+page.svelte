<script>
	let images = [
		{ src: '/about/carousel-1.svg', alt: 'Cafe Interior' },
		{ src: '/about/carousel-2.svg', alt: 'Barista at Work' },
		{ src: '/about/carousel-3.svg', alt: 'Fresh Pastries' },
		{ src: '/about/carousel-4.svg', alt: 'Coffee Beans' },
		{ src: '/about/carousel-5.svg', alt: 'Latte Art' },
		{ src: '/about/carousel-6.svg', alt: 'Outdoor Seating' },
		{ src: '/about/carousel-7.svg', alt: 'Cafe Counter' }
	];

	let photos = [
		{ src: '/about/support-1.svg', alt: 'Support Image 1' },
		{ src: '/about/support-2.svg', alt: 'Support Image 2' },
		{ src: '/about/support-3.svg', alt: 'Support Image 3' },
		{ src: '/about/support-4.svg', alt: 'Support Image 4' },
		{ src: '/about/support-5.svg', alt: 'Support Image 5' }
	];

	let currentImage = 0;
	let currentPhoto = 0;

	function prev() {
		currentImage = (currentImage - 1 + images.length) % images.length;
	}

	function next() {
		currentImage = (currentImage + 1) % images.length;
	}

	function prev2() {
		currentPhoto = (currentPhoto - 1 + photos.length) % photos.length;
	}

	function next2() {
		currentPhoto = (currentPhoto + 1) % photos.length;
	}
</script>

<svelte:head>
	<title>About Us - Cafe</title>
	<meta
		name="description"
		content="Learn more about Mabini Cafe, our story, and our passion for coffee and pastries."
	/>
</svelte:head>

<!-- HERO + ABOUT -->
<div class="main-hero" style="background-image: url(/about/hero-1.svg);">
</div>
<div
	class="flex flex-col lg:pl-[200px] lg:pr-[200px] md:flex-row px-6 sm:px-10 md:px-16 lg:px-24 py-10 gap-0"
	style="min-height: 400px;"
>
	<div class="flex-1 min-h-[400px]">
		<img
			src="/about/component-1.svg"
			alt="About Mabini Cover"
			class="w-full h-full object-cover rounded-t-3xl md:rounded-l-3xl md:rounded-r-none"
		/>
	</div>

	<div
		class="flex-1 bg-white shadow-2xl rounded-b-3xl md:rounded-r-3xl md:rounded-l-none p-6 sm:p-10 md:p-12 lg:p-16 max-w-md md:max-w-lg lg:max-w-xl flex items-center min-h-[400px]"
	>
		<div>
			<h1 class="uppercase text-3xl md:text-4xl font-black font-sans">About</h1>
			<h1
				class="uppercase text-5xl md:text-7xl lg:text-8xl font-extrabold font-herman text-mabini-yellow"
			>
				Mabini Cafe
			</h1>
			<p class="text-left text-base sm:text-lg leading-relaxed mt-4">
				Way back May 24, 2023, Mabini Cafe opened its first branch at Maypajo, Caloocan City.
				Serving good quality concoctions of espresso-based drinks and mouth-watering food, Mabini
				Cafe aims to provide excellent coffee and gracious service creating memorable experiences
				for people.
			</p>
		</div>
	</div>
</div>

<!-- BLACK BACKGROUND SECTION -->
<div
	class="bg-[url('/about/black-bg.svg')] bg-cover bg-center bg-no-repeat flex flex-col items-center justify-end text-white text-center w-full min-h-[400px] sm:min-h-[500px] md:min-h-[600px] lg:min-h-[714px] px-4 sm:px-6 md:px-10 pb-10 sm:pb-10 md:pb-10 m-0"
>
	<h1 class="font-black text-3xl sm:text-4xl md:text-5xl lg:text-6xl leading-tight">
		BAKED WITH PASSION,<br />SERVED WITH LOVE.
	</h1>
	<p class="pt-3 sm:pt-5 text-sm sm:text-base md:text-lg font-light max-w-xl">
		Enjoy our artisanal treats in a place made for moments that matter.
	</p>
</div>

<!-- CAROUSEL 1 -->
<div class="bg-black w-full h-auto flex flex-col items-center mt-0 p-0">
	<div class="relative w-full h-[80vh] overflow-hidden rounded-none shadow-lg">
		<img
			src={images[currentImage].src}
			alt={images[currentImage].alt}
			class="w-full h-full object-cover transition-all duration-300"
		/>
		<!-- Prev -->
		<button
			class="button-left-arrow bg-opacity-70 hover:bg-opacity-100"
			on:click={prev}
			aria-label="Previous"
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

		<!-- Next -->
		<button class="button-right-arrow" on:click={next} aria-label="Next">
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

	<!-- Indicators -->
	<div class="flex gap-2 mt-4">
		{#each images as _, i}
			<div class="indicator {i === currentImage ? 'indicator-active' : ''}"></div>
		{/each}
	</div>
</div>

<!-- CAROUSEL 2 -->
<div class="bg-black w-full h-auto py-10 flex flex-col items-center text-center">
	<h1 class="text-white font-extrabold font-sans text-xl sm:text-2xl md:text-3xl">
		GRATEFUL FOR YOUR CONTINUED
	</h1>
	<h1 class="text-mabini-yellow font-sans-boldie text-5xl sm:text-6xl md:text-7xl lg:text-8xl">
		SUPPORT
	</h1>

	<div
		class="relative w-4/5 sm:w-3/4 md:w-[600px] h-[250px] sm:h-[300px] md:h-[400px] overflow-visible rounded-3xl shadow-lg mt-6"
	>
		<img
			src={photos[currentPhoto].src}
			alt={photos[currentPhoto].alt}
			class="w-full h-full object-cover transition-all duration-300 rounded-3xl"
		/>

		<!-- Prev -->
		<button
			class="absolute -left-10 sm:-left-14 md:-left-20 top-1/2 -translate-y-1/2 bg-white bg-opacity-70 rounded-full p-2 sm:p-3 shadow hover:bg-opacity-100"
			on:click={prev2}
			aria-label="Previous"
		>
			<svg width="20" height="20" fill="none">
				<path
					d="M13 5l-6 6 6 6"
					stroke="#333"
					stroke-width="2"
					stroke-linecap="round"
					stroke-linejoin="round"
				/>
			</svg>
		</button>

		<!-- Next -->
		<button
			class="absolute -right-10 sm:-right-14 md:-right-20 top-1/2 -translate-y-1/2 bg-white bg-opacity-70 rounded-full p-2 sm:p-3 shadow hover:bg-opacity-100"
			on:click={next2}
			aria-label="Next"
		>
			<svg width="20" height="20" fill="none">
				<path
					d="M9 5l6 6-6 6"
					stroke="#333"
					stroke-width="2"
					stroke-linecap="round"
					stroke-linejoin="round"
				/>
			</svg>
		</button>
	</div>

	<!-- Indicators -->
	<div class="flex gap-2 mt-4">
		{#each photos as _, i}
			<div
				class="rounded-full border transition-all duration-300"
				style="width: {i === currentPhoto ? '36px' : '12px'}; height: 12px; background: {i ===
				currentPhoto
					? 'var(--color-mabini-dark-brown)'
					: '#fff'}; border-color: transparent; margin: 0 2px;"
			></div>
		{/each}
	</div>
</div>
