<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/stores';
	import { showSuccess, showError } from '$lib/utils/sweetalert';
	import { otpStore, otpLoading, otpError } from '$lib/stores';
	import { otpToken } from '$lib/stores/otp';

	import { onMount } from 'svelte';

	let email = '';
	$: email = $page.url.searchParams.get('email') ?? '';

	let otpCode = '';
	let newPassword = '';
	let confirmPassword = '';
	let resendLoading = false;
	let step: 'otp' | 'password' = 'otp';

	$: loading = $otpLoading;
	$: token = $otpToken;

	$: if ($otpError) {
		showError($otpError, 'Error');
		otpStore.clearError();
	}

	onMount(() => {
		email = $page.url.searchParams.get('email') || '';

		if (!email) {
			showError('Invalid password reset session. Please try again.', 'Error');
			goto('/login');
		}
	});

	async function handleVerifyOtp() {
		if (otpCode.length !== 6) {
			await showError('Please enter a valid 6-digit code.', 'Invalid Code');
			return;
		}

		if (!token) {
			await showError('Session expired. Please request a new code.', 'Error');
			return;
		}

		try {
			await otpStore.verifyOtp(token, otpCode);
			await showSuccess('Code verified! Please enter your new password.', 'Success');
			step = 'password';
		} catch (err: any) {
			await showError(
				err.message || 'Verification failed. Please try again.',
				'Verification Failed'
			);
		}
	}

	async function handleResetPassword() {
		if (newPassword.length < 6) {
			await showError('Password must be at least 6 characters long.', 'Invalid Password');
			return;
		}

		if (newPassword !== confirmPassword) {
			await showError('Passwords do not match.', 'Password Mismatch');
			return;
		}

		try {
			await otpStore.resetPassword(email, newPassword);
			await showSuccess(
				'Password reset successfully! You can now log in with your new password.',
				'Password Reset'
			);

			otpStore.reset();
			setTimeout(() => {
				goto('/login');
			}, 2000);
		} catch (err: any) {
			await showError(err.message || 'Password reset failed. Please try again.', 'Reset Failed');
		}
	}

	async function handleResendCode() {
		resendLoading = true;

		try {
			await otpStore.sendOtpForgotPassword(email);
			await showSuccess('Verification code resent! Please check your email.', 'Code Resent');
		} catch (err: any) {
			await showError(err.message || 'Failed to resend code. Please try again.', 'Error');
		} finally {
			resendLoading = false;
		}
	}

	function handleGoToLogin() {
		otpStore.reset();
		goto('/login');
	}
</script>

<svelte:head>
	<title>Reset Password - Mabini Cafe</title>
	<meta name="description" content="Reset your password" />
</svelte:head>

<div class="menu-page">
	<div class="container">
		<div class="page-header">
			<a href="/"><img src="/images/LOGO-4.png" alt="LOGO" style="filter: invert(1);" /></a>
			{#if step === 'otp'}
				<h2 class="text-left text-2xl font-extrabold text-mabini-dark-brown">Enter Code</h2>
				<p class="text-gray-600 font-normal text-left">
					We sent a 6-digit code to <strong>{email}</strong>
				</p>
			{:else}
				<h2 class="text-left text-2xl font-extrabold text-mabini-dark-brown">Reset Password</h2>
				<p class="text-gray-600 font-normal text-left">
					Enter your new password for <strong>{email}</strong>
				</p>
			{/if}
		</div>

		{#if step === 'otp'}
			<form on:submit|preventDefault={handleVerifyOtp}>
				<div class="inputBox">
					<input
						type="text"
						id="otpCode"
						bind:value={otpCode}
						class="form-input otp-input"
						placeholder="Enter 6-digit code"
						maxlength="6"
						inputmode="numeric"
						required
						disabled={loading}
						autocomplete="off"
					/>
				</div>

				<button type="submit" class="login-btn" disabled={loading || otpCode.length !== 6}>
					{loading ? 'Verifying...' : 'Verify Code'}
				</button>
			</form>

			<div class="action-links">
				<button type="button" class="login-btn !mt-0" on:click={handleResendCode} disabled={resendLoading}>
					{resendLoading ? 'Resending...' : 'Resend Code'}
				</button>
			</div>
		{:else}
			<form on:submit|preventDefault={handleResetPassword}>
				<div class="inputBox">
					<label for="newPassword">New Password</label>
					<input
						type="password"
						id="newPassword"
						bind:value={newPassword}
						class="form-input"
						placeholder="Enter new password"
						required
						disabled={loading}
						autocomplete="new-password"
					/>
				</div>

				<div class="inputBox">
					<label for="confirmPassword">Confirm Password</label>
					<input
						type="password"
						id="confirmPassword"
						bind:value={confirmPassword}
						class="form-input"
						placeholder="Confirm new password"
						required
						disabled={loading}
						autocomplete="new-password"
					/>
				</div>

				<button type="submit" class="login-btn" disabled={loading}>
					{loading ? 'Resetting...' : 'Reset Password'}
				</button>
			</form>
		{/if}
	</div>
	<button
		type="button"
		class="text-gray-600 underline hover:text-white p-5 cursor-pointer"
		on:click={handleGoToLogin}
	>
		Back to Login
	</button>
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
		min-height: 300px;
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
		margin-top: 0.75rem;
		display: flex;
		flex-direction: column;
		gap: 5px;
	}
	.link-btn {
		text-align: left;
		background: none;
		border: none;
		color: #666;
		cursor: pointer;
		font-size: 0.9rem;
		padding: 0.5rem;
		padding-bottom: 0;
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
