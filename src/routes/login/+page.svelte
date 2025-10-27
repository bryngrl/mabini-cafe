<script lang="ts">
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth';
	import { otpStore } from '$lib/stores';
	import { showSuccess, showError } from '$lib/utils/sweetalert';
	import Swal from 'sweetalert2';

	let email = '';
	let loading = false;
	let password = '';
	let showPassword = false;

	// Email validation - only accept @gmail.com
	function validateEmail(email: string): boolean {
		const emailRegex = /^[a-zA-Z0-9._-]+@gmail\.com$/;
		return emailRegex.test(email);
	}

	async function handleLogin() {
		loading = true;

		// Validate email format
		if (!validateEmail(email)) {
			await showError('Please use a valid @gmail.com email address.', 'Invalid Email');
			loading = false;
			return;
		}

		try {
			try {
				const adminResult = await authStore.loginAdmin(email, password);
				if (adminResult && adminResult.info) {
					const token = adminResult.token || localStorage.getItem('token');
					if (token) {
						localStorage.setItem('token', token);
					}
					localStorage.setItem(
						'user',
						JSON.stringify({
							...adminResult.info,
							role: 'admin'
						})
					);

					await showSuccess(
						'Admin login successful! Redirecting to dashboard...',
						'Welcome Back Admin!'
					);
					setTimeout(() => {
						goto('/admin');
					}, 1500);
					return;
				}
			} catch (adminErr) {
				console.log('Not an admin, trying user login...');
			}

			const userResult = await authStore.loginUser(email, password);
			if (userResult && userResult.token && userResult.info) {
				localStorage.setItem('token', userResult.token);
				localStorage.setItem(
					'user',
					JSON.stringify({
						...userResult.info,
						role: 'user'
					})
				);

				await showSuccess('Login successful! Redirecting...', 'Welcome Back!');
				setTimeout(() => {
					goto('/');
				}, 1500);
			} else {
				throw new Error('Invalid login response from server');
			}
		} catch (err: any) {
			await showError(
				err.message || 'Login failed. Please check your credentials.',
				'Login Failed'
			);
			loading = false;
		}
	}

	async function handleForgotPassword() {
		const { value: resetEmail } = await Swal.fire({
			title: 'Reset Password',
			input: 'email',
			inputLabel: 'Enter your @gmail.com email address',
			inputPlaceholder: 'your@gmail.com',
			inputValue: email,
			showCancelButton: true,
			confirmButtonText: 'Send Code',
			confirmButtonColor: '#000', // Black button
			cancelButtonColor: '#6b7280', // Gray button
			background: '#fff', // White background
			color: '#000', // Black text
			customClass: {
				popup: 'swal-popup-mabini',
				confirmButton: 'swal-confirm-mabini',
				cancelButton: 'swal-cancel-mabini',
				input: 'swal-input-mabini'
			},
			inputValidator: (value) => {
				if (!value) {
					return 'Email is required';
				}
				const emailRegex = /^[a-zA-Z0-9._-]+@gmail\.com$/;
				if (!emailRegex.test(value)) {
					return 'Please use a valid @gmail.com email address';
				}
			}
		});

		if (resetEmail) {
			try {
				await otpStore.sendOtpForgotPassword(resetEmail);
				await showSuccess('Verification code sent! Check your email.', 'Code Sent');
				goto(`/forgot-password?email=${encodeURIComponent(resetEmail)}`);
				// add a page to redirect for inputting an otp
			} catch (err: any) {
				await showError(err.message || 'Failed to send code. Please try again.', 'Error');
			}
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
			<div class="links">
				<button type="button" on:click={handleForgotPassword} class="forgot-link"
					>Forgot Password</button
				>
			</div>
			<button type="submit" class="login-btn" disabled={loading}>
				{loading ? 'Signing in...' : 'Sign In'}
			</button>
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
		padding: 1.5rem;
		border-radius: 1rem;
		box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
		width: 90%;
		max-width: 500px;
		min-height: 400px;
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
	.links {
		margin-top: 0.5rem;
		text-align: right;
	}
	.forgot-link {
		text-align: left;
		background: none;
		border: none;
		color: #6b7280;
		cursor: pointer;
		font-size: 1rem;
		padding: 0;
		text-decoration: underline;
		transition: color 0.2s;
	}
	.forgot-link:hover {
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
