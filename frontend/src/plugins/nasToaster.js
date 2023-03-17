export default (context, inject) => {
	const toasterNA = (type, text, outline = false) => {
		const data = {
			id: generateID(),
			type: type,
			text: text,
			outline: outline,
			value: 0,
			interval: {},
		};
		context.store.commit("nas_toaster/toasterNew", data);
	};

	function generateID() {
		return (
			"Id" +
			Math.floor(
				(Date.now() *
					Math.floor(Math.random() * Math.floor(Math.random() * Date.now()))) /
					(Math.random() * 1000000000),
			)
		);
	}

	inject("toasterNA", toasterNA);

	// For Nuxt <= 2.12, also add 👇
	context.$toasterNA = toasterNA;
};
