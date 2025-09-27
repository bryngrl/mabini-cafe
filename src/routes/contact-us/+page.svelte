<!-- Contact Us Page -->
<!-- TODO: Implement form submission logic -->
<!-- TODO: Add validation for required fields -->
<!-- TODO: Handle file uploads -->

<script>
	let title = '';
	let name = '';
	let email = '';
	let contactReason = '';
	let orderNumber = '';
	let description = '';
	let file = null;
	// Function ulit for handling submission dito
</script>

<div class="max-w-xl mx-auto p-8 bg-white rounded-xl">
	<div class="text-left mb-8 font-light">
		<h1 class="h1-heading font-extrabold uppercase mb-2">
			Contact <span class="text-[#ffd700]">Us</span>
		</h1>
		<p>
			Feel free to reach out to us at any time. We're here to help with orders, product questions,
			and any feedback you may have.
		</p>
		<br />
		<p class="mt-4">
			Our customer service team can assist by email Monday to Friday, from 9am to 5pm (excluding
			holidays). We'll aim to get back to you within 2 business days.
		</p>
		<p class="text-[#e53935] mt-2">* indicates a required field</p>
	</div>
	<form method="post" enctype="multipart/form-data" class="flex flex-col gap-5">
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
			placeholder="Email *"
			class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base"
			bind:value={email}
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
		></textarea>
		<label
			for="files"
			class="flex items-center justify-center border border-dashed border-gray-300 rounded-lg px-4 py-3 text-gray-500 cursor-pointer"
			>Drag or Paste an image here</label
		>
		<input
			id="files"
			name="files"
			type="file"
			class="hidden"
			on:change={(e) => (file = e.target.files[0])}
		/>
		<button
			type="submit"
			class="w-full px-4 py-3 text-black border border-solid border-black rounded-full font-bold text-base cursor-pointer transition duration-200 hover:bg-mabini-black hover:text-white hover-border-0 mb-12"
			>Submit</button
		>
	</form>
</div>
