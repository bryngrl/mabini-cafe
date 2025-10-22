/**
 * Centralized API Fetcher for Mabini Cafe
 * All backend API calls are organized here by resource type
 */

const API_BASE_URL = 'http://localhost/mabini-cafe/phpbackend/routes';
async function apiFetch(endpoint, options = {}) {
	try {
		const response = await fetch(`${API_BASE_URL}${endpoint}`, {
			...options,
			credentials: 'include', // cookies
			headers: {
				...options.headers
			}
		});

		if (!response.ok) {
			let errorMessage = `HTTP error! status: ${response.status}`;
			try {
				const data = await response.json();
				errorMessage = data.error || errorMessage;
			} catch (e) {
				errorMessage = response.statusText || errorMessage;
			}
			throw new Error(errorMessage);
		}

		const data = await response.json();
		return data;
	} catch (error) {
		console.error(`API Error [${endpoint}]:`, error);
		if (error.message === 'Failed to fetch') {
			throw new Error(
				`Cannot connect to backend at ${API_BASE_URL}. Make sure XAMPP/Apache is running.`
			);
		}
		throw error;
	}
}

async function apiFormData(endpoint, formData, method = 'POST') {
	try {
		const response = await fetch(`${API_BASE_URL}${endpoint}`, {
			method,
			body: formData,
			credentials: 'include'
		});

		const data = await response.json();

		if (!response.ok) {
			throw new Error(data.error || `HTTP error! status: ${response.status}`);
		}

		return data;
	} catch (error) {
		console.error(`API Error [${endpoint}]:`, error);
		throw error;
	}
}

// ===============
// USER
// ===============

/**
 * Register a new user
 * @param {object} userData - User information object
 * @param {string} userData.username - Username
 * @param {string} userData.email - Email address
 * @param {string} userData.password - Password
 * @param {string} userData.address - Address
 * @param {string} userData.contact_number - Contact number
 */
export async function signup(userData) {
	return apiFetch('/users', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			username: userData.username || userData.name,
			email: userData.email,
			password: userData.password,
			address: userData.address || '',
			contact_number: userData.contact_number || ''
		})
	});
}

/**
 * User login
 * @param {string} email - User's email
 * @param {string} password - User's password
 */
export async function loginUser(email, password) {
	return apiFetch('/users/login', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ email, password })
	});
}

/**
 * Get all users (admin only)
 *
 */
export async function getAllUsers() {
	return apiFetch('/users');
}

/**
 * Get user by ID
 * @param {string} userId - User ID
 */
export async function getUserById(userId) {
	return apiFetch(`/users/${userId}`);
}

/**
 * Update user profile
 * @param {string} userId - User ID
 * @param {object} userData - Updated user information
 */
export async function updateUser(userId, userData) {
	return apiFetch(`/users/${userId}`, {
		method: 'PUT',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(userData)
	});
}

/**
 * Delete user account
 * @param {string} userId - User ID
 */
export async function deleteUser(userId) {
	return apiFetch(`/users/${userId}`, {
		method: 'DELETE'
	});
}

// ==============
// ADMIN
// ==========

/**
 * Admin login
 */
export async function loginAdmin(email, password) {
	return apiFetch('/admins/login', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ email, password })
	});
}

/**
 * Admin logout
 */
export async function logoutAdmin() {
	return apiFetch('/admins/logout', {
		method: 'POST'
	});
}

/**
 * Get all admins
 */
export async function getAllAdmins() {
	return apiFetch('/admins');
}

/**
 * Get admin by ID
 */
export async function getAdminById(adminId) {
	return apiFetch(`/admins/${adminId}`);
}

/**
 * Create new admin
 */
export async function createAdmin(adminData) {
	return apiFetch('/admins', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(adminData)
	});
}

/**
 * Update admin
 */
export async function updateAdmin(adminId, adminData) {
	return apiFetch(`/admins/${adminId}`, {
		method: 'PUT',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(adminData)
	});
}

/**
 * Delete admin
 */
export async function deleteAdmin(adminId) {
	return apiFetch(`/admins/${adminId}`, {
		method: 'DELETE'
	});
}

// =============
// MENU ITEMS
// =============

/**
 * Get all menu items
 */
export async function getAllMenuItems() {
	return apiFetch('/menu');
}

/**
 * Get menu item by ID
 */
export async function getMenuItemById(itemId) {
	return apiFetch(`/menu/${itemId}`);
}

/**
 * Get menu items by category
 */
export async function getMenuItemsByCategory(categoryId) {
	return apiFetch(`/menu?category_id=${categoryId}`);
}

/**
 * Get menu items by desc/subcategory
 */
export async function getMenuItemsByDescription(description) {
	return apiFetch(`/menu?description=${encodeURIComponent(description)}`);
}

/**
 * Create new menu item (with optional image upload)
 */
export async function createMenuItem(itemData, imageFile = null) {
	const formData = new FormData();
	formData.append('name', itemData.name);
	formData.append('price', itemData.price);
	if (itemData.description) formData.append('description', itemData.description);
	if (itemData.category_id) formData.append('category_id', itemData.category_id);
	if (imageFile) formData.append('image', imageFile);

	return apiFormData('/menu', formData, 'POST');
}

/**
 * Update menu item (with optional image upload)
 */
export async function updateMenuItem(itemId, itemData, imageFile = null) {
	const formData = new FormData();
	formData.append('name', itemData.name);
	formData.append('price', itemData.price);
	if (itemData.description) formData.append('description', itemData.description);
	if (itemData.category_id) formData.append('category_id', itemData.category_id);
	if (imageFile) formData.append('image', imageFile);

	return apiFormData(`/menu/${itemId}`, formData, 'PUT');
}

/**
 * Delete menu item
 */
export async function deleteMenuItem(itemId) {
	return apiFetch(`/menu/${itemId}`, {
		method: 'DELETE'
	});
}

// ===============
// SHOPPING CART
// ===============

/**
 * Get all carts (admin)
 */
export async function getAllCarts() {
	return apiFetch('/cart');
}

/**
 * Get cart by ID
 */
export async function getCartById(cartId) {
	return apiFetch(`/cart/${cartId}`);
}

/**
 * Get cart items by customer/user ID
 */
export async function getCartByCustomerId(customerId) {
	return apiFetch(`/cart?customer_id=${customerId}`);
}

/**
 * Add item to cart
 */
export async function addToCart(cartItem) {
	return apiFetch('/cart', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			user_id: cartItem.user_id,
			menu_item_id: cartItem.menu_item_id,
			quantity: cartItem.quantity,
			subtotal: cartItem.subtotal
		})
	});
}

/**
 * Update cart item
 */
export async function updateCartItem(cartId, quantity, subtotal) {
	return apiFetch(`/cart/${cartId}`, {
		method: 'PUT',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ quantity, subtotal })
	});
}

/**
 * Remove item from cart
 */
export async function removeFromCart(cartId) {
	return apiFetch(`/cart/${cartId}`, {
		method: 'DELETE'
	});
}

// ===============
// ORDERS
// ==============

/**
 * Get all orders
 */
export async function getAllOrders() {
	return apiFetch('/orders');
}

/**
 * Get order by ID
 */
export async function getOrderById(orderId) {
	return apiFetch(`/orders/${orderId}`);
}

/**
 * Get orders by customer ID
 */
export async function getOrdersByCustomerId(customerId) {
	return apiFetch(`/orders?customerId=${customerId}`);
}

/**
 * Create new order
 */
export async function createOrder(orderData) {
	return apiFetch('/orders', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			user_id: orderData.user_id,
			total_amount: orderData.total_amount
		})
	});
}

/**
 * Update order status to Preparing
 */
export async function updateOrderToPreparing(orderId) {
	return apiFetch(`/orders/preparing/${orderId}`, {
		method: 'PUT'
	});
}

/**
 * Update order status to Delivering
 */
export async function updateOrderToDelivering(orderId) {
	return apiFetch(`/orders/delivering/${orderId}`, {
		method: 'PUT'
	});
}

/**
 * Update order status to Completed
 */
export async function updateOrderToCompleted(orderId) {
	return apiFetch(`/orders/completed/${orderId}`, {
		method: 'PUT'
	});
}

/**
 * Update order status to Cancelled
 */
export async function updateOrderToCancelled(orderId) {
	return apiFetch(`/orders/cancelled/${orderId}`, {
		method: 'PUT'
	});
}

/**
 * Update payment status to Paid
 */
export async function updatePaymentStatus(orderId) {
	return apiFetch(`/orders/updatepayment/${orderId}`, {
		method: 'PUT'
	});
}

/**
 * Set payment method to Cash
 */
export async function setPaymentToCash(orderId) {
	return apiFetch(`/orders/setToCash/${orderId}`, {
		method: 'PUT'
	});
}

/**
 * Set payment method to Online
 */
export async function setPaymentToOnline(orderId) {
	return apiFetch(`/orders/setToOnline/${orderId}`, {
		method: 'PUT'
	});
}

/**
 * Get total number of orders
 */
export async function getTotalOrders() {
	return apiFetch('/orders/totalOrders');
}

/**
 * Get total delivered orders
 */
export async function getTotalDelivered() {
	return apiFetch('/orders/totalDelivered');
}

/**
 * Get total cancelled orders
 */
export async function getTotalCancelled() {
	return apiFetch('/orders/totalCancelled');
}

// ==================
// ORDER ITEMS
// ==================

/**
 * Get all order items
 */
export async function getAllOrderItems() {
	return apiFetch('/orderitems');
}

/**
 * Get order item by ID
 */
export async function getOrderItemById(itemId) {
	return apiFetch(`/orderitems/${itemId}`);
}

/**
 * Get order items by order ID
 */
export async function getOrderItemsByOrderId(orderId) {
	return apiFetch(`/orderitems?orderId=${orderId}`);
}

/**
 * Create new order item
 */
export async function createOrderItem(orderItemData) {
	return apiFetch('/orderitems', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			order_id: orderItemData.order_id,
			menu_item_id: orderItemData.menu_item_id,
			quantity: orderItemData.quantity,
			subtotal: orderItemData.subtotal
		})
	});
}

// ==================
// CONTACT FORM
// ==================

/**
 * Get all contact messages
 */
export async function getAllContacts() {
	return apiFetch('/contacts');
}

/**
 * Submit contact form (with optional image)
 */
export async function submitContactForm(contactData, imageFile = null) {
	const formData = new FormData();

	if (contactData.user_id) formData.append('user_id', contactData.user_id);
	formData.append('name', contactData.name);
	formData.append('email', contactData.email);
	formData.append('contact_reason', contactData.contact_reason);
	formData.append('topic', contactData.topic);
	if (contactData.message) formData.append('message', contactData.message);
	if (contactData.order_id) formData.append('order_id', contactData.order_id);
	if (imageFile) formData.append('image', imageFile);

	return apiFormData('/contacts', formData, 'POST');
}

/**
 * Validate token
 */
export async function validateToken() {
	try {
		const response = await apiFetch('/auth/validate-token');
		return response;
	} catch (error) {
		return null;
	}
}
// ==============
// SHIPPING INFO
// ==============

/**
 * Get all shipping information (admin)
 */
export async function getAllShippingInfo() {
	return apiFetch('/shipinfo');
}

/**
 * Get shipping information by ID
 * @param {number} shipInfoId
 */
export async function getShippingInfoById(shipInfoId) {
	return apiFetch(`/shipinfo/${shipInfoId}`);
}

/**
 * Get shipping information by user ID
 * @param {number} userId
 */
export async function getShippingInfoByUserId(userId) {
	return apiFetch(`/shipinfo?user_id=${userId}`);
}

/**
 * Create/Store new shipping information
 * @param {object} shippingData - Shipping information object
 * @param {number} shippingData.user_id - User ID (optional)
 * @param {string} shippingData.email - Email address
 * @param {string} shippingData.first_name - First name
 * @param {string} shippingData.last_name - Last name
 * @param {string} shippingData.address - Street address
 * @param {string} shippingData.apartment_suite_etc - Apartment/Suite (optional)
 * @param {string} shippingData.postal_code - Postal/ZIP code
 * @param {string} shippingData.city - City
 * @param {string} shippingData.region - Region/State
 * @param {string} shippingData.phone - Phone number
 */
export async function storeShippingInfo(shippingData) {
	return apiFetch('/shipinfo', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			user_id: shippingData.user_id || null,
			email: shippingData.email,
			first_name: shippingData.first_name,
			last_name: shippingData.last_name,
			address: shippingData.address,
			apartment_suite_etc: shippingData.apartment_suite_etc || '',
			postal_code: shippingData.postal_code,
			city: shippingData.city,
			region: shippingData.region,
			phone: shippingData.phone
		})
	});
}

/**
 * Update shipping information by user ID
 * @param {number} userId - User ID
 * @param {object} shippingData - Updated shipping information
 */
export async function updateShippingInfo(userId, shippingData) {
	return apiFetch(`/shipinfo?user_id=${userId}`, {
		method: 'PUT',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			user_id: userId,
			email: shippingData.email,
			first_name: shippingData.first_name,
			last_name: shippingData.last_name,
			address: shippingData.address,
			apartment_suite_etc: shippingData.apartment_suite_etc || '',
			postal_code: shippingData.postal_code,
			city: shippingData.city,
			region: shippingData.region,
			phone: shippingData.phone
		})
	});
}

/**
 * Delete shipping information by ID
 * @param {number} shipInfoId
 */
export async function deleteShippingInfo(shipInfoId) {
	return apiFetch(`/shipinfo/${shipInfoId}`, {
		method: 'DELETE'
	});
}
/**
 * Add a new Shipping Info
 * @param {number} userId - current user ID
 * @param {object} shippingData - new Shipping Data
 * @param {string} shippingData.email - Email address
 * @param {string} shippingData.first_name - First name
 * @param {string} shippingData.last_name - Last name
 * @param {string} shippingData.address - Street address
 * @param {string} shippingData.apartment_suite_etc - Apartment/Suite (optional)
 * @param {string} shippingData.postal_code - Postal/ZIP code
 * @param {string} shippingData.city - City
 * @param {string} shippingData.region - Region/State
 * @param {string} shippingData.phone - Phone number
 */
export async function addNewShippingInfo(userId, shippingData) {
	return apiFetch('/shipinfo', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			user_id: userId,
			email: shippingData.email,
			first_name: shippingData.first_name,
			last_name: shippingData.last_name,
			address: shippingData.address,
			apartment_suite_etc: shippingData.apartment_suite_etc || '',
			postal_code: shippingData.postal_code,
			city: shippingData.city,
			region: shippingData.region,
			phone: shippingData.phone
		})
	});
}

// ======
// OTP
// ======

/** Send OTP to email
 * @param {string} email - Email address to send OTP to
 */
export async function sendOtp(email) {
	return apiFetch('/users/sendotp', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ email })
	});
}
/**
 * Verify OTP for password reset
 * @param {string} email - The user's email
 * @param {string} otp - The OTP to verify
 */
export async function verifyOtp(email, otp) {
	return apiFetch('/users/verifyotp', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ email, otp })
	});
}
/**
 * Reset password after OTP verification
 * @param {string} email - The user's email
 * @param {string} newPassword - The new password to set
 */
export async function resetPassword(email, newPassword) {
	return apiFetch('/users/changepassword', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ email, newPassword })
	});
}
