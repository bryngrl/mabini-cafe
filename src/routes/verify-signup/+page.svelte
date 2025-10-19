<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/stores';
	import { showSuccess, showError } from '$lib/utils/sweetalert';
	import { otpStore, usersStore, otpLoading, otpError } from '$lib/stores';
	import { onMount } from 'svelte';

	let email = '';
	let name = '';
	let password = '';
	let otpCode = '';
	let resendLoading = false;

	$: loading = $otpLoading;

	$: if ($otpError) {
		showError($otpError, 'Error');
		otpStore.clearError();
	}

	onMount(() => {
		email = $page.url.searchParams.get('email') || '';
		name = $page.url.searchParams.get('name') || '';
		password = $page.url.searchParams.get('password') || '';

		if (!email || !name || !password) {
			showError('Invalid signup session. Please try again.', 'Error');
			goto('/signup');
		}
	});

	async function handleVerifyOtp() {
		if (otpCode.length !== 6) {
			await showError('Please enter a valid 6-digit code.', 'Invalid Code');
			return;
		}

		try {
			await otpStore.verifyOtp(email, otpCode);

			const result = await usersStore.signup({
				username: name,
				email: email,
				password: password
			});

			if (result && result.message) {
				await showSuccess(
					'Account created successfully! You can now log in.',
					'Welcome to Mabini Cafe!'
				);
				// Reset OTP store
				otpStore.reset();
				setTimeout(() => {
					goto('/login');
				}, 2000);
			}
		} catch (err: any) {
			await showError(err.message || 'Verification failed. Please try again.', 'Verification Failed');
		}
	}

	async function handleResendCode() {
		resendLoading = true;

		try {
			await otpStore.sendOtp(email);
			await showSuccess('Verification code resent! Please check your email.', 'Code Resent');
		} catch (err: any) {
			await showError(err.message || 'Failed to resend code. Please try again.', 'Error');
		} finally {
			resendLoading = false;
		}
	}

	function handleSignupWithDifferentEmail() {
		otpStore.reset();
		goto('/signup');
	}

	function handleGoToLogin() {
		otpStore.reset();
		goto('/login');
	}
</script>

<svelte:head>
	<title>Verify Email - Mabini Cafe</title>
	<meta name="description" content="Verify your email to complete signup" />
</svelte:head>

<div class="menu-page">
	<div class="container">
		<div class="page-header">
			<a href="/"><img src="/images/LOGO-4.png" alt="LOGO" style="filter: invert(1);" /></a>
			<h2 class="uppercase text-left">Enter Verification Code</h2>
			<p class="text-gray-600 font-normal text-left">
				We sent a 6-digit code to <strong>{email}</strong>
			</p>
		</div>

		<form on:submit|preventDefault={handleVerifyOtp}>
			<div class="inputBox">
				<input
					type="text"
					id="otpCode"
					bind:value={otpCode}
					class="form-input otp-input"
					placeholder="Enter 6-digit code"
					maxlength="6"
					pattern="[0-9]{6}"
					required
					disabled={loading}
					autocomplete="off"
				/>
			</div>

			<button type="submit" class="login-btn" disabled={loading || otpCode.length !== 6}>
				{loading ? 'Verifying...' : 'Verify & Create Account'}
			</button>
		</form>

		<div class="action-links">
			<button
				type="button"
				class="link-btn"
				on:click={handleResendCode}
				disabled={resendLoading}
			>
				{resendLoading ? 'Resending...' : 'Resend Code'}
			</button>
			<button type="button" class="link-btn" on:click={handleSignupWithDifferentEmail}>
				Sign up with different email
			</button>
			<button type="button" class="link-btn" on:click={handleGoToLogin}>
				Already have an account? Log in
			</button>
		</div>
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
		min-height: 500px;
		width: 100%;
		text-align: center;
	}
	.page-header {
		text-align: center;
		margin-bottom: 20px;
		font-weight: 900;
	}
	.page-header p {
		margin-top: 0.5rem;
	}
	.page-header strong {
		color: #000;
		word-break: break-all;
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
	.otp-input {
		text-align: center;
		font-size: 1.5rem;
		letter-spacing: 0.5rem;
		font-weight: 600;
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
	.login-btn:disabled {
		opacity: 0.6;
		cursor: not-allowed;
	}
	.login-btn:hover:not(:disabled) {
		background-color: #333;
	}
	img {
		display: block;
		margin: 0 auto;
	}
	.action-links {
		margin-top: 2rem;
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
	}
	.link-btn {
		background: none;
		border: none;
		color: #666;
		cursor: pointer;
		font-size: 0.9rem;
		padding: 0.5rem;
		transition: color 0.2s;
		text-decoration: underline;
	}
	.link-btn:hover:not(:disabled) {
		color: #000;
	}
	.link-btn:disabled {
		opacity: 0.6;
		cursor: not-allowed;
	}
</style>
