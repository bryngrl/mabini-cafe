import { writable, derived } from 'svelte/store';
import { 
	sendOtpForgotPassword,
	sendOtpSignup,
	verifyOtp,
	resetPassword
} from '$lib/utils/fetcher';

function createOtpStore() {
	const { subscribe, set, update } = writable({
		otpSent: false,
		otpVerified: false,
		email: null,
		token: null, 
		loading: false,
		error: null,
		step: 'email' 
	});

	return {
		subscribe,

		/**
		 * Send OTP to email for password reset
		 * @param {string} email - Email address to send OTP to
		 */
		sendOtpForgotPassword: async (email) => {
			update(state => ({ 
				...state, 
				loading: true, 
				error: null,
				email: email 
			}));
			
			try {
				const result = await sendOtpForgotPassword(email);
				update(state => ({ 
					...state, 
					otpSent: true,
					token: result.token, // Store the token
					loading: false,
					step: 'otp'
				}));
				return result;
			} catch (error) {
				update(state => ({ 
					...state, 
					loading: false, 
					error: error instanceof Error ? error.message : 'Failed to send OTP'
				}));
				throw error;
			}
		},

		/**
		 * Send OTP to email for signup verification
		 * @param {string} email - Email address to send OTP to
		 */
		sendOtpSignup: async (email) => {
			update(state => ({ 
				...state, 
				loading: true, 
				error: null,
				email: email 
			}));
			
			try {
				const result = await sendOtpSignup(email);
				update(state => ({ 
					...state, 
					otpSent: true,
					token: result.token, 
					loading: false,
					step: 'otp'
				}));
				return result;
			} catch (error) {
				update(state => ({ 
					...state, 
					loading: false, 
					error: error instanceof Error ? error.message : 'Failed to send OTP'
				}));
				throw error;
			}
		},

		/**
		 * Verify OTP code
		 * @param {string} token - JWT token from sendOtp
		 * @param {string} otp - OTP code to verify
		 */
		verifyOtp: async (token, otp) => {
			update(state => ({ ...state, loading: true, error: null }));
			
			try {
				const result = await verifyOtp(token, otp);
				update(state => ({ 
					...state, 
					otpVerified: true,
					loading: false,
					step: 'password'
				}));
				return result;
			} catch (error) {
				update(state => ({ 
					...state, 
					loading: false, 
					error: error instanceof Error ? error.message : 'Invalid OTP code'
				}));
				throw error;
			}
		},

		/**
		 * Reset password after OTP verification
		 * @param {string} email - Email address
		 * @param {string} newPassword - New password to set
		 */
		resetPassword: async (email, newPassword) => {
			update(state => ({ ...state, loading: true, error: null }));
			
			try {
				const result = await resetPassword(email, newPassword);
				update(state => ({ 
					...state, 
					loading: false,
					step: 'success'
				}));
				return result;
			} catch (error) {
				update(state => ({ 
					...state, 
					loading: false, 
					error: error instanceof Error ? error.message : 'Failed to reset password'
				}));
				throw error;
			}
		},

		/**
		 * Reset the entire OTP flow
		 */
		reset: () => {
			set({
				otpSent: false,
				otpVerified: false,
				email: null,
				token: null,
				loading: false,
				error: null,
				step: 'email'
			});
		},

		/**
		 * Clear error message
		 */
		clearError: () => {
			update(state => ({ ...state, error: null }));
		},

		/**
		 * Go back to previous step
		 */
		goBack: () => {
			update(state => {
				let newStep = 'email';
				let updates = { step: newStep };

				if (state.step === 'otp') {
					newStep = 'email';
					updates = { 
						...updates, 
						otpSent: false 
					};
				} else if (state.step === 'password') {
					newStep = 'otp';
					updates = { 
						...updates, 
						otpVerified: false 
					};
				}

				return { ...state, ...updates };
			});
		}
	};
}

export const otpStore = createOtpStore();

export const otpLoading = derived(otpStore, $otp => $otp.loading);
export const otpError = derived(otpStore, $otp => $otp.error);
export const otpStep = derived(otpStore, $otp => $otp.step);
export const otpSent = derived(otpStore, $otp => $otp.otpSent);
export const otpVerified = derived(otpStore, $otp => $otp.otpVerified);
export const otpEmail = derived(otpStore, $otp => $otp.email);
export const otpToken = derived(otpStore, $otp => $otp.token);
