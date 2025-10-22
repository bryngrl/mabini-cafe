# Account Settings - Multiple Addresses Update

## Summary of Changes

Your account settings page has been updated to support multiple shipping addresses with full CRUD (Create, Read, Update, Delete) functionality.

## Key Features Implemented

### 1. **Multiple Address Display**
- Dynamically displays all saved addresses for a user
- First address is labeled as "Default Address"
- Additional addresses labeled as "Address 2", "Address 3", etc.

### 2. **Add New Address**
- "Add New Address" button appears when not editing
- Opens a form to add a new shipping address
- Validates all required fields before submission
- Refreshes address list after successful addition

### 3. **Edit Address**
- Each address has an individual "EDIT" button
- Loads the selected address data into the form
- Updates only the specific address being edited
- Refreshes list after successful update

### 4. **Delete Address**
- Each address has an individual "DELETE" button
- Shows confirmation dialog before deletion
- Removes the address from database and UI
- Automatically refreshes the address list

### 5. **Reactive Updates**
- Uses Svelte stores for real-time updates
- `$shippingAddresses` automatically updates when addresses change
- `hasExistingShipping` dynamically checks if user has any addresses

## Technical Implementation

### Store Integration
```javascript
import { shippingStore, shippingAddresses } from '$lib/stores';

// Reactive subscription to addresses
$: userAddresses = $shippingAddresses || [];
$: hasExistingShipping = userAddresses.length > 0;

// Fetch addresses when user is available
$: if ($currentUser?.id) {
    shippingStore.fetchByUserId($currentUser.id);
}
```

### Functions Added/Updated

1. **`editAddress(address)`** - Load address data into form for editing
2. **`showAddNewAddressForm()`** - Show empty form for new address
3. **`deleteAddress(addressId)`** - Delete specific address with confirmation
4. **`handleAddNewAddress()`** - Add new address via `addAddress()` method
5. **`handleUpdateAddress()`** - Update existing address via `updateAddress()` method
6. **`cancelEdit()`** - Reset form and close edit/add mode

### State Management

```javascript
let addNewAddress = false;      // True when adding new address
let isEditAddress = false;      // True when editing existing address
let editingAddressId = null;    // ID of address being edited
```

## User Workflow

### Adding First Address
1. User sees "You have no saved addresses"
2. Form is displayed automatically
3. User fills out form and clicks "Save Address"
4. Address is saved and displayed

### Adding Additional Addresses
1. User clicks "Add New Address" button
2. Empty form appears
3. User fills out form and clicks "Add Address"
4. New address is added to the list

### Editing an Address
1. User clicks "EDIT" button on specific address
2. Form pre-fills with address data
3. User modifies fields
4. Clicks "Update Address" to save changes
5. Address list refreshes with updated data

### Deleting an Address
1. User clicks "DELETE" button on specific address
2. Confirmation dialog appears
3. User confirms deletion
4. Address is removed from list

## UI Enhancements

- Each address card shows:
  - Full name
  - Complete address with apartment/suite
  - City and region
  - Individual Edit and Delete buttons

- Form dynamically changes:
  - Button text: "Add Address" vs "Update Address"
  - Form title: "ADD NEW ADDRESS" vs "EDIT ADDRESS"

## Backend Integration

Uses the updated shipping store methods:
- `shippingStore.fetchByUserId(userId)` - Get all addresses
- `shippingStore.addAddress(userId, data)` - Add new address
- `shippingStore.updateAddress(userId, data)` - Update address
- `shippingStore.deleteAddress(addressId, userId)` - Delete address

## Testing Checklist

- [ ] Add first address when none exist
- [ ] Add second/third address
- [ ] Edit each address individually
- [ ] Delete non-default addresses
- [ ] Delete default address
- [ ] Cancel during add/edit
- [ ] Form validation works
- [ ] Address list updates in real-time
- [ ] Account Overview shows first address info

## Notes

- The backend `getByUserId()` was updated to return `fetchAll()` instead of `fetch()`
- Maximum number of addresses: Currently unlimited (can add logic to limit)
- Default address: Always the first address in the list
- All forms include proper validation for required fields
