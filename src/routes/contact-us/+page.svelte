<!-- Contact Us Page -->
<div class="contact-container">
	<div class="contact-header">
		<h1 class="contact-title">
			Contact <span class="text-mabini-yellow">Us</span>
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
		<p class="text-mabini-red mt-2">* indicates a required field</p>
	</div>
	<form method="post" class="contact-form">
		<input type="text" name="title" id="title" placeholder="Title *" class="contact-input" />
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
			class="contact-input"
		/>

		<textarea
			name="description"
			id="description"
			placeholder="Tell us the details *"
			class="contact-textarea"
		></textarea>

		<label for="files" class="contact-upload-label">Drag or Paste an image here</label>
		<input id="files" type="file" class="contact-upload-input" />

		<button type="submit" class="contact-submit">Submit</button>
	</form>
</div>

<style>
	.contact-container {
		max-width: 600px;
		margin: 0 auto;
		padding: 2rem 1rem;
		background: white;
		border-radius: 1rem;
		/* box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08); */
	}

	.contact-header {
		text-align: left;
		margin-bottom: 2rem;
        font-weight: 300;
	}

	.contact-title {
		font-size: 4rem;
		font-weight: 900;
		text-transform: uppercase;
	}

	.contact-form {
		display: flex;
		flex-direction: column;
		gap: 1.25rem;
	}

	.contact-input {
		width: 100%;
		padding: 0.75rem;
		border: 1px solid #ccc;
		border-radius: 0.5rem;
		font-size: 1rem;
		height: 50px;
	}

	.contact-textarea {
		width: 100%;
		height: 120px;
		padding: 0.75rem;
		border: 1px solid #ccc;
		border-radius: 0.5rem;
		resize: none;
		font-size: 1rem;
	}

	.contact-upload-label {
		display: flex;
		align-items: center;
		justify-content: center;
		border: 1px dashed #ccc;
		border-radius: 0.5rem;
		padding: 0.75rem;
		color: #888;
		cursor: pointer;
	}

	.contact-upload-input {
		display: none;
	}

	.contact-submit {
		width: 100%;
		padding: 0.75rem;
		background: var(--color-mabini-white, #ffd700);
		color: #222;
		border: solid 1px;
		border-radius: 3rem;
		font-size: 1rem;
		font-weight: bold;
		cursor: pointer;
		transition: background 0.2s;
		margin-bottom: 5rem;
	}

	.contact-submit:hover {
		background: #e6c200;
	}

	.text-mabini-yellow {
		color: #ffd700;
	}

	.text-mabini-red {
		color: #e53935;
	}
</style>
