// src/routes/menu/+page.server.ts

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
            error: error instanceof Error ? error.message : 'Unknown error during menu data load'
        };
    }
};

// **GUARDRAIL:** Ensure you are NOT exporting any other function (like getAllMenuItems) 
// from this file, as that was the cause of your previous build error.