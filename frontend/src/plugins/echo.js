import Echo from "laravel-echo";

window.Pusher = require("pusher-js");
// s
export default (app, inject) => {
	const echo = new Echo({
		broadcaster: "pusher",
		key: "7c0143f983453453cc",
		wsHost: "localhost",
		wsPort: 6001,
		forceTLS: false,
		disableStats: true,
		authorizer: (channel, options) => {
			return {
				authorize: (socketId, callback) => {
					app.$axios
						.post("http://localhost:8000/broadcasting/auth", {
							socket_id: socketId,
							channel_name: channel.name,
						})
						.then((response) => {
							callback(false, response.data);
						})
						.catch((error) => {
							callback(true, error);
						});
				},
			};
		},
		// server
		// broadcaster: "pusher",
		// key: "45464csfffa",
		// cluster: "hm120hm",
		// wsHost: "clientbackend.oredoh.org",
		// wsPort: 6001,
		// wssPort: 443,
		// authorizer: (channel, options) => {
		// 	return {
		// 		authorize: (socketId, callback) => {
		// 			app.$axios
		// 				.post("https://clientbackend.oredoh.org/broadcasting/auth", {
		// 					socket_id: socketId,
		// 					channel_name: channel.name,
		// 				})
		// 				.then((response) => {
		// 					callback(false, response.data);
		// 				})
		// 				.catch((error) => {
		// 					callback(true, error);
		// 				});
		// 		},
		// 	};
		// },
	});

	inject("echo", echo);
};
