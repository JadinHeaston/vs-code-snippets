var imagePreviewAttachStatus = false; //Global variables stores status to prevent multiple images showing at once.

//Add the following class and base64 encoded image to HTML element:
//class="image-preview" data-image=""
jQuery(document).on('mouseover mouseleave', '.image-preview-trigger', function(event)
{
	if (event.type === "mouseleave")
	{
		removeImagePreview(previewImage);
		return;
	}

	//Getting image data.
	var imageData = jQuery(this).attr('data-image');

	if (imageData === undefined)
		return false;

	if (imagePreviewAttachStatus) //Image already attached.
		return;
	
	previewImage = document.createElement('img'); //Creating image element.

	jQuery(previewImage).attr('src', imageData);
	previewImage.classList.add("image-preview");

	document.body.appendChild(previewImage); //Appending element to the body.

	showImagePreview(previewImage);
});

const imagePreviewFollowMouse = (event) => {
	previewImage.style.left = event.x + "px";
	previewImage.style.top = event.y + "px";
}

function showImagePreview(image) {
	if (imagePreviewAttachStatus) //Image already attached.
		return;
	
	imagePreviewAttachStatus = true;
	image.style.display = "block";
	document.addEventListener("pointermove", imagePreviewFollowMouse);
}

function removeImagePreview(image) {
	imagePreviewAttachStatus = false;
	image.remove(); //Removing image element.
	document.removeEventListener("pointermove", imagePreviewFollowMouse);
}