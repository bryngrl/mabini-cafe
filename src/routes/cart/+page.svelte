<!-- Cart page -->
<script lang="ts">
	import { cart, fetchCart, addToCart } from '$lib/stores/cart';
	import { onMount } from 'svelte';

	onMount(() => {
		fetchCart();
	});

	async function increment(item) {
		const newQuantity = (item.quantity || 1) + 1;
		const newSubtotal = item.price * newQuantity;
		await updateCart(item.id, newQuantity, newSubtotal);
	}
	async function decrement(item) {
		if ((item.quantity || 1) > 1) {
			const newQuantity = item.quantity - 1;
			const newSubtotal = item.price * newQuantity;
			await updateCart(item.id, newQuantity, newSubtotal);
		}
	}
	async function remove(item) {
		try {
			const response = await fetch(
				`http://localhost/mabini-cafe/phpbackend/routes/cart/${item.id}`,
				{
					method: 'DELETE'
				}
			);
			if (response.ok) {
				await fetchCart();
			}
		} catch (err) {
			console.error('Error removing item from cart:', err);
		}
	}

	async function updateCart(cartId, quantity, subtotal) {
		try {
			const response = await fetch(
				`http://localhost/mabini-cafe/phpbackend/routes/cart/${cartId}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({ quantity, subtotal })
				}
			);
			if (response.ok) {
				await fetchCart();
			}
		} catch (err) {
			console.error('Error updating cart:', err);
		}
	}

	$: cartItems = $cart.items.reduce((acc, curr) => {
		const found = acc.find((i) => i.id === curr.id);
		if (found) {
			found.quantity += curr.quantity;
			found.subtotal += curr.subtotal;
		} else {
			acc.push({ ...curr });
		}
		return acc;
	}, []);

	$: subtotal = cartItems.reduce(
		(sum, item) =>
			sum +
			(typeof item.subtotal === 'number' && !isNaN(item.subtotal)
				? item.subtotal
				: item.price * (item.quantity || 1) || 0),
		0
	);
</script>

<svelte:head>
	<title>Cart - Mabini Cafe</title>
	<meta name="description" content="Manage your cart at Mabini Cafe" />
</svelte:head>

<div class="flex min-h-[100vh]">
	<div class="flex-1 bg-white text-black flex p-20">
		<div>
			<h2 class="text-2xl font-bold mb-4 text-left">YOUR CART</h2>
			<!-- formssssszzz -->
			<div>
				{#if cartItems.length === 0}
					<p>Your cart is empty.</p>
				{:else}
					{#each cartItems as item}
						<div class="flex items-center gap-10 mb-4">
							<img
								src={item.image}
								alt={item.name}
								class="w-24 h-24 object-cover rounded-lg border border-gray-300"
							/>

							<div class="flex-1 flex justify-between items-center">
								<div class="flex items-center justify-between w-full">
									<div class="flex flex-col justify-between h-full">
										<h3 class="text-lg font-semibold mb-2 min-h-[2.5rem] flex items-center">
											{item.name}
										</h3>
										<div
											class="flex items-center border rounded-lg px-2 py-1 gap-2 bg-gray-50 min-w-[120px]"
										>
											<button
												class="px-2 py-1 bg-white text-gray-700 font-bold text-sm"
												on:click={() => decrement(item)}>-</button
											>
											<p
												class="text-gray-600 text-center font-semibold text-sm px-3 flex items-center"
											>
												{item.quantity}
											</p>
											<button
												class="px-2 py-1 bg-white text-gray-700 font-bold text-sm"
												on:click={() => increment(item)}>+</button
											>
										</div>
									</div>
									<button
										class="text-red-600 hover:text-red-800 transition ml-3 mt-15 self-center h-10 flex items-center"
										on:click={() => remove(item)}
									>
										Remove
									</button>
								</div>
								<p class="text-lg text-black font-bold pl-[300px] pr-15">
									₱{(typeof item.subtotal === 'number' && !isNaN(item.subtotal)
										? item.subtotal
										: item.price * (item.quantity || 1) || 0
									).toFixed(2)}
								</p>
							</div>
						</div>
					{/each}
				{/if}
			</div>
		</div>
	</div>

	<div class="flex-1 bg-white text-black flex p-15">
		<div class="bg-[#666666] rounded-xl p-10 w-full h-fit text-white">
			<div class="flex items-center justify-between w-full">
				<h2 class="text-[24px]">Subtotal</h2>
				<p class="text-lg font-[900] pl-20">₱{subtotal.toFixed(2)}</p>
			</div>
			<p class="text-normal pt-5 pb-5">* shipping calculated at checkout.</p>
			<button
				class="w-full bg-mabini-dark-brown font-bold rounded-full py-2 px-10 text-base tracking-wide text-center"
				disabled={cartItems.length === 0}
			>
				{#if cartItems.length === 0}
					<span class="block text-center text-red-500 font-semibold" aria-live="polite">
						You cannot checkout with an empty cart.
					</span>
				{:else}
					<a href="/place-order" class="w-full">
						Checkout - ₱{subtotal.toFixed(2)}
					</a>
				{/if}
			</button>
		</div>
	</div>
</div>
