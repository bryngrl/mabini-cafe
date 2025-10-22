# Multiple Shipping Addresses Update

## Changes Made

### 1. Backend (PHP)
**File: `phpbackend/Models/ShipInfo.php`**
- Changed `getByUserId()` to return **multiple addresses** using `fetchAll()` instead of `fetch()`
- This allows users to have multiple shipping addresses

### 2. Frontend Fetcher
**File: `src/lib/utils/fetcher.js`**
- Fixed `addNewShippingInfo()` function syntax (was returning wrong value)
- Now properly calls `apiFetch()` with POST method to add new addresses

### 3. Shipping Store
**File: `src/lib/stores/shipping.js`**
- Changed from storing single `shippingInfo` to array `shippingAddresses`
- Added new methods:
  - `addAddress(userId, shippingData)` - Add a new address for a user
  - `updateAddress(userId, shippingData)` - Update an existing address
  - `deleteAddress(shipInfoId, userId)` - Delete an address
- Updated existing methods to work with arrays

### 4. Exported Stores
Changed from:
```javascript
export const shippingInfo = derived(shippingStore, $shipping => $shipping.shippingInfo);
```

To:
```javascript
export const shippingAddresses = derived(shippingStore, $shipping => $shipping.shippingAddresses);
```

## How to Use

### Fetch all addresses for a user:
```javascript
import { shippingStore, shippingAddresses } from '$lib/stores/shipping';

// Fetch addresses
await shippingStore.fetchByUserId(userId);

// Access in component
$: addresses = $shippingAddresses; // This is now an array
```

### Add a new address:
```javascript
const newAddress = {
    email: 'user@example.com',
    first_name: 'John',
    last_name: 'Doe',
    address: '123 Main St',
    apartment_suite_etc: 'Apt 4B',
    postal_code: '12345',
    city: 'Manila',
    region: 'NCR',
    phone: '09123456789'
};

await shippingStore.addAddress(userId, newAddress);
```

### Update an address:
```javascript
await shippingStore.updateAddress(userId, updatedAddressData);
```

### Delete an address:
```javascript
await shippingStore.deleteAddress(addressId, userId);
```

### Display multiple addresses in UI:
```svelte
<script>
    import { shippingStore, shippingAddresses } from '$lib/stores/shipping';
    import { currentUser } from '$lib/stores';
    
    $: if ($currentUser?.id) {
        shippingStore.fetchByUserId($currentUser.id);
    }
</script>

{#if $shippingAddresses.length > 0}
    <h3>Your Addresses</h3>
    {#each $shippingAddresses as address}
        <div class="address-card">
            <p>{address.first_name} {address.last_name}</p>
            <p>{address.address}</p>
            <p>{address.city}, {address.region} {address.postal_code}</p>
            <p>{address.phone}</p>
            <button on:click={() => shippingStore.deleteAddress(address.id, $currentUser.id)}>
                Delete
            </button>
        </div>
    {/each}
{:else}
    <p>No addresses found.</p>
{/if}

<button on:click={() => showAddAddressForm = true}>Add New Address</button>
```

## Important Notes

1. **Breaking Change**: If you were using `$shippingInfo` before, you need to update to `$shippingAddresses[0]` or loop through the array.

2. **Backend supports multiple addresses**: Users can now add multiple shipping addresses and choose which one to use during checkout.

3. **Update your components**: Any component using the old `shippingInfo` export needs to be updated to use `shippingAddresses` array.

## Example Migration

**Before:**
```svelte
<script>
    import { shippingInfo } from '$lib/stores/shipping';
</script>

{#if $shippingInfo}
    <p>{$shippingInfo.address}</p>
{/if}
```

**After:**
```svelte
<script>
    import { shippingAddresses } from '$lib/stores/shipping';
</script>

{#each $shippingAddresses as address}
    <p>{address.address}</p>
{/each}
```
