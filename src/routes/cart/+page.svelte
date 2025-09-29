<!-- ABOUT THIS -->

<!-- Static only and not dynamic or interactive -->

<script lang="ts">
	function increment() {
		count += 1;
	}
	function decrement() {
		if (count > 0) {
			count -= 1;
		}
	}
	let count = 0;
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
				<!-- Items to checkout -->
			

				<div class="flex items-center gap-10 mb-4">
					<img
						src="/images/food-1.png"
						alt="Food Item"
						class="w-24 h-24 object-cover rounded-lg border border-gray-300"
					/>
					<div class="flex-1 flex justify-between items-center">
						<div class="flex items-center justify-between w-full">
							<div>
								<h3 class="text-lg font-semibold mb-5">Name</h3>
								<div
									class="flex items-center border rounded-lg px-2 py-1 gap-2 bg-gray-50 min-w-[120px]"
								>
									<button
										class="px-2 py-1 bg-white text-gray-700 font-bold text-sm"
										on:click={decrement}>-</button
									>
									<p class="text-gray-600 text-center font-semibold text-sm px-3">{count}</p>
									<button
										class="px-2 py-1 bg-white text-gray-700 font-bold text-sm"
										on:click={increment}>+</button
									>
								</div>
							</div>
							<button class="text-red-600 hover:text-red-800 transition ml-4 self-center pt-12"
								>Remove</button
							>
						</div>
						<p class="text-lg text-black font-bold pl-[300px]">₱10.00</p>
					</div>
				</div>
				<!-- Item 2 -->
				<div class="flex items-center gap-10 mb-4">
					<img
						src="/images/food-1.png"
						alt="Food Item"
						class="w-24 h-24 object-cover rounded-lg border border-gray-300"
					/>
					<div class="flex-1 flex justify-between items-center">
						<div class="flex items-center justify-between w-full">
							<div>
								<h3 class="text-lg font-semibold mb-5">Name</h3>
								<div
									class="flex items-center border rounded-lg px-2 py-1 gap-2 bg-gray-50 min-w-[120px]"
								>
									<button
										class="px-2 py-1 bg-white text-gray-700 font-bold text-sm"
										on:click={decrement}>-</button
									>
									<p class="text-gray-600 text-center font-semibold text-sm px-3">{count}</p>
									<button
										class="px-2 py-1 bg-white text-gray-700 font-bold text-sm"
										on:click={increment}>+</button
									>
								</div>
							</div>
							<button class="text-red-600 hover:text-red-800 transition ml-4 self-center pt-12"
								>Remove</button
							>
						</div>
						<p class="text-lg text-black font-bold pl-[300px]">₱10.00</p>
					</div>
				</div>
				<!-- Item 3 -->
				<div class="flex items-center gap-10 mb-4">
					<img
						src="/images/food-1.png"
						alt="Food Item"
						class="w-24 h-24 object-cover rounded-lg border border-gray-300"
					/>
					<div class="flex-1 flex justify-between items-center">
						<div class="flex items-center justify-between w-full">
							<div>
								<h3 class="text-lg font-semibold mb-5">Name</h3>
								<div
									class="flex items-center border rounded-lg px-2 py-1 gap-2 bg-gray-50 min-w-[120px]"
								>
									<button
										class="px-2 py-1 bg-white text-gray-700 font-bold text-sm"
										on:click={decrement}>-</button
									>
									<p class="text-gray-600 text-center font-semibold text-sm px-3">{count}</p>
									<button
										class="px-2 py-1 bg-white text-gray-700 font-bold text-sm"
										on:click={increment}>+</button
									>
								</div>
							</div>
							<button class="text-red-600 hover:text-red-800 transition ml-4 self-center pt-12"
								>Remove</button
							>
						</div>
						<p class="text-lg text-black font-bold pl-[300px]">₱10.00</p>
					</div>
				</div>

				<!-- ITEMS -->
			</div>
		</div>
	</div>

	<div class="flex-1 bg-white text-black flex p-20">
		<div class="bg-[#666666] rounded-xl p-10 w-full h-fit text-white">
			<div class="flex items-center justify-between w-full">
				<h2 class="text-[24px]">Subtotal</h2>
				<p class="text-lg font-[900] pl-20">₱30.00</p>
			</div>
			<p class="text-normal pt-5 pb-5">* shipping calculated at checkout.</p>
			<button 
				class="w-full bg-mabini-dark-brown font-bold rounded-full py-2 px-10 text-base tracking-wide text-center"
			>
				<a href="/place-order" class="w-full">
					Checkout - ₱30.00
				</a>
			</button>
		</div>
	</div>
</div>
