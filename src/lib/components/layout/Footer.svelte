<script>
	import SearchModal from '../ui/SearchModal.svelte';
	let cartModalOpen = false;
	let searchModalOpen = false;

	function closeSearch() {
		searchModalOpen = false;
	}
	function openSearch() {
		searchModalOpen = true;
	}


</script>

<div class="text-mabini-white">
	<div class="bg-mabini-black grid grid-cols-1 md:grid-cols-2 items-center p-6 md:p-[50px]">
		<!-- Column 1: Logo -->
		<div class="flex justify-center items-center mb-6 md:mb-0">
			<a href="/">
				<img
					src="/images/LOGO-4.png"
					alt="Mabini Cafe"
					class="w-[180px] h-[52px] md:w-[238px] md:h-[68px]"
				/>
			</a>
		</div>
		<!-- two-column text -->
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
			<div>
				<h3 class="font-bold mb-2 text-base md:text-lg">Maypajo Branch</h3>
				<p class="text-sm font-light">191 A. Mabini St., Maypajo Brgy 30, Caloocan, Philippines</p>

				<h3 class="font-bold mb-2 pt-3 text-base md:text-lg">10th Avenue Branch</h3>
				<p class="text-sm font-light">
					Anmars Bldg., 10th Avenue, Corner 3rd Street, Grace Park East, Brgy 91, Caloocan City,
					Caloocan, Philippines
				</p>
				<p class="text-sm font-light pt-5">
					Delivery service for Mabini Cafe is available in Metro Manila only.
				</p>
			</div>
			<div class="mt-6 md:mt-0">
				<h3 class="font-bold mb-1 text-base md:text-lg">Support</h3>
				<button class="block text-sm pt-3 md:pt-5 hover:text-mabini-yellow" on:click={openSearch}>Search</button>
				<a class="block text-sm pt-3 md:pt-5 hover:text-mabini-yellow" href="/orders-and-payment"
					>Order & Payment</a
				>
				<a class="block text-sm pt-3 md:pt-5 hover:text-mabini-yellow" href="/shipping">Shipping</a>
				<a class="block text-sm pt-3 md:pt-5 hover:text-mabini-yellow" href="/contact-us"
					>Contact us</a
				>
			</div>
		</div>
	</div>
</div>

<footer class="bg-mabini-dark-brown w-full">
	<div
		class="h-auto min-h-[80px] py-6 md:py-4 md:h-[60px] grid grid-cols-1 md:grid-cols-6 gap-6 md:gap-6 items-center px-4 md:px-8 text-mabini-dirty-white uppercase font-extrabold text-sm md:text-base overflow-visible"
	>
		<div
			class="col-span-1 md:col-span-3 flex flex-col md:flex-row items-center md:items-center gap-2 md:gap-4 md:ml-[100px] text-center md:text-left"
		>
			<p class="mb-2 md:mb-0">Condition of use and How to order</p>
			<a href="/privacy-policy" class="relative group hover:text-mabini-yellow"> Privacy policy </a>
		</div>

		<div
			class="col-span-1 md:col-span-2 flex flex-col md:flex-row justify-center items-center gap-2 md:gap-4 mt-4 md:mt-0"
		>
			<p class="mb-2 md:mb-0">Follow us on social media:</p>
			<div class="flex gap-4 md:gap-4 justify-center items-center min-h-[40px]">
				<a href="https://www.facebook.com/profile.php?id=100091862720618" target="_blank">
					<img
						src="/items/facebook.svg"
						alt="Facebook"
						width="32"
						height="32"
						class="w-8 h-8 hover:color-mabini-yellow"
					/>
				</a>
				<a href="https://instagram.com/mabinicafe_ph" target="_blank">
					<img src="/items/instagram.svg" alt="Instagram" width="32" height="32" class="w-8 h-8" />
				</a>
			</div>
		</div>
	</div>
</footer>
<SearchModal isOpen={searchModalOpen} onClose={closeSearch} />

<style>
	footer {
		margin-top: auto;
	}
</style>
