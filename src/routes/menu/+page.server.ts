import type { PageServerLoad } from './$types';
// //import { getAllMenuItems } from '$lib/utils/fetcher';
// // $lib/utils/fetcher.ts

// Define the full production URL for server-side fetching
const API_BASE_URL = 'https://mabini-cafe.bscs3a.com/phpbackend';

// Assuming your menu items endpoint is something like /phpbackend/api/menu-items
export async function getAllMenuItems() {
    // You'll need to confirm the exact route part after /phpbackend
    const endpoint = '/api/menu-items'; // <--- ADJUST THIS IF YOUR ROUTE IS DIFFERENT
    const url = `${API_BASE_URL}${endpoint}`;

    try {
        // SvelteKit automatically passes the 'fetch' function, 
        // but since we are hardcoding the URL here, we don't strictly need the fetch argument 
        // if this utility is only used on the server, but let's assume standard 'fetch' is available.
        const response = await fetch(url);

        if (!response.ok) {
            // Throwing an error here ensures your +page.server.ts 'catch' block gets executed.
            throw new Error(`Failed to fetch menu items: ${response.status} ${response.statusText} from ${url}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error fetching data from PHP backend:', error);
        // Re-throw the error to be caught in the +page.server.ts
        throw error; 
    }
}