import { writable, derived } from 'svelte/store';
import { 
	getAllContacts,
	submitContactForm
} from '$lib/utils/fetcher';

function createContactsStore() {
	const { subscribe, set, update } = writable({
		contacts: [],
		loading: false,
		error: null,
		success: false
	});

	return {
		subscribe,

		fetchAll: async () => {
			update(state => ({ ...state, loading: true, error: null }));
			try {
				const contacts = await getAllContacts();
				update(state => ({ ...state, contacts, loading: false }));
				return contacts;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message }));
				throw error;
			}
		},

		submit: async (contactData, imageFile) => {
			update(state => ({ ...state, loading: true, error: null, success: false }));
			try {
				const result = await submitContactForm(contactData, imageFile);
				update(state => ({ ...state, loading: false, success: true }));
				return result;
			} catch (error) {
				update(state => ({ ...state, loading: false, error: error.message, success: false }));
				throw error;
			}
		},

		clearSuccess: () => {
			update(state => ({ ...state, success: false }));
		},

		clearError: () => {
			update(state => ({ ...state, error: null }));
		}
	};
}

export const contactsStore = createContactsStore();

export const contacts = derived(contactsStore, $contacts => $contacts.contacts);
export const contactsLoading = derived(contactsStore, $contacts => $contacts.loading);
export const contactSuccess = derived(contactsStore, $contacts => $contacts.success);
