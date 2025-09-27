<script lang="ts">
	import '../app.css';
	import Header from '$lib/components/layout/Header.svelte';
	import Footer from '$lib/components/layout/Footer.svelte';
	import { page } from '$app/stores';
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

{#if $page.url.pathname !== '/login' && $page.url.pathname !== '/signup'}
	<Header />
{/if}
<main class="">
	<slot />
</main>
{#if $page.url.pathname !== '/login' && $page.url.pathname !== '/signup'}
	<Footer />
{/if}


