<script lang="ts">
	import { menuStore } from '$lib/stores';
	import { showError, showSuccess } from '$lib/utils/sweetalert';

	interface Product {
		id: number;
		name: string;
		price: string | number;
		description?: string;
		category_id?: number;
		category_name?: string;
		image_path?: string;
		image_url?: string;
		isAvailable?: boolean | number;
	}

	interface Props {
		product: Product;
		onClose: () => void;
	}

	let { product, onClose }: Props = $props();

	// Form state
	let name = $state(product.name);
	let price = $state(String(product.price));
	let description = $state(product.description || '');
	let categoryId = $state(product.category_id || 1);
	let isAvailable = $state(product.isAvailable === 1 || product.isAvailable === true);
	let newImage: File | null = $state(null);
	let imagePreview = $state('');
	let isSubmitting = $state(false);

	const categories = [
		{ id: 1, name: 'Pastries' },
		{ id: 2, name: 'Meals' },
		{ id: 3, name: 'Beverages' }
	];

	function handleImageChange(e: Event) {
		const target = e.target as HTMLInputElement;
		const file = target.files?.[0];
		if (file) {
			newImage = file;
			const reader = new FileReader();
			reader.onload = (e) => {
				imagePreview = e.target?.result as string;
			};
			reader.readAsDataURL(file);
		}
	}

	async function handleSubmit(e: Event) {
		e.preventDefault();
		
		if (isSubmitting) return;
		
		try {
			isSubmitting = true;

			if (!name || !price) {
				showError('Please fill in all required fields');
				return;
			}

			const updateData = {
				name: name.trim(),
				price: parseFloat(price),
				description: description.trim(),
				category_id: categoryId,
				isAvailable: isAvailable ? 1 : 0
			};

			await menuStore.update(product.id, updateData, newImage);

			await showSuccess(
				`${name} has been updated successfully!`,
				'Product Updated'
			);

			onClose();
		} catch (err) {
			console.error('Error updating product:', err);
			await showError('Failed to update product. Please try again.');
		} finally {
			isSubmitting = false;
		}
	}

	function handleBackdropClick(e: MouseEvent) {
		if (e.target === e.currentTarget) {
			onClose();
		}
	}
</script>

<!-- Modal Backdrop -->
<div
	class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
	onclick={handleBackdropClick}
	role="dialog"
	aria-modal="true"
	aria-labelledby="modal-title"
>
	<!-- Modal Content -->
	<div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
		<!-- Header -->
		<div class="bg-black text-white p-6 rounded-t-2xl flex justify-between items-center">
			<h2 id="modal-title" class="text-2xl font-bold">Edit Product</h2>
			<button
				type="button"
				onclick={onClose}
				class="text-white hover:text-gray-300 transition-colors"
				aria-label="Close modal"
			>
				<svg
					xmlns="http://www.w3.org/2000/svg"
					class="h-6 w-6"
					fill="none"
					viewBox="0 0 24 24"
					stroke="currentColor"
				>
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M6 18L18 6M6 6l12 12"
					/>
				</svg>
			</button>
		</div>

		<!-- Form -->
		<form onsubmit={handleSubmit} class="p-6 space-y-6">
			<!-- Current Image -->
			<div class="flex flex-col items-center">
				<label class="text-sm font-semibold text-gray-700 mb-2">Current Image</label>
				<img
					src={imagePreview || `https://mabini-cafe.bscs3a.com/phpbackend/${product.image_path || product.image_url}`}
					alt={product.name}
					class="w-48 h-48 object-cover rounded-lg shadow-md"
				/>
			</div>

			<!-- Product Name -->
			<div>
				<label for="product-name" class="block text-sm font-semibold text-gray-700 mb-2">
					Product Name <span class="text-red-600">*</span>
				</label>
				<input
					id="product-name"
					type="text"
					bind:value={name}
					required
					class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-mabini-yellow focus:border-transparent"
					placeholder="e.g., Caramel Macchiato"
				/>
			</div>

			<!-- Price -->
			<div>
				<label for="product-price" class="block text-sm font-semibold text-gray-700 mb-2">
					Price (₱) <span class="text-red-600">*</span>
				</label>
				<input
					id="product-price"
					type="number"
					step="0.01"
					min="0"
					bind:value={price}
					required
					class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-mabini-yellow focus:border-transparent"
					placeholder="e.g., 150.00"
				/>
			</div>

			<!-- Category -->
			<div>
				<label for="product-category" class="block text-sm font-semibold text-gray-700 mb-2">
					Category <span class="text-red-600">*</span>
				</label>
				<select
					id="product-category"
					bind:value={categoryId}
					required
					class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-mabini-yellow focus:border-transparent"
				>
					{#each categories as category}
						<option value={category.id}>{category.name}</option>
					{/each}
				</select>
			</div>

			<!-- Subcategory / Description -->
			<div>
				<label for="product-description" class="block text-sm font-semibold text-gray-700 mb-2">
					Subcategory / Description
				</label>
				<input
					id="product-description"
					type="text"
					bind:value={description}
					class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-mabini-yellow focus:border-transparent"
					placeholder="e.g., Hot, Iced, Savory Waffle"
				/>
				<p class="text-xs text-gray-500 mt-1">
					Optional: Adds a filter option in the menu sidebar
				</p>
			</div>

			<!-- Availability Toggle -->
			<div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
				<div>
					<label for="product-availability" class="text-sm font-semibold text-gray-700">
						Product Availability
					</label>
					<p class="text-xs text-gray-500">
						Toggle to make this product available or unavailable for customers
					</p>
				</div>
				<label class="flex items-center gap-3 cursor-pointer">
					<input
						id="product-availability"
						type="checkbox"
						bind:checked={isAvailable}
						class="w-6 h-6 cursor-pointer accent-mabini-yellow"
					/>
					<span class="text-sm font-medium {isAvailable ? 'text-green-600' : 'text-red-600'}">
						{isAvailable ? 'Available' : 'Unavailable'}
					</span>
				</label>
			</div>

			<!-- Upload New Image -->
			<div>
				<label for="product-image" class="block text-sm font-semibold text-gray-700 mb-2">
					Upload New Image (Optional)
				</label>
				<input
					id="product-image"
					type="file"
					accept="image/*"
					onchange={handleImageChange}
					class="w-full border border-gray-300 rounded-lg px-4 py-2 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-mabini-yellow file:text-white hover:file:bg-yellow-600 cursor-pointer"
				/>
				<p class="text-xs text-gray-500 mt-1">
					Leave empty to keep the current image
				</p>
			</div>

			<!-- Action Buttons -->
			<div class="flex gap-4 pt-4">
				<button
					type="button"
					onclick={onClose}
					class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
					disabled={isSubmitting}
				>
					Cancel
				</button>
				<button
					type="submit"
					class="flex-1 px-6 py-3 bg-mabini-yellow text-white rounded-lg font-semibold hover:bg-yellow-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
					disabled={isSubmitting}
				>
					{isSubmitting ? 'Saving...' : 'Save Changes'}
				</button>
			</div>
		</form>
	</div>
</div>

<style>
	/* Custom scrollbar for modal */
	div::-webkit-scrollbar {
		width: 8px;
	}

	div::-webkit-scrollbar-track {
		background: #f1f1f1;
		border-radius: 10px;
	}

	div::-webkit-scrollbar-thumb {
		background: #888;
		border-radius: 10px;
	}

	div::-webkit-scrollbar-thumb:hover {
		background: #555;
	}
</style>
