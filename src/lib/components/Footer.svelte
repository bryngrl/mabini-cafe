<!-- !TO: DO
 ! [] Anchor in the Supports, Branches and other links
 ! [] Responsive
-->

<div class="footer-cover text-white py-10 pt-130">
	<div class="grid grid-cols-2 items-center">
		<!-- Column 1: Logo -->
		<div class="flex justify-center items-center">
			<img src="src/lib/assets/logo/LOGO-4.png" alt="Mabini Cafe" class="w-[238px] h-[68px]" />
		</div>
		<!-- two-column text -->
		<div class="grid grid-cols-2 gap-6">
			<div>
				<h3 class="font-bold mb-2">Maypajo Branch</h3>
				<p class="text-sm font-light">191 A. Mabini St., Maypajo Brgy 30, Caloocan, Philippines</p>

				<h3 class="font-bold mb-2 pt-3">10th Avenue Branch</h3>
				<p class="text-sm font-light">
					Anmars Bldg., 10th Avenue, Corner 3rd Street, Grace Park East, Brgy 91, Caloocan City,
					Caloocan, Philippines
				</p>
				<p class="text-sm font-light pt-5">
					Delivery service for Mabini Cafe is available in Metro Manila only.
				</p>
			</div>
			<div>
				<h3 class="font-bold mb-1">Support</h3>
				<p class="text-sm pt-5">Search</p>
				<p class="text-sm pt-5">Order & Payment</p>
				<p class="text-sm pt-5">Shipping</p>
				<p class="text-sm pt-5">Contact us</p>
			</div>
		</div>
	</div>
</div>

<footer class="bg-mabini-dark-brown flex items-center justify-center">
	<!--Div For Details are not yet ready -->

	<div
		class=" h-[60px] flex justify-center items-center gap-6 text-mabini-dirty-white uppercase text-extrabold text-base"
	>
		<p>Condition of use and How to order</p>

		<a href="/policy" class="relative group">
			Privacy policy
			<span class="underline-anim"></span></a
		>

		<p>Follow us on social media:</p>

		<a href="https://www.facebook.com/profile.php?id=100091862720618" target="_blank" class="">
			<img src="src/lib/assets/logo/facebook-icon.png" alt="Facebook" />
		</a>

		<a href="https://instagram.com/mabinicafe_ph" target="_blank" class="">
			<img src="src/lib/assets/logo/instagram-icon.png" alt="Instagram" />
		</a>
	</div>
</footer>

<style>
	.footer-cover {
		background-image: url('src/lib/assets/images/Footer-bg.png'); /* Relative path from static/public folder */
		background-size: cover;
		background-repeat: no-repeat;
		background-position: center center;
		height: 100vh;
	}
</style>
