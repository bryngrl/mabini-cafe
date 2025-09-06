<!-- Details -->

<div class="footer-cover">
	<!-- Logo on the right and Details on left -->
	<div class="grid-container">
		<div class="column-one">
			<!-- right one  -->
		</div>
		<div class="column-two">
			<!-- left one -->
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

		<a href="https://facebook.com/yourusername" target="_blank" class="">
			<img src="facebook-icon.png" alt="Facebook" />
		</a>

		<a href="https://instagram.com/yourusername" target="_blank" class="">
			<img src="instagram-icon.png" alt="Instagram" />
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
	.grid-container {
		display: grid;
		grid-template-columns: 1fr 1fr; /* Creates two equal-width columns */
		gap: 20px; /* Adds space between columns */
	}
</style>
