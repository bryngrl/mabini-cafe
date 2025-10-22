# OTP Component Usage Examples

## Component Props

```typescript
email: string              // Email to display
loading: boolean           // Show loading state
purpose: 'signup' | 'forgot-password' | 'phone-change' | 'email-change'
```

## Events

- `on:verify` - Fired when all 6 digits are entered, returns `{ code: string }`
- `on:resend` - Fired when resend button is clicked, returns `{ email: string }`

---

## Example 1: Email Verification (Signup)

```svelte
<script lang="ts">
	import OtpInput from '$lib/components/ui/OtpInput.svelte';
	import { otpStore } from '$lib/stores';
	import { showSuccess, showError } from '$lib/utils/sweetalert';
	
	let email = 'user@example.com';
	let loading = false;
	
	async function handleVerify(event: CustomEvent<{ code: string }>) {
		loading = true;
		try {
			await otpStore.verifyOtp(email, event.detail.code);
			await showSuccess('Email verified successfully!');
			// Redirect to dashboard or login
		} catch (err: any) {
			await showError(err.message || 'Invalid code');
		} finally {
			loading = false;
		}
	}
	
	async function handleResend(event: CustomEvent<{ email: string }>) {
		try {
			await otpStore.sendOtp(event.detail.email);
			await showSuccess('Code resent! Check your email.');
		} catch (err: any) {
			await showError(err.message || 'Failed to resend code');
		}
	}
</script>

<OtpInput
	{email}
	{loading}
	purpose="signup"
	on:verify={handleVerify}
	on:resend={handleResend}
/>
```

---

## Example 2: Password Reset

```svelte
<script lang="ts">
	import OtpInput from '$lib/components/ui/OtpInput.svelte';
	import { otpStore } from '$lib/stores';
	import { showSuccess, showError } from '$lib/utils/sweetalert';
	
	let email = 'user@example.com';
	let loading = false;
	let verified = false;
	
	async function handleVerify(event: CustomEvent<{ code: string }>) {
		loading = true;
		try {
			await otpStore.verifyOtp(email, event.detail.code);
			verified = true;
			await showSuccess('Code verified! Enter your new password.');
		} catch (err: any) {
			await showError(err.message || 'Invalid code');
		} finally {
			loading = false;
		}
	}
	
	async function handleResend(event: CustomEvent<{ email: string }>) {
		try {
			await otpStore.sendOtp(event.detail.email);
			await showSuccess('Code resent to your email.');
		} catch (err: any) {
			await showError('Failed to resend code');
		}
	}
</script>

{#if !verified}
	<OtpInput
		{email}
		{loading}
		purpose="forgot-password"
		on:verify={handleVerify}
		on:resend={handleResend}
	/>
{:else}
	<!-- Show password reset form -->
	<form>
		<input type="password" placeholder="New password" />
		<button>Reset Password</button>
	</form>
{/if}
```

---

## Example 3: Phone Number Change

```svelte
<script lang="ts">
	import OtpInput from '$lib/components/ui/OtpInput.svelte';
	import { showSuccess, showError } from '$lib/utils/sweetalert';
	
	let newPhone = '+639123456789';
	let loading = false;
	
	async function handleVerify(event: CustomEvent<{ code: string }>) {
		loading = true;
		try {
			// Call your phone verification API
			const response = await fetch('/api/verify-phone', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({
					phone: newPhone,
					code: event.detail.code
				})
			});
			
			if (!response.ok) throw new Error('Verification failed');
			
			await showSuccess('Phone number updated successfully!');
			// Update user profile or redirect
		} catch (err: any) {
			await showError(err.message || 'Invalid code');
		} finally {
			loading = false;
		}
	}
	
	async function handleResend(event: CustomEvent<{ email: string }>) {
		try {
			// Call your SMS resend API
			await fetch('/api/send-phone-otp', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ phone: newPhone })
			});
			await showSuccess('Code resent via SMS.');
		} catch (err: any) {
			await showError('Failed to resend code');
		}
	}
</script>

<div class="phone-verification">
	<h2>Verify Your New Phone Number</h2>
	<p>We've sent a code to {newPhone}</p>
	
	<OtpInput
		email={newPhone}
		{loading}
		purpose="phone-change"
		on:verify={handleVerify}
		on:resend={handleResend}
	/>
</div>
```

---

## Example 4: Email Change (in Settings)

```svelte
<script lang="ts">
	import OtpInput from '$lib/components/ui/OtpInput.svelte';
	import { otpStore, authStore } from '$lib/stores';
	import { showSuccess, showError } from '$lib/utils/sweetalert';
	
	let newEmail = '';
	let showOtpInput = false;
	let loading = false;
	
	async function requestEmailChange() {
		if (!newEmail || !newEmail.includes('@')) {
			await showError('Please enter a valid email');
			return;
		}
		
		try {
			// Send OTP to new email
			await otpStore.sendOtp(newEmail);
			showOtpInput = true;
			await showSuccess('Verification code sent to your new email!');
		} catch (err: any) {
			await showError('Failed to send code');
		}
	}
	
	async function handleVerify(event: CustomEvent<{ code: string }>) {
		loading = true;
		try {
			await otpStore.verifyOtp(newEmail, event.detail.code);
			
			// Update user email
			await fetch('/api/user/update-email', {
				method: 'PUT',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ email: newEmail })
			});
			
			await showSuccess('Email updated successfully!');
			showOtpInput = false;
			newEmail = '';
		} catch (err: any) {
			await showError(err.message || 'Failed to verify code');
		} finally {
			loading = false;
		}
	}
	
	async function handleResend(event: CustomEvent<{ email: string }>) {
		try {
			await otpStore.sendOtp(event.detail.email);
			await showSuccess('Code resent!');
		} catch (err: any) {
			await showError('Failed to resend code');
		}
	}
</script>

<div class="settings-section">
	<h3>Change Email Address</h3>
	
	{#if !showOtpInput}
		<div class="email-input">
			<input
				type="email"
				bind:value={newEmail}
				placeholder="Enter new email"
			/>
			<button on:click={requestEmailChange}>
				Send Verification Code
			</button>
		</div>
	{:else}
		<OtpInput
			email={newEmail}
			{loading}
			purpose="email-change"
			on:verify={handleVerify}
			on:resend={handleResend}
		/>
		<button on:click={() => showOtpInput = false}>
			Cancel
		</button>
	{/if}
</div>
```

---

## Example 5: Two-Factor Authentication (2FA)

```svelte
<script lang="ts">
	import OtpInput from '$lib/components/ui/OtpInput.svelte';
	import { authStore } from '$lib/stores';
	import { showSuccess, showError } from '$lib/utils/sweetalert';
	import { goto } from '$app/navigation';
	
	let userEmail = $authStore.user?.email || '';
	let loading = false;
	
	async function handleVerify(event: CustomEvent<{ code: string }>) {
		loading = true;
		try {
			const response = await fetch('/api/auth/verify-2fa', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({
					code: event.detail.code,
					userId: $authStore.user?.id
				})
			});
			
			if (!response.ok) throw new Error('Invalid 2FA code');
			
			const data = await response.json();
			await authStore.setUser(data.user);
			await showSuccess('Login successful!');
			goto('/dashboard');
		} catch (err: any) {
			await showError('Invalid authentication code');
		} finally {
			loading = false;
		}
	}
	
	async function handleResend() {
		try {
			await fetch('/api/auth/resend-2fa', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ userId: $authStore.user?.id })
			});
			await showSuccess('New code sent!');
		} catch (err: any) {
			await showError('Failed to resend code');
		}
	}
</script>

<div class="two-factor-auth">
	<h2>Two-Factor Authentication</h2>
	<p>Enter the code we sent to your email for added security.</p>
	
	<OtpInput
		email={userEmail}
		{loading}
		purpose="signup"
		on:verify={handleVerify}
		on:resend={handleResend}
	/>
</div>
```

---

## Component Features

✅ **Auto-focus**: First input auto-focuses on mount
✅ **Smart navigation**: Automatically moves to next input when digit is entered
✅ **Backspace handling**: Clears current and moves to previous
✅ **Arrow keys**: Navigate between inputs with keyboard
✅ **Paste support**: Paste full 6-digit code at once
✅ **Resend cooldown**: 60-second timer prevents spam
✅ **Loading states**: Shows spinner during verification
✅ **Responsive**: Works on mobile and desktop
✅ **Accessible**: Proper ARIA labels and keyboard navigation
✅ **Type-safe**: Full TypeScript support
