<!-- !
[] Contact Reason Not yet finished
[] No script yet
[] Not responsive 
[] Style not yet Finish

-->

<div class="container text-center px-4">
	<div class="heading-container mt-20 mb-10">
		<div class="p-heading font-semibold max-w-[780px] mx-auto text-left">
			<h1 class="h1-heading uppercase">
				Contact <span class="!text-mabini-yellow">Us</span>
			</h1>
			<p>
				Feel free to reach out to us at any time. We're here to help with orders, product questions,
				and any feedback you may have.
			</p>

			<p class="mt-4">
				Our customer service team can assist by email Monday to Friday, from 9am to 5pm (excluding
				holidays). We'll aim to get back to you within 2 business days.
			</p>

			<p class="text-mabini-red mt-2 !text-normal">* indicates a required field</p>
		</div>
	</div>

	<div class="input-container flex flex-col items-center">
		<form method="post" class="flex flex-col items-center space-y-[25px] w-full max-w-[780px]">
			<input
				type="text"
				name="name"
				id="name"
				placeholder="First name and Last Name *"
				class="contact-input"
			/>

			<input type="email" name="email" id="email" placeholder="Email *" class="contact-input" />

			<select name="contact-reason" id="contact-reason" class="contact-input">
				<option value="" disabled selected>Contact Reason *</option>
				<option class="option">Issue with my product</option>
				<option class="option">Shipping Question</option>
				<option class="option">Product Question</option>
				<option class="option">Collaboration</option>
				<option class="option">Other / General Inquiry</option>
			</select>

			<input
				type="number"
				name="order-number"
				id="order-number"
				placeholder="Order Number *"
				class="contact-input"
			/>

			<input type="text" name="title" id="title" placeholder="Title *" class="contact-input" />

			<textarea
				name="description"
				id="description"
				placeholder="Tell us the details *"
				class="w-full h-[150px] border px-3 py-2 resize-none rounded-[7px] border-gray-500"
			></textarea>

			<label
				for="files"
				class="btn cursor-pointer w-full h-[40px] flex items-center justify-center border rounded-[7px] border-gray-500 text-gray-500"
			>
				Drag or Paste an image here
			</label>
			<input id="files" type="file" class="hidden" />

			<button type="submit" class="w-full btn-primary-clicked"> Submit </button>
		</form>
	</div>
</div>

<style>
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
</style>
