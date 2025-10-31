import type { PageLoad } from './$types';
import { getAllMenuItems } from '$lib/utils/fetcher';

export const load: PageLoad = async ({ fetch }) => {
    try {
        const items = await getAllMenuItems(fetch);
        return { items };
    } catch (error) {
        console.error('Error loading menu items:', error);
        return {
            items: [],
            error: error instanceof Error ? error.message : 'Unknown error'
        };
    }
};