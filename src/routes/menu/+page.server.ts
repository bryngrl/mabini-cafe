import type { PageServerLoad } from './$types';
import { getAllMenuItems } from '$lib/utils/fetcher';
export const load: PageServerLoad = async ({ fetch }) => {
	try {
		const items = await getAllMenuItems();
		return { items };
	} catch (error) {
		console.error('Error loading menu items on server:', error);
		return {
			items: [],
			error: error instanceof Error ? error.message : 'Unknown error'
		};
	}
};
