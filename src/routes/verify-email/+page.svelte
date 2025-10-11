<!-- Email Verification Page -->
<script>
	import { goto } from '$app/navigation';
	import { onMount } from 'svelte';

	let email = '';
	let verificationCode = '';
	let loading = false;
	let error = '';
	let message = '';
	let resendLoading = false;
	let resendMessage = '';

	// for email
	onMount(() => {
		const params = new URLSearchParams(window.location.search);
		email = params.get('email') || localStorage.getItem('pendingVerificationEmail') || '';
		
		if (!email) {
			error = 'No email found. Please sign up first.';
		}
	});
	//verfiy email
	async function handleVerify() {
		if (!verificationCode || verificationCode.length !== 6) {
			error = 'Please enter a valid 6-digit code.';
			return;
		}

		loading = true;
		error = '';
		message = '';

		try {
			const response = await fetch('http://localhost/mabini-cafe/phpbackend/routes/users/verify-email', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				credentials: 'include',
				body: JSON.stringify({
					email: email,
					code: verificationCode
				})
			});

			const result = await response.json();

			if (response.ok && result.success) {
				message = 'Email verified successfully! Redirecting to login...';
				localStorage.removeItem('pendingVerificationEmail');
				setTimeout(() => {
					goto('/login');
				}, 2000);
			} else {
				throw new Error(result.error || 'Verification failed');
			}
		} catch (err) {
			error = err.message || 'Verification failed. Please try again.';
			loading = false;
		}
	}

	async function handleResendCode() {
		if (!email) {
			error = 'No email found.';
			return;
		}

		resendLoading = true;
		resendMessage = '';
		error = '';
		//fetch to backend to resend code
		try {
			const response = await fetch('http://localhost/mabini-cafe/phpbackend/routes/users/resend-verification', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				credentials: 'include',
				body: JSON.stringify({ email: email })
			});

			const result = await response.json();

			if (response.ok && result.success) {
				resendMessage = 'Verification code resent! Check your email.';
			} else {
				throw new Error(result.error || 'Failed to resend code');
			}
		} catch (err) {
			error = err.message || 'Failed to resend code. Please try again.';
		} finally {
			resendLoading = false;
		}
	}

	function formatCode(value) {
		return value.replace(/\D/g, '').slice(0, 6);
	}

	function handleCodeInput(e) {
		verificationCode = formatCode(e.target.value);
	}
</script>

<svelte:head>
	<title>Verify Email - Mabini Cafe</title>
	<meta name="description" content="Verify your email address" />
</svelte:head>

<div class="verify-page">
	<div class="container">
		<div class="page-header">
			<a href="/"><img src="/images/LOGO-4.png" alt="LOGO" style="filter: invert(1);" /></a>
			<h2 class="uppercase">Verify Your Email</h2>
			<p class="text-gray-600 font-normal">
				We've sent a 6-digit verification code to:
			</p>
			<p class="email-display">{email}</p>
		</div>

		<form on:submit|preventDefault={handleVerify}>
			<div class="code-input-container">
				<label for="code">Enter Verification Code</label>
				<input
					type="text"
					id="code"
					bind:value={verificationCode}
					on:input={handleCodeInput}
					class="code-input"
					placeholder="000000"
					maxlength="6"
					pattern="[0-9]{6}"
					required
					disabled={loading}
					autocomplete="off"
				/>
			</div>

			{#if error}
				<p class="error-message">{error}</p>
			{/if}
			{#if message}
				<p class="success-message">{message}</p>
			{/if}
			{#if resendMessage}
				<p class="success-message">{resendMessage}</p>
			{/if}

			<button type="submit" class="verify-btn" disabled={loading || verificationCode.length !== 6}>
				{loading ? 'Verifying...' : 'Verify Email'}
			</button>

			<div class="resend-section">
				<p class="text-gray-600">Didn't receive the code?</p>
				<button
					type="button"
					class="resend-btn"
					on:click={handleResendCode}
					disabled={resendLoading}
				>
					{resendLoading ? 'Sending...' : 'Resend Code'}
				</button>
			</div>
		</form>
	</div>

	<div class="footer">
		<p>Already verified? <a href="/login">Log in here</a></p>
	</div>
</div>

<style>
	.verify-page {
		min-height: 100vh;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		background: black;
		padding: 2rem;
	}

	.container {
		background: white;
		padding: 2.5rem;
		border-radius: 1rem;
		box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
		min-width: 350px;
		max-width: 500px;
		width: 100%;
		text-align: center;
	}

	.page-header {
		text-align: center;
		margin-bottom: 2rem;
	}

	.page-header h2 {
		font-weight: 900;
		margin: 1rem 0;
	}

	.page-header p {
		margin: 0.5rem 0;
		font-size: 0.9rem;
	}

	.email-display {
		font-weight: 600;
		color: #000;
		font-size: 1rem;
		margin-top: 0.5rem;
		word-break: break-all;
	}

	.code-input-container {
		margin: 2rem 0;
	}

	.code-input-container label {
		display: block;
		text-align: left;
		margin-bottom: 0.5rem;
		font-weight: 500;
		font-size: 0.9rem;
	}

	.code-input {
		width: 100%;
		padding: 1rem;
		border: 2px solid #ccc;
		border-radius: 0.5rem;
		font-size: 1.5rem;
		text-align: center;
		letter-spacing: 0.5rem;
		font-weight: 600;
		transition: border-color 0.2s;
	}

	.code-input:focus {
		outline: none;
		border-color: #000;
	}

	.code-input:disabled {
		background-color: #f5f5f5;
		cursor: not-allowed;
	}

	.error-message {
		color: #dc2626;
		padding: 0.75rem;
		margin: 1rem 0;
		background-color: #fee;
		border-radius: 0.5rem;
		font-size: 0.9rem;
	}

	.success-message {
		color: #16a34a;
		padding: 0.75rem;
		margin: 1rem 0;
		background-color: #f0fdf4;
		border-radius: 0.5rem;
		font-size: 0.9rem;
	}

	.verify-btn {
		width: 100%;
		padding: 0.875rem;
		background-color: black;
		color: white;
		border: none;
		border-radius: 0.75rem;
		font-size: 1rem;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		margin-top: 1rem;
	}

	.verify-btn:hover:not(:disabled) {
		background-color: #333;
		transform: translateY(-1px);
	}

	.verify-btn:disabled {
		background-color: #999;
		cursor: not-allowed;
		transform: none;
	}

	.resend-section {
		margin-top: 2rem;
		padding-top: 1.5rem;
		border-top: 1px solid #e5e5e5;
	}

	.resend-section p {
		font-size: 0.9rem;
		margin-bottom: 0.75rem;
	}

	.resend-btn {
		background: none;
		border: none;
		color: #000;
		font-weight: 600;
		cursor: pointer;
		text-decoration: underline;
		font-size: 0.9rem;
		padding: 0.5rem;
		transition: color 0.2s;
	}

	.resend-btn:hover:not(:disabled) {
		color: #666;
	}

	.resend-btn:disabled {
		color: #999;
		cursor: not-allowed;
	}

	img {
		display: block;
		margin: 0 auto;
		max-width: 120px;
	}

	.footer {
		text-align: center;
		margin-top: 2rem;
		color: white;
	}

	.footer a {
		color: white;
		text-decoration: underline;
	}

	.footer a:hover {
		color: #ccc;
	}
</style>
