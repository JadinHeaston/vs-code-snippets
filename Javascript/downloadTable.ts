async function downloadTable(event: Event) {
	await downloadCSVFile(await tableToCSV(<HTMLTableElement>(event.target as SVGAElement).parentElement.nextElementSibling));

	//Defining functions.
	async function tableToCSV(table: HTMLTableElement) {
		// Variable to store the final csv data
		let csv_data: string[] = [];

		// Get each row data
		let rows = table.getElementsByTagName('tr');
		for (let i = 0; i < rows.length; ++i) {
			//Get each column data
			let cols = rows[i].querySelectorAll('td,th');

			//Stores each csv row data
			let csvrow: string[] = [];
			for (let j = 0; j < cols.length; ++j) {
				let text = (cols[j] as HTMLTableCellElement).textContent!.replace(/(<([^>]+)>)/ig, '');
				text = "\"" + text + "\"";

				//Get the text data of each cell of
				//A row and push it to csvrow
				csvrow.push(text);
			}

			//Combine each column value with comma
			csv_data.push(csvrow.join(","));
		}
		// combine each row data with new line character
		return csv_data.join('\n');
	}

	async function downloadCSVFile(csv_data: string) {
		// Create CSV file object and feed our
		// csv_data into it
		let CSVFile = new Blob([csv_data], { type: "text/csv" });

		// Create to temporary link to initiate
		// download process
		let temp_link = document.createElement('a');

		//Download csv file
		temp_link.download = window.location.pathname.substring(window.location.pathname.lastIndexOf('/') + 1, window.location.pathname.lastIndexOf('.')) + ".csv";
		let url = window.URL.createObjectURL(CSVFile);
		temp_link.href = url;

		//This link should not be displayed
		temp_link.style.display = "none";
		document.body.appendChild(temp_link);

		//Automatically click the link to trigger download
		temp_link.click();
		document.body.removeChild(temp_link);
	}
}