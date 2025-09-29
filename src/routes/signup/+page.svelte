<!-- TODO: FETCH API ENDPOINT -->
<script lang="ts">
	let name = '';
	let email = '';
	let password = '';
	let confirmPassword = '';
	let loading = false;
	let message = '';
	let error = '';

	async function handleSignup(event: Event) {
		event.preventDefault();
		error = '';
		message = '';
		
		if (password !== confirmPassword) {
			error = 'Passwords do not match.';
			return;
		}
		loading = true;
		try {
			const response = await fetch('http://localhost/mabini-cafe/phpbackend/routes/users', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					name,
					email,
					password
				})
			});
			const data = await response.json();
			if (response.ok && data.success) {
				message = 'Account created successfully!';
				name = email = password = confirmPassword = '';
			} else {
				error = data.message || 'Signup failed.';
			}
		} catch (err) {
			error = 'Network error. Please try again.';
		} finally {
			loading = false;
		}
	}
</script>

<svelte:head>
	<title>Sign Up - Mabini Cafe</title>
	<meta name="description" content="Create your Mabini Cafe account" />
</svelte:head>
<div class="menu-page">
	<div class="container">
		<div class="page-header">
			<a href="/"><img src="/images/LOGO-4.png" alt="LOGO" style="filter: invert(1);" /></a>
			<h2 class="uppercase text-left">Create Account</h2>
			<p class="text-gray-600 font-normal text-left">
				Enter your email and we'll send you a sign-in code.
			</p>
		</div>
		<form on:submit|preventDefault={handleSignup}>
			<div class="inputBox">
				<input
					type="text"
					id="name"
					bind:value={name}
					class="form-input"
					placeholder="Enter your name"
					required
					disabled={loading}
				/>
			</div>
			<div class="inputBox">
				<input
					type="email"
					id="email"
					bind:value={email}
					class="form-input"
					placeholder="Enter your email"
					required
					disabled={loading}
				/>
			</div>
			<div class="inputBox">
				<input
					type="password"
					id="password"
					bind:value={password}
					class="form-input"
					placeholder="Enter your password"
					required
					disabled={loading}
				/>
			</div>
			<div class="inputBox">
				<input
					type="password"
					id="confirmPassword"
					bind:value={confirmPassword}
					class="form-input"
					placeholder="Confirm your password"
					required
					disabled={loading}
				/>
			</div>
			{#if error}
				<p style="color: red; padding-top: 0.5rem;">{error}</p>
			{/if}
			{#if message}
				<p style="color: green; padding-top: 0.5rem;">{message}</p>
			{/if}	
			<button type="submit" class="login-btn" disabled={loading}>
				{loading ? 'Signing up...' : 'Sign Up'}
			</button>
		</form>
	</div>
	<div class="login-footer">
		<p>Already have an account? <a href="/login">Log in here</a></p>
	</div>
</div>

<style>
	.menu-page {
		min-height: 100vh;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		background: black;
	}
	.container {
		background: white;
		padding: 2rem 2.5rem;
		border-radius: 1rem;
		box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
		min-width: 350px;
		max-width: 500px;
		height: 500px;
		width: 100%;
		text-align: center;
	}
	.page-header {
		text-align: center;
		margin-bottom: 20px;
		font-weight: 900;
	}
	.inputBox {
		margin-top: 1rem;
		text-align: left;
	}
	.inputBox input {
		width: 100%;
		height: 80%;
		padding: 0.75rem;
		border: 1px solid #ccc;
		border-radius: 0.5rem;
		font-size: 1rem;
	}
	.links {
		margin-top: 0.7rem;
		text-align: left;
	}
	.login-btn {
		width: 100%;
		margin-top: 1rem;
		padding: 0.75rem;
		background-color: black;
		color: white;
		border: none;
		border-radius: 1rem;
		font-size: 1rem;
		cursor: pointer;
		transition: background-color 0.3s ease;
	}
	img {
		display: block;
		margin: 0 auto;
	}
	.login-footer {
		text-align: center;
		margin-top: 2.5rem;
		color: white;
		width: 100%;
	}
</style>
