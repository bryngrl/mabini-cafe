<!-- Home Page -->
<script>
	let heroImage = '/images/cover-photo-1.png';
	let heroImage2 = '/images/sofa.png';
	let heroImage3 = '/images/cover-photo-2.png';
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
		<h1 class="h1-heading pt-[70px] text-center uppercase">
			Featured
			<span class="!text-mabini-yellow"> Products </span>
		</h1>
		<!-- Featured Products Here -->
	</div>
</section>
