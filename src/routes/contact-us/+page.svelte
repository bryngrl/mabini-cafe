<!-- Contact Us Page -->
<!-- TODO: Implement form submission logic -->
<!-- TODO: Add validation for required fields -->
<!-- TODO: Handle file uploads -->

<script>
	import { goto } from '$app/navigation';
	import { contactsStore } from '$lib/stores';
	import { onMount } from 'svelte';
	let title = '';
	let name = '';
	let email = '';
	let contactReason = '';
	let orderNumber = '';
	let description = '';
	let file = null;
	let message = '';
	let error = '';
	let loading = false;

	// Email validation - only accept @gmail.com
	function validateEmail(email) {
		const emailRegex = /^[a-zA-Z0-9._-]+@gmail\.com$/;
		return emailRegex.test(email);
	}

	async function handleSubmit() {
		message = '';
		error = '';
		loading = true;

		// Validation
		if (!title || !name || !email || !contactReason || !description) {
			error = 'Please fill in all required fields';
			loading = false;
			return;
		}

		// Validate email
		if (!validateEmail(email)) {
			error = 'Please use a valid @gmail.com email address';
			loading = false;
			return;
		}

		try {
			const result = await contactsStore.submit(
				{
					topic: title,
					name: name,
					email: email,
					contact_reason: contactReason,
					message: description,
					order_id: orderNumber || null
				},
				file
			);

			if (result && result.message) {
				message = 'Contact form submitted successfully!';
				title = '';
				name = '';
				email = '';
				contactReason = '';
				orderNumber = '';
				description = '';
				file = null;
			}
		} catch (err) {
			error = (err && err.message) || 'Failed to submit contact form. Please try again.';
			console.error('Error submitting contact form:', err);
		} finally {
			loading = false;
		}
	}
</script>

<svelte:head>
	<title>Contact Us - Mabini Cafe</title>
	<meta name="description" content="Get in touch with Mabini Cafe" />
</svelte:head>

<div class="max-w-xl mx-auto  p-10 sm:p-12 md:p-14 bg-white rounded-xl">
	<div class="text-left mb-8 font-light">
		<h1 class="header-text mb-2">
			Contact <span class="text-[#ffd700]">Us</span>
		</h1>
		<p class="content-paragraph">
			Feel free to reach out to us at any time. We're here to help with orders, product questions,
			and any feedback you may have.
		</p>
		<p class="content-paragraph pt-4">
			Our customer service team can assist by email Monday to Friday, from 9am to 5pm (excluding
			holidays). We'll aim to get back to you within 2 business days.
		</p>
		<p class="text-[#e53935] pt-2 content-paragraph">* indicates a required field</p>
	</div>
	<form
		on:submit|preventDefault={handleSubmit}
		enctype="multipart/form-data"
		class="flex flex-col gap-5"
	>
		<input
			type="text"
			name="title"
			id="title"
			placeholder="Title *"
			class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base"
			bind:value={title}
		/>
		<input
			type="text"
			name="name"
			id="name"
			placeholder="First name and Last Name *"
			bind:value={name}
			class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base"
		/>
		<input
			type="email"
			name="email"
			id="email"
			placeholder="Email (@gmail.com only) *"
			class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base"
			bind:value={email}
			pattern="[a-zA-Z0-9._-]+@gmail\.com"
			title="Please use a valid @gmail.com email address"
			required
		/>
		<select
			name="contact-reason"
			id="contact-reason"
			class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base"
			bind:value={contactReason}
		>
			<option value="" disabled selected>Contact Reason *</option>
			<option>Issue with my product</option>
			<option>Shipping Question</option>
			<option>Product Question</option>
			<option>Collaboration</option>
			<option>Other / General Inquiry</option>
		</select>
		<input
			type="number"
			name="order-number"
			id="order-number"
			placeholder="Order Number *"
			class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base"
			bind:value={orderNumber}
		/>
		<textarea
			name="description"
			id="description"
			placeholder="Tell us the details *"
			class="w-full h-28 px-4 py-3 border border-gray-300 rounded-lg resize-none text-base"
			bind:value={description}
			required
		></textarea>
		<label
			for="files"
			class="flex items-center justify-center border border-dashed border-gray-300 rounded-lg px-4 py-3 text-gray-500 cursor-pointer"
		>
			{file ? file.name : 'Drag or Paste an image here'}
		</label>
		<input
			id="files"
			name="files"
			type="file"
			accept="image/*"
			class="hidden"
			on:change={(e) => {
				const target = e.currentTarget;
				file = target.files ? target.files[0] : null;
			}}
		/>
		{#if error}
			<p class="text-red-600 text-sm">{error}</p>
		{/if}
		{#if message}
			<p class="text-green-600 text-sm">{message}</p>
		{/if}
		<button
			type="submit"
			disabled={loading}
			class="w-full px-4 py-3 text-black border border-solid border-black rounded-full font-bold text-base cursor-pointer transition duration-200 hover:bg-mabini-black hover:text-white hover-border-0 mb-12 disabled:opacity-50 disabled:cursor-not-allowed"
		>
			{loading ? 'Submitting...' : 'Submit'}
		</button>
	</form>
</div>
