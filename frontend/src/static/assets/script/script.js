$(document).ready(function () {
	carousel();
});
function carousel(items, rtl) {
	if (items >= 4) {
		items = 4;
	}
	$(".stepper-header-owl").owlCarousel({
		loop: false,
		margin: 0,
		nav: false,
		rtl: rtl,
		dots: false,
		responsive: {
			0: {
				items: 2,
			},
			700: {
				items: items,
			},
			1000: {
				items: items,
			},
		},
	});
	window.dispatchEvent(new Event("resize"));
}
function dataURLtoFile(dataurl, filename) {
	var arr = dataurl.split(","),
		mime = arr[0].match(/:(.*?);/)[1],
		bstr = atob(arr[1]),
		n = bstr.length,
		u8arr = new Uint8Array(n);

	while (n--) {
		u8arr[n] = bstr.charCodeAt(n);
	}

	return new File([u8arr], filename, { type: mime });
}





