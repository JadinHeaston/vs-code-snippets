async function removeEmptyFormValues(event: SubmitEvent) {
	let formData = new FormData(<HTMLFormElement>event.target);
	let deletionArray = [];
	for (let [name, value] of formData.entries()) {
		if (value === null || value === undefined || String(value).trim().length === 0) {
			deletionArray.push(name);
		}
	}
	deletionArray.forEach(formDataName => {
		formData.delete(formDataName);
	});
}