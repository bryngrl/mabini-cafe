<script lang="ts">
	import { createEventDispatcher } from 'svelte';
	import { cartStore } from '$lib/stores/cart';
	import { authStore } from '$lib/stores/auth';
	import { goto } from '$app/navigation';
	import { showError, showLoginRequired, showSuccess } from '$lib/utils/sweetalert';
	export let selectedItem;
	export let modalOpen = false;
	export let addToCartProp;

	authStore.init();

	async function buyNow(item) {
		try {
			const quantity = 1;
			const subtotal = parseFloat(item.price) * quantity;

			await cartStore.add({
				user_id: $authStore.user?.id,
				menu_item_id: item.id,
				quantity: quantity,
				subtotal: subtotal
			});

			await showSuccess(`${item.name} will be checked out!`, 'Checkout');
		} catch (err: any) {
			if (err.type === 'LOGIN_REQUIRED') {
				const result = await showLoginRequired();
				if (result.isConfirmed) {
					goto('/login');
				} else {
					goto('/');
				}
				return;
			}

			await showError(err.message || 'Failed to add item to cart', 'Error');
			console.error('Error adding to cart:', err);
		}
	}

	async function handleAddToCart(item) {
		try {
			const quantity = 1;
			const subtotal = parseFloat(item.price) * quantity;

			await cartStore.add({
				user_id: $authStore.user?.id,
				menu_item_id: item.id,
				quantity: quantity,
				subtotal: subtotal
			});

			await showSuccess(`${item.name} has been added to your cart!`, 'Added to Cart');
		} catch (err: any) {
			if (err.type === 'LOGIN_REQUIRED') {
				const result = await showLoginRequired();
				if (result.isConfirmed) {
					goto('/login');
				} else {
					goto('/');
				}
				return;
			}

			await showError(err.message || 'Failed to add item to cart', 'Error');
			console.error('Error adding to cart:', err);
		}
	}
	async function handleCheckout() {
		try {
			if (!$authStore.isAuthenticated) {
				goto('/login');
				return;
			}
			await buyNow(selectedItem);
			goto('/checkout');
		} catch (err: any) {
			await showError(err.message || 'Failed to proceed to checkout', 'Error');
			console.error('Error during checkout:', err);
		}
	}

	const dispatch = createEventDispatcher();
	const close = () => {
		dispatch('close');
	};
	let quantity = 1;
	function decrease() {
		if (quantity > 1) quantity--;
	}
	function increase() {
		quantity++;
	}
</script>

{#if modalOpen && selectedItem}
	<div
		class="fixed inset-0 z-50 flex items-center justify-center bg-[rgba(255,255,255,0.2)] backdrop-blur-md"
	>
		<div class="bg-white rounded-xl shadow-2xl w-full max-w-[95vw] sm:max-w-[600px] md:max-w-[700px] min-h-[400px] sm:min-h-[500px] p-4 sm:p-6 relative">
			<button
				class="absolute top-2 right-2 sm:top-3 sm:right-3 w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-full bg-transparent text-mabini-black cursor-pointer transition"
				on:click={close}
				aria-label="Close modal"
			>
				<svg
					width="24"
					height="20"
					viewBox="0 0 34 29"
					fill="none"
					xmlns="http://www.w3.org/2000/svg"
					class="sm:w-[34px] sm:h-[29px]"
				>
					<path
						d="M29.0118 0.497009C30.2196 0.496942 31.3818 0.958407 32.2605 1.78699C33.1393 2.61557 33.6682 3.74863 33.739 4.95433L33.7469 5.23213V24.1726C33.747 25.3804 33.2855 26.5426 32.4569 27.4213C31.6284 28.3001 30.4953 28.829 29.2896 28.8998L29.0118 28.9077H11.8202C11.0687 28.9075 10.3281 28.7285 9.6595 28.3855C8.99092 28.0424 8.4136 27.5451 7.97525 26.9348L7.80478 26.6822L1.36502 16.3754C1.07629 15.9137 0.911911 15.3852 0.887849 14.8412C0.863788 14.2972 0.980864 13.7562 1.2277 13.2708L1.36344 13.0293L7.80478 2.72252C8.20305 2.08516 8.74731 1.55176 9.39257 1.16642C10.0378 0.781077 10.7655 0.554863 11.5155 0.50648L11.8202 0.497009H29.0118ZM29.0118 3.65376H11.8202C11.5867 3.65383 11.3562 3.70569 11.1452 3.80558C10.9343 3.90547 10.7481 4.05092 10.6001 4.23144L10.4817 4.39559L4.04194 14.7024L10.4817 25.0091C10.6054 25.2072 10.7715 25.3753 10.968 25.5014C11.1645 25.6275 11.3866 25.7084 11.6181 25.7384L11.8202 25.751H29.0118C29.3984 25.7509 29.7715 25.609 30.0604 25.3521C30.3493 25.0952 30.5339 24.7412 30.5791 24.3573L30.5902 24.1726V5.23213C30.5901 4.84553 30.4482 4.4724 30.1913 4.1835C29.9344 3.8946 29.5804 3.71004 29.1965 3.6648L29.0118 3.65376ZM16.1938 9.12124L19.54 12.4705L22.8893 9.12124C23.0359 8.9747 23.21 8.85847 23.4016 8.7792C23.5931 8.69993 23.7984 8.65917 24.0058 8.65925C24.2131 8.65932 24.4184 8.70023 24.6099 8.77963C24.8014 8.85904 24.9754 8.97538 25.1219 9.12203C25.2684 9.26868 25.3847 9.44275 25.4639 9.63431C25.5432 9.82588 25.584 10.0312 25.5839 10.2385C25.5838 10.4458 25.5429 10.6511 25.4635 10.8426C25.3841 11.0341 25.2678 11.2081 25.1211 11.3546L21.775 14.7024L25.1211 18.0501C25.4173 18.3461 25.5837 18.7475 25.5839 19.1662C25.584 19.5849 25.4179 19.9865 25.1219 20.2827C24.8259 20.5789 24.4245 20.7453 24.0058 20.7455C23.5871 20.7456 23.1855 20.5795 22.8893 20.2835L19.5416 16.9342L16.1938 20.2835C15.8977 20.5797 15.496 20.746 15.0771 20.746C14.6583 20.746 14.2566 20.5797 13.9604 20.2835C13.6643 19.9873 13.4979 19.5856 13.4979 19.1668C13.4979 18.748 13.6643 18.3463 13.9604 18.0501L17.3097 14.7024L13.9604 11.3546C13.6643 11.0585 13.4979 10.6568 13.4979 10.2379C13.4979 9.8191 13.6643 9.41741 13.9604 9.12124C14.2566 8.82507 14.6583 8.65869 15.0771 8.65869C15.496 8.65869 15.8977 8.82507 16.1938 9.12124Z"
						fill="black"
					/>
				</svg>
			</button>
			<div class="flex flex-col sm:flex-row items-center gap-2 md:gap-4">
				<!-- Left: Image -->
				<div class="flex-shrink-0 flex justify-center items-center h-full ml-0 sm:ml-8 object-contain">
					<img
						src={selectedItem.image_path
							? `https://mabini-cafe.bscs3a.com/phpbackend/${selectedItem.image_path.replace(/^\/?/, '')}`
							: ''}
						alt={selectedItem.name}
						class="w-32 h-32 sm:w-40 sm:h-40 object-cover rounded mb-0"
					/>
				</div>
				<!-- Right: Details -->
				<div class="flex flex-col justify-center items-start text-left h-full py-4 sm:py-20 px-2 sm:px-10">
					<h2 class="text-xl sm:text-2xl font-[1000] uppercase mb-2">{selectedItem.name}</h2>
					<p class="text-base sm:text-lg font-[300] text-gray-400 mb-2 sm:mb-4">₱ {selectedItem.price}</p>
					<p class="text-sm sm:text-base text-black font-[500] mb-2">{selectedItem.description}</p>
					<!-- Quantity Selector -->
					<div class="box flex items-center gap-4 my-4">
						<button
							class="minus bg-white text-mabini-black px-3 py-1 border-black font-bold text-xl cursor-pointer"
							on:click={decrease}>-</button
						>
						<span class="text-l font-bold w-8 text-center">{quantity}</span>
						<button
							class="plus bg-white text-mabini-black px-3 py-1  border-black font-bold text-xl cursor-pointer"
							on:click={increase}>+</button
						>
						
					</div>
					<div class="flex flex-col sm:flex-row gap-2 mt-4 w-full">
						<button
							class="add-to-cart uppercase bg-black text-mabini-white rounded-full font-bold hover:bg-mabini-dark-brown transition cursor-pointer text-sm sm:text-base"
							on:click={() => handleAddToCart(selectedItem)}
						>
							Add to Cart
						</button>
						<button
							on:click={() => handleCheckout(selectedItem)}
							class="buy-now uppercase bg-mabini-beige text-mabini-dark-brown rounded-full font-extrabold hover:bg-mabini-black transition cursor-pointer text-sm sm:text-base"
							>Buy Now</button
						>
					</div>
				</div>
			</div>
		</div>
	</div>
{/if}

<style>
	.box{
		border: 1px solid black;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
	}
	.plus {
		border-left: 1px solid black;
		border-radius: 0 4px 4px 0;
	}
	.minus {
		border-right: 1px solid black;
		border-radius: 4px 0 0 4px;
	}
	
	.add-to-cart{
		padding: 0.5rem 1rem;
		box-shadow: #f5f5dc 0 2px 4px;
		transition: background-color 0.3s ease;
	}
	@media (min-width: 640px) {
		.add-to-cart {
			padding: 0.5rem 1.25rem;
		}
	}
	.buy-now{
		padding: 0.5rem 1rem;
		box-shadow: #f5f5dc 0 2px 4px;
		transition: background-color 0.3s ease;
	}
	@media (min-width: 640px) {
		.buy-now {
			padding: 0.5rem 1.5rem;
		}
	}
	.buy-now:hover {
		color: var(--color-mabini-beige);
	}
</style>
