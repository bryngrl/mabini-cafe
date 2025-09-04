<script lang="ts">
	import '../app.css';
	import Navbar from '$lib/components/Navbar.svelte';
	import Footer from '$lib/components/Footer.svelte';
</script>

<Navbar />
<main class="min-h-screen px-4 py-6">
	<slot />
</main>
<Footer />
