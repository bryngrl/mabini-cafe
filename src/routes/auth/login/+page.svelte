<!-- 
TODO:
- Add form validation and form action and method
- Integrate with backend authentication
- Add "Forgot Password" link
- Redesign for mobile responsiveness
- Add social media login options
- Redesign the sizes and the colors
- Implement remember me functionality 
-->


<body>
	<div class="bg-black min-h-screen flex items-center justify-center">
		<div class="bg-white p-8 rounded-3xl shadow-md w-full max-w-md">
			<img src="src/lib/assets/logo/LOGO-4-BLACK.png" alt="Logo" class="mx-auto mb-6" />
			<h2 class="text-2xl font-bold mb-6 text-left">Login</h2>
			<form>
				<div class="mb-4">
					<label class="block text-gray-700" for="email">Email</label>
					<input
						class="mt-1 block w-full border border-gray-300 rounded p-2"
						type="email"
						id="email"
						required
					/>
				</div>
				<div class="mb-4">
					<label class="block text-gray-700" for="password">Password</label>
					<input
						class="mt-1 block w-full border border-gray-300 rounded p-2"
						type="password"
						id="password"
						required
					/>
				</div>
				<button class="btn-primary w-full" type="submit">Login</button>
			</form>
			<p class="mt-4 text-center text-gray-600">
				Don't have an account? <a href="/register" class="text-blue-500">Register</a>
			</p>
		</div>
	</div>
	<p class="mt-4 text-center text-gray-600">
		Don't have an account? <a href="/register" class="text-blue-500">Register</a>
	</p>
</body>
