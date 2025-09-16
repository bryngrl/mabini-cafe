<!-- TODO
- Add form validation and form action and method
- Integrate with backend authentication
- Add "Forgot Password" link
- Redesign for mobile responsiveness
- Add social media login options
- Redesign the inputs and buttons

-->

<body>
	<div class="bg-black min-h-screen flex items-center justify-center">
		<div class="bg-white p-8 rounded-3xl shadow-md w-full max-w-md">
			<img src="src/lib/assets/logo/LOGO-4-BLACK.png" alt="Logo" class="mx-auto mb-6" />
			<h2 class="text-2xl font-bold mb-6 text-left">Sign Up</h2>
			<form>
				
				<input type="text" id="username" name="username" placeholder="Username" required />

				
				<input type="email" id="email" name="email" placeholder="Email" required />

				
				<input type="password" id="password" name="password" placeholder="Password" required />

				<button type="submit">Create Account</button>
			</form>
			<p>Already have an account? <a href="/login">Sign in!</a></p>
		</div>
	</div>
</body>
