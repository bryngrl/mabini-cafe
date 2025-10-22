<script lang="ts">
	import { createEventDispatcher } from 'svelte';
	import { onMount } from 'svelte';

	// Props
	export let email: string = '';
	export let loading: boolean = false;
	export let purpose: 'signup' | 'forgot-password' | 'phone-change' | 'email-change' = 'signup';
	
	// Internal state
	let otpDigits: string[] = $state(['', '', '', '', '', '']);
	let inputs: HTMLInputElement[] = [];
	let resendCountdown = $state(0);
	let resendLoading = $state(false);

	const dispatch = createEventDispatcher();

	// Computed OTP code
	$effect(() => {
		const code = otpDigits.join('');
		if (code.length === 6) {
			dispatch('verify', { code });
		}
	});

	onMount(() => {
		// Auto-focus first input
		if (inputs[0]) {
			inputs[0].focus();
		}
	});

	function handleInput(index: number, event: Event) {
		const target = event.target as HTMLInputElement;
		const value = target.value;

		// Only allow digits
		if (value && !/^\d$/.test(value)) {
			target.value = '';
			return;
		}

		otpDigits[index] = value;

		// Auto-focus next input
		if (value && index < 5) {
			inputs[index + 1]?.focus();
		}
	}

	function handleKeyDown(index: number, event: KeyboardEvent) {
		// Backspace: clear current and focus previous
		if (event.key === 'Backspace') {
			if (!otpDigits[index] && index > 0) {
				inputs[index - 1]?.focus();
			}
			otpDigits[index] = '';
		}
		
		// Arrow keys navigation
		if (event.key === 'ArrowLeft' && index > 0) {
			inputs[index - 1]?.focus();
		}
		if (event.key === 'ArrowRight' && index < 5) {
			inputs[index + 1]?.focus();
		}
	}

	function handlePaste(event: ClipboardEvent) {
		event.preventDefault();
		const pastedData = event.clipboardData?.getData('text') || '';
		const digits = pastedData.replace(/\D/g, '').slice(0, 6).split('');
		
		digits.forEach((digit, index) => {
			if (index < 6) {
				otpDigits[index] = digit;
			}
		});

		// Focus last filled input or first empty
		const lastFilledIndex = Math.min(digits.length - 1, 5);
		inputs[lastFilledIndex]?.focus();
	}

	async function handleResend() {
		if (resendCountdown > 0 || resendLoading) return;
		
		resendLoading = true;
		dispatch('resend', { email });
		
		// Start 60-second countdown
		resendCountdown = 60;
		const interval = setInterval(() => {
			resendCountdown--;
			if (resendCountdown <= 0) {
				clearInterval(interval);
				resendLoading = false;
			}
		}, 1000);
	}

	function getPurposeText() {
		switch (purpose) {
			case 'signup':
				return 'verify your email';
			case 'forgot-password':
				return 'reset your password';
			case 'phone-change':
				return 'verify your new phone number';
			case 'email-change':
				return 'verify your new email';
			default:
				return 'verify your identity';
		}
	}
</script>

<div class="otp-container">
	<div class="otp-header">
		<h2 class="otp-title">Enter Verification Code</h2>
		<p class="otp-description">
			We've sent a 6-digit code to <strong>{email}</strong> to {getPurposeText()}.
		</p>
	</div>

	<div class="otp-inputs">
		{#each otpDigits as digit, index}
			<input
				bind:this={inputs[index]}
				type="text"
				inputmode="numeric"
				maxlength="1"
				value={digit}
				oninput={(e) => handleInput(index, e)}
				onkeydown={(e) => handleKeyDown(index, e)}
				onpaste={handlePaste}
				disabled={loading}
				class="otp-input"
				class:filled={digit !== ''}
				aria-label={`Digit ${index + 1}`}
			/>
		{/each}
	</div>

	<div class="otp-actions">
		<button
			type="button"
			onclick={handleResend}
			disabled={resendCountdown > 0 || resendLoading}
			class="resend-button"
		>
			{#if resendLoading}
				Sending...
			{:else if resendCountdown > 0}
				Resend in {resendCountdown}s
			{:else}
				Resend Code
			{/if}
		</button>
	</div>

	{#if loading}
		<div class="otp-loading">
			<div class="spinner"></div>
			<p>Verifying code...</p>
		</div>
	{/if}
</div>

<style>
	.otp-container {
		width: 100%;
		max-width: 500px;
		margin: 0 auto;
		padding: 2rem;
	}

	.otp-header {
		text-align: center;
		margin-bottom: 2rem;
	}

	.otp-title {
		font-size: 1.75rem;
		font-weight: bold;
		color: #2a1c0f;
		margin-bottom: 0.5rem;
	}

	.otp-description {
		font-size: 0.95rem;
		color: #666;
		line-height: 1.5;
	}

	.otp-description strong {
		color: #2a1c0f;
		font-weight: 600;
	}

	.otp-inputs {
		display: flex;
		gap: 0.75rem;
		justify-content: center;
		margin-bottom: 1.5rem;
	}

	.otp-input {
		width: 3.5rem;
		height: 3.5rem;
		text-align: center;
		font-size: 1.5rem;
		font-weight: bold;
		border: 2px solid #d1d5db;
		border-radius: 0.5rem;
		background: white;
		transition: all 0.2s;
		outline: none;
	}

	.otp-input:focus {
		border-color: #ffae00;
		box-shadow: 0 0 0 3px rgba(255, 174, 0, 0.1);
	}

	.otp-input.filled {
		border-color: #2a1c0f;
		background-color: #fffef8;
	}

	.otp-input:disabled {
		background-color: #f3f4f6;
		cursor: not-allowed;
	}

	.otp-actions {
		text-align: center;
		margin-top: 1rem;
	}

	.resend-button {
		background: none;
		border: none;
		color: #ffae00;
		font-weight: 600;
		cursor: pointer;
		padding: 0.5rem 1rem;
		font-size: 0.95rem;
		transition: all 0.2s;
	}

	.resend-button:hover:not(:disabled) {
		color: #e69d00;
		text-decoration: underline;
	}

	.resend-button:disabled {
		color: #9ca3af;
		cursor: not-allowed;
	}

	.otp-loading {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 0.75rem;
		margin-top: 1.5rem;
		padding: 1rem;
		background-color: #fffef8;
		border-radius: 0.5rem;
	}

	.spinner {
		width: 24px;
		height: 24px;
		border: 3px solid #f3f4f6;
		border-top-color: #ffae00;
		border-radius: 50%;
		animation: spin 0.8s linear infinite;
	}

	@keyframes spin {
		to {
			transform: rotate(360deg);
		}
	}

	.otp-loading p {
		color: #666;
		font-size: 0.9rem;
	}

	@media (max-width: 640px) {
		.otp-inputs {
			gap: 0.5rem;
		}

		.otp-input {
			width: 2.75rem;
			height: 2.75rem;
			font-size: 1.25rem;
		}
	}
</style>
