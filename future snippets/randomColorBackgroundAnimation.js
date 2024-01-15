// function start screensaver
function randomColorBackgroundAnimation(element, timeout = 4000, iterations = null, child = false) {
	element.style.backgroundColor = getNewColor();
	if (!child) {
		element.style.transitionDuration = timeout * 1.5 + "ms";
	}

	if (iterations === null) {
		setTimeout(() => {
			randomColorBackgroundAnimation(element, timeout, null, true);
		}, timeout);
	} else {
		for (let iterator = 0; iterator < iterations; ++iterator) {
			setTimeout(() => {
				randomColorBackgroundAnimation(element, timeout, 1, true);
			}, timeout);
		}
	}

	// change color fuction
	function getNewColor() {
		let myColor = "rgb(" + randomNumber() + ", " + randomNumber() + ", " + randomNumber() + ")";
		return myColor;

		function randomNumber() {
			return Math.floor(Math.random() * 256);
		}
	}
}
randomColorBackgroundAnimation(document.querySelector("body"));