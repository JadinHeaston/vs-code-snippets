function removeEmptyGetParameters(relativeURL: string) {
	let url = new URL(relativeURL, window.location.href);
	let searchParams = new URLSearchParams(url.search);
	let deletionArray = [];
	for (let [name, value] of searchParams.entries()) {
		if (value === null || value === undefined || String(value).trim().length === 0) {
			deletionArray.push(name);
		}
	}
	deletionArray.forEach(searchParamName => {
		searchParams.delete(searchParamName);
	});
	url.search = searchParams.toString();
	return url.toString();
}