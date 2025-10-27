import { writable } from 'svelte/store';
import { browser } from '$app/environment';

function createOrderNoteStore() {
	// Load from localStorage if available
	const storedNote = browser ? localStorage.getItem('order_note') || '' : '';
	const { subscribe, set, update } = writable(storedNote);

	return {
		subscribe,
		setNote: (note) => {
			set(note);
			if (browser) {
				localStorage.setItem('order_note', note);
			}
		},
		clear: () => {
			set('');
			if (browser) {
				localStorage.removeItem('order_note');
			}
		}
	};
}

export const orderNoteStore = createOrderNoteStore();
