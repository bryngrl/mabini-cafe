<script lang="ts">
	import '../app.css';
	import Header from '$lib/components/layout/Header.svelte';
	import Footer from '$lib/components/layout/Footer.svelte';
	import { page } from '$app/stores';
	import { authStore } from '$lib/stores';
	import { onMount } from 'svelte';

	onMount(() => {
		authStore.init();
	});

	let links = [
		'/login',
		'/signup',
		'/verify-email',
		'/checkout',
		'/checkout/shipping',
		'/checkout/payment',
		'/checkout/review'
	];
</script>

<link
	rel="stylesheet"
	href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
/>

{#if !links.includes($page.url.pathname) || $page.url.pathname.startsWith('/checkout', '/checkout/shipping', '/checkout/payment', '/checkout/review')}
	<Header />
{/if}
<main>
	<slot />
</main>
{#if !links.includes($page.url.pathname) && $page.url.pathname !== '/admin'}
	<Footer />
{/if}

<!-- {/if}

{#if $page.url.pathname !== '/login' && $page.url.pathname !== '/signup' && $page.url.pathname !== '/verify-email' && $page.url.pathname !== '/checkout'}
	<Footer />
{/if} -->
