<script lang="ts">
	import { goto } from '$app/navigation';
	import { showSuccess, showError } from '$lib/utils/sweetalert';
	import { usersStore } from '$lib/stores';
	import { otpStore } from '$lib/stores';

	let name = '';
	let email = '';
	let password = '';
	let confirmPassword = '';
	let loading = false;
	let showPassword = false;
	let showConfirmPassword = false;

	// Email validation - only accept @gmail.com
	function validateEmail(email: string): boolean {
		const emailRegex = /^[a-zA-Z0-9._-]+@gmail\.com$/;
		return emailRegex.test(email);
	}

	// Password validation - minimum 8 characters
	function validatePassword(password: string): boolean {
		return password.length >= 8;
	}

	async function handleSignup() {
		loading = true;

		// Validate email
		if (!validateEmail(email)) {
			await showError('Please use a valid @gmail.com email address.', 'Invalid Email');
			loading = false;
			return;
		}

		// Validate password length
		if (!validatePassword(password)) {
			await showError('Password must be at least 8 characters long.', 'Invalid Password');
			loading = false;
			return;
		}

		if (password !== confirmPassword) {
			await showError('Passwords do not match.', 'Error');
			loading = false;
			return;
		}

		try {
			// Send OTP first before creating account
			await otpStore.sendOtpSignup(email);

			await showSuccess(
				'Verification code sent! Please check your email.',
				'Verify Your Email'
			);
			
			// Redirect to OTP verification with signup data (including password)
			setTimeout(() => {
				goto(`/verify-signup?email=${encodeURIComponent(email)}&name=${encodeURIComponent(name)}&password=${encodeURIComponent(password)}`);
			}, 2000);
		} catch (err: any) {
			await showError(err.message || 'Failed to send verification code. Please try again.', 'Error');
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
				Enter your details and we'll send you a verification code.
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
					placeholder="Enter your email (@gmail.com only)"
					required
					disabled={loading}
					pattern="[a-zA-Z0-9._-]+@gmail\.com"
					title="Please use a valid @gmail.com email address"
				/>
			</div>
			<div class="inputBox password-field">
				<input
					type={showPassword ? 'text' : 'password'}
					id="password"
					bind:value={password}
					class="form-input"
					placeholder="Enter your password (min. 8 characters)"
					required
					disabled={loading}
					minlength="8"
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
			<div class="inputBox password-field">
				<input
					type={showConfirmPassword ? 'text' : 'password'}
					id="confirmPassword"
					bind:value={confirmPassword}
					class="form-input"
					placeholder="Confirm your password"
					required
					disabled={loading}
				/>
				<button
					type="button"
					class="toggle-password"
					on:click={() => (showConfirmPassword = !showConfirmPassword)}
					aria-label={showConfirmPassword ? 'Hide password' : 'Show password'}
				>
					{showConfirmPassword ? 'Hide' : 'Show'}
				</button>
			</div>
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
		padding: 1.5rem;
		border-radius: 1rem;
		box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
		width: 90%;
		max-width: 500px;
		min-height: 500px;
		text-align: center;
	}
	@media (min-width: 640px) {
		.container {
			padding: 2rem 2.5rem;
			width: 100%;
		}
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
