<!-- TODO: FETCH URL AND REDIRECT TSAKA STORE NG TOKEN -->

<script lang="ts">
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth';
	import { showSuccess, showError } from '$lib/utils/sweetalert';

	let email = '';
	let loading = false;
	let password = '';
	let showPassword = false;

	async function handleLogin() {
		loading = true;

		try {
			const result = await authStore.loginUser(email, password);
			
			if (result && result.token) {
				localStorage.setItem('token', result.token);
				await showSuccess('Login successful! Redirecting...', 'Welcome Back!');
				setTimeout(() => {
					goto('/');
				}, 1500);
			}
		} catch (err: any) {
			await showError(err.message || 'Login failed. Please check your credentials.', 'Login Failed');
			loading = false;
		}
	}
</script>

<svelte:head>
	<title>Login - Mabini Cafe</title>
	<meta name="description" content="Login to your Mabini Cafe account" />
</svelte:head>
<div class="menu-page">
	<div class="container">
		<div class="page-header">
			<a href="/"><img src="/images/LOGO-4.png" alt="LOGO" style="filter: invert(1);" /></a>
			<h2 class="uppercase text-left">Login</h2>
		</div>
		<form on:submit|preventDefault={handleLogin}>
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
			<div class="inputBox password-field">
				<input
					type={showPassword ? 'text' : 'password'}
					id="password"
					bind:value={password}
					class="form-input"
					placeholder="Enter your password"
					required
					disabled={loading}
				/>
				<button
					type="button"
					class="toggle-password"
					on:click={() => (showPassword = !showPassword)}
					aria-label={showPassword ? 'Hide password' : 'Show password'}
				>
					{showPassword ? 'Hide' : 'Show'}
				</button>
			</div>
			<button type="submit" class="login-btn" disabled={loading}>
				{loading ? 'Signing in...' : 'Sign In'}
			</button>
			<div class="links">
				<a href="#" class="text-gray-600">Forgot Password</a>
			</div>
		</form>
	</div>
	<div class="login-footer">
		<p>Don't have an account? <a href="/signup">Sign up here</a></p>
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
		height: 400px;
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
	.password-field {
		position: relative;
	}
	.password-field input {
		padding-right: 3rem;
	}
	.toggle-password {
		position: absolute;
		right: 0.75rem;
		top: 50%;
		transform: translateY(-50%);
		background: none;
		border: none;
		cursor: pointer;
		font-size: 0.875rem;
		font-weight: 500;
		padding: 0.25rem 0.5rem;
		color: #6b7280;
		transition: color 0.2s;
		user-select: none;
	}
	.toggle-password:hover {
		color: #374151;
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
