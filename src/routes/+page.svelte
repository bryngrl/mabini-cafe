<!-- !TO: DO
 ! [] Responsive
 ! [] Featured Products
-->
<!-- Home -->
<section class="hero">
	<div class="hero-content">
		<!-- 3 circles -->
	</div>
</section>

<section class="hero-2">
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
<section class="hero-3"></section>
<section class="paper">
	<div class="content">
		<h1 class="h1-heading !text-mabini-white pt-[50px] pb-[50px] text-center uppercase">
			Receipt of
			<span class="!text-mabini-yellow"> Gratitude </span>
		</h1>
		<img src="src/lib/assets/images/Paper-1.png" alt="Paper" class="mx-auto object-center" />
	</div>
</section>
<section class="featured-products">
	<div class="content">
		<h1 class="h1-heading pt-[70px] text-center uppercase">
			Featured
			<span class="!text-mabini-yellow"> Products </span>
		</h1>
	</div>
</section>

<style>
	.hero {
		height: 70vh;
		width: 100%;
		background: url('src/lib/assets/images/cover-photo-1.png') no-repeat center center;
		background-size: cover;
		display: flex;
		justify-content: center;
		align-items: center;
		color: white;
		text-align: center;
	}
	.hero-2 {
		margin-top: 100px;
		height: 100vh;
		width: 100%;
		background: url('src/lib/assets/images/sofa.png') no-repeat center 80%;
		background-size: cover;
		display: flex;
		justify-content: center;
		align-items: flex-start;
		color: white;
		text-align: center;
		background-position: center 15%;
	}

	.hero-3 {
		margin-top: 100px;
		height: 70vh;
		width: 100%;
		background: url('src/lib/assets/images/cover-photo-2.png') no-repeat center center;
		background-size: cover;
		display: flex;
		justify-content: center;
		align-items: top;
		color: white;
		text-align: center;
	}
	.paper {
		background-color: black;
		height: 100vh;
		width: 100%;
	}
	.featured-products {
		background-color: white;
		height: 70vh;
		width: 100%;
	}
</style>
