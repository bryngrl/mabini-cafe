<script>
	import Navbar from '$lib/components/Navbar.svelte';
	import Footer from '$lib/components/Footer.svelte';
</script>

<!-- This is the homepage like the main one -->
<Navbar />

<body class="flex flex-col min-h-screen">
	<main class="flex-grow">
		<!-- Cover -->
		<div class="h-[700px]">
			<img src="src\lib\assets\images\cover-photo-1.png" alt="Cover" class="" />
		</div>

		<!-- Refresher -->
		<div class="h-[1025px]">
			<h2 class="subheadings text-center !mb-0.5">
				<span>Choose Your </span>
				<span class="!text-mabini-beige">Refresher</span>
			</h2>
			<h1 class="text-center h1-headings !text-mabini-brown !mb-1">Cucumber <br /> Lemonade</h1>
		</div>
		<!-- Cover 2 -->
		<div class="h-[720px]">
			<img src="src\lib\assets\images\cover-photo-2.png" alt="Cover-2" />
		</div>

		<!-- Receipt of Gratitude -->
		<div class="h-[800px] bg-mabini-black">
			<h2 class="h1-headings !text-mabini-dirty-white text-center">Receipt of Gratitude</h2>
			<img src="src\lib\assets\images\Paper-1.png" alt="Paper" class="mx-auto" />
		</div>

		<!-- Featured Products -->
		<div class="h-[800px]">
			<h2 class="h1-headings text-center p-20">
				<span>Featured </span>
				<span class="inline !text-mabini-beige">Products </span>
			</h2>
			<!-- Products -->
			<!-- Next Button -->
		</div>
	</main>
</body>

<Footer />
