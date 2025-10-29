// $lib/utils/fetcher.ts (The file SvelteKit's server is running)

const API_BASE_URL = 'https://mabini-cafe.bscs3a.com/phpbackend/routes';

export async function getAllMenuItems() {
    // **CRITICAL: Is this 'endpoint' correct for your PHP backend?**
    const endpoint = '/menu'; // <--- Check this line carefully!
    
    const url = `${API_BASE_URL}${endpoint}`; // Should be: https://mabini-cafe.bscs3a.com/phpbackend/api/menu-items

    const response = await fetch(url);
    // ... rest of the code
}