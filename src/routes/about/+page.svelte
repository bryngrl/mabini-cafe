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
	<meta name="description" content="Learn more about Mabini Cafe, our story, and our passion for coffee and pastries." />
</svelte:head>

<div class="flex flex-col m-0 p-0">
	<div class="m-0 p-0 w-full flex justify-center">
		<img src="/about/hero-1.svg" alt="Cover Page" class="w-full h-auto shadow-lg" />
	</div>
	<div class="flex gap-0 flex-row items-center rounded-lg p-30 pb-0 pt-35 justify-center">
		<img src="/about/component-1.svg" alt="About Mabini Cover" />
		<div class="w-[513px] h-[608px] p-20 shadow-2xl rounded-r-4xl bg-white">
			<h1 class="uppercase text-4xl font-black font-sans">About</h1>
			<h1 class="uppercase text-8xl font-extrabold font-herman text-mabini-yellow">Mabini Cafe</h1>
			<p class="text-left justify-start text-lg">
				Way back May 24, 2023, Mabini Cafe opened its first branch at Maypajo, Caloocan City.
				Serving good quality concoctions of espresso based drinks and mouth-watering food, Coffee
				Project aims to provide excellent coffee and gracious service creating memorable experience
				for people.
			</p>
		</div>
	</div>
</div>
<div class="about-bg flex flex-col justify-center items-center text-white text-center">
	<h1 class="font-black pt-120 text-6xl w-full">BAKED WITH PASSION,<br /> SERVED WITH LOVE.</h1>
	<p class="pt-5 text-xl font-light w-full">
		Enjoy our artisanal treats in a place made for moments that matter.
	</p>
</div>
<!-- CAROUSEL 1 -->
<div class="bg-black w-full h-auto">
	<div class="flex flex-col items-center py-10">
		<div class="relative w-full h-auto overflow-hidden rounded-2xl shadow-lg">
			<img
				src={images[currentImage].src}
				alt={images[currentImage].alt}
				class="w-full h-full object-cover transition-all duration-300"
			/>
			<button
				class="absolute left-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-70 rounded-full p-2 shadow hover:bg-opacity-100"
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
			<button
				class="absolute right-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-70 rounded-full p-2 shadow hover:bg-opacity-100"
				on:click={next}
				aria-label="Next"
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
		<div class="flex gap-2 mt-4">
			{#each images as _, i}
				<div
					class="rounded-full border transition-all duration-300"
					style="width: {i === currentImage ? '36px' : '12px'}; height: 12px; background: {i ===
					currentImage
						? 'var(--color-mabini-dark-brown)'
						: '#fff'}; border-color: transparent; margin: 0 2px;"
				></div>
			{/each}
		</div>
	</div>
</div>

<!-- CAROUSEL 2 -->

<div class="bg-black w-full h-auto">
	<div class="flex flex-col items-center py-10">
		<h1 class="text-white font-extrabold font-sans text-2xl">GRATEFUL FOR YOUR CONTINUED</h1>
		<h1 class="text-mabini-yellow font-sans-boldie text-8xl">SUPPORT</h1>
		<div class="relative w-[600px] h-[400px] overflow-hidden rounded-3xl shadow-lg">
			<img
				src={photos[currentPhoto].src}
				alt={photos[currentPhoto].alt}
				class="w-full h-full object-cover transition-all duration-300"
			/>
			<button
				class="absolute left-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-70 rounded-full p-2 shadow hover:bg-opacity-100"
				on:click={prev2}
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
			<button
				class="absolute right-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-70 rounded-full p-2 shadow hover:bg-opacity-100"
				on:click={next2}
				aria-label="Next"
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
</div>

<style>
	.about-bg {
		background-image: url('/about/black-bg.svg');
		background-size: cover;
		background-repeat: no-repeat;
		background-position: center;
		width: 1540px;
		min-height: 714px;
	}
</style>
