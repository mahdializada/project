<template>
	<div>
		<v-card
			v-if="error.statusCode === 404"
			class="text-center w-full error-page pa-3"
		>
			<v-img
				src="/images/illustrations/404-illustration.svg"
				max-height="300"
				contain
			/>
			<div class="display-2 mt-10">{{ $tr("how_did_you_get_here") }}</div>
			<div class="mt-3 mb-6">
				{{ $tr("cant_find_page") }}
			</div>
			<v-text-field solo placeholder="Search website" large></v-text-field>
			<v-btn block large @click="back" color="primary">
				{{ $tr("sent_me_back") }}
			</v-btn>
		</v-card>
		<v-card
			v-else-if="error.statusCode === 401"
			class="text-center w-full error-page pa-3"
		>
			<v-img
				src="/images/illustrations/401-illustration.svg"
				max-height="300"
				contain
			/>
			<div class="display-2 mt-10">{{ $tr("unauthorized") }}</div>
			<div class="mt-3 mb-6">
				{{ $tr("unauthorized_to_page") }}
			</div>
			<v-text-field solo placeholder="Search website" large></v-text-field>
			<v-btn block large @click="back" color="primary">
				{{ $tr("sent_me_back") }}
			</v-btn>
		</v-card>
		<v-card
			v-else-if="error.statusCode === 403"
			class="text-center w-full error-page pa-3"
		>
			<!-- <v-img
				src="/images/illustrations/401-illustration.svg"
				max-height="300"
				contain
			/> -->

			<svg
				xmlns="http://www.w3.org/2000/svg"
				xmlns:xlink="http://www.w3.org/1999/xlink"
				width="300"
				viewBox="0 0 184.991 156.537"
			>
				<defs>
					<filter
						id="Path_11"
						x="23.691"
						y="0"
						width="161.3"
						height="126.297"
						filterUnits="userSpaceOnUse"
					>
						<feOffset dx="-3" dy="3" input="SourceAlpha" />
						<feGaussianBlur stdDeviation="7.5" result="blur" />
						<feFlood flood-opacity="0.102" />
						<feComposite operator="in" in2="blur" />
						<feComposite in="SourceGraphic" />
					</filter>
				</defs>
				<g id="location-error" transform="translate(-612.491 -89.06)">
					<circle
						id="Ellipse_32"
						data-name="Ellipse 32"
						cx="75.5"
						cy="75.5"
						r="75.5"
						transform="translate(617.491 94.597)"
						fill="rgba(46,123,230,0.1)"
					/>
					<g
						id="Path_15"
						data-name="Path 15"
						transform="translate(612.491 115.597)"
						fill="#fff"
					>
						<path
							d="M 156 90.5 L 5 90.5 C 3.07008695602417 90.5 1.5 88.92991638183594 1.5 87 L 1.5 5 C 1.5 3.07008695602417 3.07008695602417 1.5 5 1.5 L 156 1.5 C 157.9299163818359 1.5 159.5 3.07008695602417 159.5 5 L 159.5 87 C 159.5 88.92991638183594 157.9299163818359 90.5 156 90.5 Z"
							stroke="none"
						/>
						<path
							d="M 5 3 C 3.897201538085938 3 3 3.897201538085938 3 5 L 3 87 C 3 88.10279846191406 3.897201538085938 89 5 89 L 156 89 C 157.1027984619141 89 158 88.10279846191406 158 87 L 158 5 C 158 3.897201538085938 157.1027984619141 3 156 3 L 5 3 M 5 0 L 156 0 C 158.7614288330078 0 161 2.238571166992188 161 5 L 161 87 C 161 89.76140594482422 158.7614288330078 92 156 92 L 5 92 C 2.238571166992188 92 0 89.76140594482422 0 87 L 0 5 C 0 2.238571166992188 2.238571166992188 0 5 0 Z"
							stroke="none"
							fill="#2e7be6"
						/>
					</g>
					<line
						id="Line_18"
						data-name="Line 18"
						y2="16.937"
						transform="translate(692.732 207.923)"
						fill="none"
						stroke="#2e7be6"
						stroke-width="10"
					/>
					<path
						id="Path_14"
						data-name="Path 14"
						d="M2,0H30a2,2,0,0,1,2,2V80a2,2,0,0,1-2,2H2a2,2,0,0,1-2-2V2A2,2,0,0,1,2,0Z"
						transform="translate(617.491 120.597)"
						fill="#2e7be6"
					/>
					<line
						id="Line_22"
						data-name="Line 22"
						x2="20.324"
						transform="translate(623.856 162.194)"
						fill="none"
						stroke="rgba(255,255,255,0.28)"
						stroke-linecap="round"
						stroke-width="3"
					/>
					<line
						id="Line_20"
						data-name="Line 20"
						x2="20.324"
						transform="translate(623.856 155.419)"
						fill="none"
						stroke="rgba(255,255,255,0.28)"
						stroke-linecap="round"
						stroke-width="3"
					/>
					<line
						id="Line_21"
						data-name="Line 21"
						x2="20.324"
						transform="translate(623.856 168.968)"
						fill="none"
						stroke="rgba(255,255,255,0.28)"
						stroke-linecap="round"
						stroke-width="3"
					/>
					<g
						id="Group_86"
						data-name="Group 86"
						transform="translate(627.78 126.408)"
					>
						<path
							id="Path_12"
							data-name="Path 12"
							d="M53.182,27.923c.425.245.85.49,1.259.752.654.376,1.308.768,1.945,1.144a1.451,1.451,0,0,1,.556.441c.36.572-.376,1.193-.915.9-.409-.213-.785-.474-1.177-.7-.654-.392-1.308-.768-1.978-1.161a4.26,4.26,0,0,1-.458-.278.622.622,0,0,1-.147-.213,2.527,2.527,0,0,1-.049-.278l-.311-.2a6.723,6.723,0,0,1-1.259,1.144,4.163,4.163,0,0,1-3.253.343A4.347,4.347,0,0,1,45.3,28.332a3.964,3.964,0,0,1-.539-1.046,4.021,4.021,0,0,1-.147-.638,1.284,1.284,0,0,1,.016-.785.653.653,0,0,1,.556-.327.636.636,0,0,1,.67.539,2.8,2.8,0,0,0,2.763,2.6,2.779,2.779,0,1,0-.049-5.558,3.3,3.3,0,0,0-2.6,1.586.654.654,0,0,1-.932.1.5.5,0,0,1-.2-.36,1.868,1.868,0,0,1,.523-1.079,3.627,3.627,0,0,1,1.586-1.21,4.082,4.082,0,0,1,2.5-.262,8.2,8.2,0,0,0-.834-.85c-.213-.2-.523-.343-.621-.621a.636.636,0,0,1,.033-.458.738.738,0,0,1,.343-.327.7.7,0,0,1,.654.18l.588.49a9.114,9.114,0,0,1,2.06,2.73c-.016-.016,0-.065,0-.082a.965.965,0,0,0-.033-.229,7.5,7.5,0,0,0-.163-.834,7.755,7.755,0,0,0-.425-1.226,6.376,6.376,0,0,0-.311-.572,1.282,1.282,0,0,1-.245-.523v-.049a.6.6,0,0,1,.588-.621c.441.016.687.523.719.621a9.371,9.371,0,0,1,.915,2.3s.131.654.147.752a10.6,10.6,0,0,0,.327-2.223.5.5,0,0,1,.6-.458c.376.016.572.294.588.7a18.961,18.961,0,0,1-2.141,7.111l.278.2C52.577,27.956,52.839,27.711,53.182,27.923Z"
							transform="translate(-44.561 -18.9)"
							fill="#fff"
						/>
						<path
							id="Path_13"
							data-name="Path 13"
							d="M59.616,59.663a.474.474,0,0,1-.474-.474.458.458,0,0,1,.36-.458,1.082,1.082,0,1,0,.556.948,1.262,1.262,0,0,0-.033-.245A.481.481,0,0,1,59.616,59.663Zm-.343.556a.2.2,0,1,1,.2-.2A.21.21,0,0,1,59.273,60.218Z"
							transform="translate(-55.72 -52.11)"
							fill="#fff"
						/>
					</g>
					<line
						id="Line_27"
						data-name="Line 27"
						x2="53.069"
						transform="translate(665.633 225.425)"
						fill="none"
						stroke="#2e7be6"
						stroke-linecap="round"
						stroke-width="3"
					/>
					<rect
						id="Rectangle_35"
						data-name="Rectangle 35"
						width="35"
						height="7"
						rx="3.5"
						transform="translate(619.491 144.597)"
						fill="#fff"
					/>
					<line
						id="Line_19"
						data-name="Line 19"
						x2="20.324"
						transform="translate(623.856 148.351)"
						fill="none"
						stroke="rgba(48,81,211,0.3)"
						stroke-linecap="round"
						stroke-width="3"
					/>
					<g
						id="Group_85"
						data-name="Group 85"
						transform="translate(661.681 108.56)"
					>
						<g
							transform="matrix(1, 0, 0, 1, -49.19, -19.5)"
							filter="url(#Path_11)"
						>
							<path
								id="Path_11-2"
								data-name="Path 11"
								d="M2.258,0H114.041A2.258,2.258,0,0,1,116.3,2.258v76.78a2.258,2.258,0,0,1-2.258,2.258H2.258A2.258,2.258,0,0,1,0,79.039V2.258A2.258,2.258,0,0,1,2.258,0Z"
								transform="translate(49.19 19.5)"
								fill="#fff"
							/>
						</g>
						<line
							id="Line_23"
							data-name="Line 23"
							x2="51.94"
							transform="translate(12.985 40.084)"
							fill="none"
							stroke="rgba(172,172,172,0.28)"
							stroke-linecap="round"
							stroke-width="3"
						/>
						<line
							id="Line_26"
							data-name="Line 26"
							x2="90.33"
							transform="translate(12.985 60.408)"
							fill="none"
							stroke="rgba(172,172,172,0.28)"
							stroke-linecap="round"
							stroke-width="3"
						/>
						<line
							id="Line_24"
							data-name="Line 24"
							x2="90.33"
							transform="translate(12.985 50.246)"
							fill="none"
							stroke="rgba(172,172,172,0.28)"
							stroke-linecap="round"
							stroke-width="3"
						/>
						<line
							id="Line_25"
							data-name="Line 25"
							x2="90.33"
							transform="translate(12.985 70.57)"
							fill="none"
							stroke="rgba(172,172,172,0.28)"
							stroke-linecap="round"
							stroke-width="3"
						/>
						<path
							id="globe-solid"
							d="M12.1,8.8a21.494,21.494,0,0,1-.113,2.2H5.613a21.3,21.3,0,0,1-.144-2.2,21.3,21.3,0,0,1,.144-2.2h6.373A21.494,21.494,0,0,1,12.1,8.8Zm5.222-2.2a8.831,8.831,0,0,1,0,4.4H13.09c.072-.708.11-1.475.11-2.2a21.718,21.718,0,0,0-.11-2.2ZM16.96,5.5H12.949a12.087,12.087,0,0,0-1.9-5.21A8.82,8.82,0,0,1,16.96,5.5Zm-5.125,0H5.765a13.8,13.8,0,0,1,.928-3.254A5.62,5.62,0,0,1,7.844.475,1.416,1.416,0,0,1,8.8,0a1.416,1.416,0,0,1,.956.475,5.62,5.62,0,0,1,1.152,1.772A13.805,13.805,0,0,1,11.835,5.5ZM.64,5.5A8.818,8.818,0,0,1,6.552.29a12.087,12.087,0,0,0-1.9,5.21ZM4.51,6.6a21.575,21.575,0,0,0-.141,2.2c0,.725.069,1.492.141,2.2H.277a8.868,8.868,0,0,1,0-4.4Zm2.183,8.752A13.775,13.775,0,0,1,5.765,12.1h6.071a13.775,13.775,0,0,1-.928,3.252,5.6,5.6,0,0,1-1.152,1.774,1.423,1.423,0,0,1-.987.474,1.424,1.424,0,0,1-.925-.474,5.6,5.6,0,0,1-1.152-1.774Zm-.141,1.959A8.821,8.821,0,0,1,.64,12.1H4.651a12.091,12.091,0,0,0,1.9,5.211Zm4.5,0a12.091,12.091,0,0,0,1.9-5.211H16.96a8.823,8.823,0,0,1-5.912,5.211Z"
							transform="translate(11.856 13.568)"
							fill="#2e7be6"
							opacity="0.4"
						/>
						<g
							id="Group_88"
							data-name="Group 88"
							transform="translate(21.327 24.702)"
						>
							<g
								id="Ellipse_33"
								data-name="Ellipse 33"
								transform="translate(0.483 0.335)"
								fill="#2e7be6"
								stroke="#fff"
								stroke-width="1"
							>
								<circle cx="4" cy="4" r="4" stroke="none" />
								<circle cx="4" cy="4" r="4.5" fill="none" />
							</g>
							<path
								id="Icon_ionic-ios-close"
								data-name="Icon ionic-ios-close"
								d="M13.887,13.392l1.5-1.5a.352.352,0,0,0-.5-.5l-1.5,1.5-1.5-1.5a.352.352,0,1,0-.5.5l1.5,1.5-1.5,1.5a.352.352,0,0,0,.5.5l1.5-1.5,1.5,1.5a.352.352,0,0,0,.5-.5Z"
								transform="translate(-8.906 -9.056)"
								fill="#fff"
							/>
						</g>
					</g>
				</g>
			</svg>

			<div class="mt-10" style="font-size: 22px">
				{{ $tr("location_unavailable") }}
			</div>
			<div class="mt-1 mb-1" style="font-size: 16px">
				{{ $tr("you_have_to_allow_location_first") }}
			</div>
			<div class="mb-2" style="font-size: 16px">
				{{ $tr("please_turn_on_location") }}
			</div>
			<div class="mb-2 mx-auto" style="font-size: 14px; max-width: 300px">
				To turn on Location on your browser, please follow this steps:
			</div>

			<v-row class="ma-0">
				<v-col cols="12" md="6">
					<div class="step-counter mb-2">1</div>
					<p
						class="ma-0 mb-1"
						style="
							font-size: 12px;
							color: var(--input-placeholder-color);
							height: 40px;
						"
					>
						Click on the icon appeared on the browser URL search box.
					</p>

					<svg
						xmlns="http://www.w3.org/2000/svg"
						class="w-full"
						viewBox="0 0 183 27"
					>
						<g id="toolbar" transform="translate(0.302 0.165)">
							<path
								id="Rectangle_24"
								data-name="Rectangle 24"
								d="M9,0H174a9,9,0,0,1,9,9V27a0,0,0,0,1,0,0H0a0,0,0,0,1,0,0V9A9,9,0,0,1,9,0Z"
								transform="translate(-0.302 -0.165)"
								fill="rgba(201,201,201,0.37)"
							/>
							<rect
								id="Rectangle_25"
								data-name="Rectangle 25"
								width="115"
								height="15"
								rx="7.5"
								transform="translate(37.698 6.835)"
								fill="#fff"
							/>
							<g
								id="Group_78"
								data-name="Group 78"
								transform="translate(8.17 11.801)"
							>
								<circle
									id="Ellipse_16"
									data-name="Ellipse 16"
									cx="2.5"
									cy="2.5"
									r="2.5"
									transform="translate(8.528 0.034)"
									fill="#f8a500"
								/>
								<ellipse
									id="Ellipse_27"
									data-name="Ellipse 27"
									cx="3"
									cy="2.5"
									rx="3"
									ry="2.5"
									transform="translate(-0.472 0.034)"
									fill="#f80000"
								/>
								<circle
									id="Ellipse_17"
									data-name="Ellipse 17"
									cx="2.5"
									cy="2.5"
									r="2.5"
									transform="translate(16.528 0.034)"
									fill="#18c105"
								/>
							</g>
							<circle
								id="Ellipse_18"
								data-name="Ellipse 18"
								cx="13"
								cy="13"
								r="13"
								transform="translate(130.698 0.835)"
								fill="rgba(48,81,211,0.08)"
							/>
							<path
								id="Icon_material-location-on"
								data-name="Icon material-location-on"
								d="M10.15,3A2.648,2.648,0,0,0,7.5,5.65c0,1.987,2.65,4.921,2.65,4.921S12.8,7.637,12.8,5.65A2.648,2.648,0,0,0,10.15,3Zm0,3.6A.946.946,0,1,1,11.1,5.65.947.947,0,0,1,10.15,6.6Z"
								transform="translate(133.277 7.051)"
								fill="#2e7be6"
							/>
							<text
								id="https:_website.com"
								data-name="https://website.com"
								transform="translate(51.698 15.835)"
								fill="#c8c8c8"
								font-size="5"
								font-family="Poppins-Regular, Poppins"
							>
								<tspan x="0" y="0">https://website.com</tspan>
							</text>
							<path
								id="Icon_awesome-lock"
								data-name="Icon awesome-lock"
								d="M5.584,3.127H5.249V2.122a2.122,2.122,0,1,0-4.244,0V3.127H.67A.67.67,0,0,0,0,3.8v2.68a.67.67,0,0,0,.67.67H5.584a.67.67,0,0,0,.67-.67V3.8A.67.67,0,0,0,5.584,3.127Zm-1.452,0H2.122V2.122a1.005,1.005,0,1,1,2.01,0Z"
								transform="translate(44.947 10.62)"
								fill="#c8c8c8"
							/>
							<g
								id="Group_79"
								data-name="Group 79"
								transform="translate(168.848 9.986)"
							>
								<circle
									id="Ellipse_28"
									data-name="Ellipse 28"
									cx="1.5"
									cy="1.5"
									r="1.5"
									transform="translate(-0.15 -0.15)"
									fill="#fff"
								/>
								<circle
									id="Ellipse_29"
									data-name="Ellipse 29"
									cx="1.5"
									cy="1.5"
									r="1.5"
									transform="translate(-0.15 3.85)"
									fill="#fff"
								/>
								<circle
									id="Ellipse_30"
									data-name="Ellipse 30"
									cx="1.5"
									cy="1.5"
									r="1.5"
									transform="translate(-0.15 6.85)"
									fill="#fff"
								/>
							</g>
						</g>
					</svg>
				</v-col>
				<v-col cols="12" md="6">
					<div class="step-counter mb-2">2</div>
					<p
						class="ma-0 mb-1"
						style="
							font-size: 12px;
							color: var(--input-placeholder-color);
							height: 40px;
						"
					>
						Then check "continue allowing this site to access your location".
					</p>

					<svg
						xmlns="http://www.w3.org/2000/svg"
						xmlns:xlink="http://www.w3.org/1999/xlink"
						class="w-full"
						viewBox="0 0 183 127"
					>
						<defs>
							<filter
								id="Rectangle_26"
								x="23"
								y="25"
								width="149"
								height="102"
								filterUnits="userSpaceOnUse"
							>
								<feOffset dy="3" input="SourceAlpha" />
								<feGaussianBlur stdDeviation="3" result="blur" />
								<feFlood flood-opacity="0.161" />
								<feComposite operator="in" in2="blur" />
								<feComposite in="SourceGraphic" />
							</filter>
						</defs>
						<g id="toolbar-2" transform="translate(-703 -507)">
							<g
								id="Group_81"
								data-name="Group 81"
								transform="translate(703.233 507.165)"
							>
								<path
									id="Rectangle_24"
									data-name="Rectangle 24"
									d="M9,0H174a9,9,0,0,1,9,9V27a0,0,0,0,1,0,0H0a0,0,0,0,1,0,0V9A9,9,0,0,1,9,0Z"
									transform="translate(-0.234 -0.165)"
									fill="rgba(201,201,201,0.37)"
								/>
								<rect
									id="Rectangle_25"
									data-name="Rectangle 25"
									width="115"
									height="15"
									rx="7.5"
									transform="translate(37.766 6.835)"
									fill="#fff"
								/>
								<g
									id="Group_78"
									data-name="Group 78"
									transform="translate(8.17 11.801)"
								>
									<circle
										id="Ellipse_16"
										data-name="Ellipse 16"
										cx="2.5"
										cy="2.5"
										r="2.5"
										transform="translate(8.596 0.034)"
										fill="#f8a500"
									/>
									<ellipse
										id="Ellipse_27"
										data-name="Ellipse 27"
										cx="3"
										cy="2.5"
										rx="3"
										ry="2.5"
										transform="translate(-0.404 0.034)"
										fill="#f80000"
									/>
									<circle
										id="Ellipse_17"
										data-name="Ellipse 17"
										cx="2.5"
										cy="2.5"
										r="2.5"
										transform="translate(16.596 0.034)"
										fill="#18c105"
									/>
								</g>
								<path
									id="Icon_material-location-on"
									data-name="Icon material-location-on"
									d="M10.15,3A2.648,2.648,0,0,0,7.5,5.65c0,1.987,2.65,4.921,2.65,4.921S12.8,7.637,12.8,5.65A2.648,2.648,0,0,0,10.15,3Zm0,3.6A.946.946,0,1,1,11.1,5.65.947.947,0,0,1,10.15,6.6Z"
									transform="translate(133.277 7.051)"
									fill="#2e7be6"
								/>
								<text
									id="https:_website.com"
									data-name="https://website.com"
									transform="translate(51.766 15.835)"
									fill="#c8c8c8"
									font-size="5"
									font-family="Poppins-Regular, Poppins"
								>
									<tspan x="0" y="0">https://website.com</tspan>
								</text>
								<path
									id="Icon_awesome-lock"
									data-name="Icon awesome-lock"
									d="M5.584,3.127H5.249V2.122a2.122,2.122,0,1,0-4.244,0V3.127H.67A.67.67,0,0,0,0,3.8v2.68a.67.67,0,0,0,.67.67H5.584a.67.67,0,0,0,.67-.67V3.8A.67.67,0,0,0,5.584,3.127Zm-1.452,0H2.122V2.122a1.005,1.005,0,1,1,2.01,0Z"
									transform="translate(44.947 10.62)"
									fill="#c8c8c8"
								/>
								<g
									id="Group_79"
									data-name="Group 79"
									transform="translate(168.848 9.986)"
								>
									<circle
										id="Ellipse_28"
										data-name="Ellipse 28"
										cx="1.5"
										cy="1.5"
										r="1.5"
										transform="translate(-0.082 -0.15)"
										fill="#fff"
									/>
									<circle
										id="Ellipse_29"
										data-name="Ellipse 29"
										cx="1.5"
										cy="1.5"
										r="1.5"
										transform="translate(-0.082 3.85)"
										fill="#fff"
									/>
									<circle
										id="Ellipse_30"
										data-name="Ellipse 30"
										cx="1.5"
										cy="1.5"
										r="1.5"
										transform="translate(-0.082 6.85)"
										fill="#fff"
									/>
								</g>
							</g>
							<g
								id="Group_67"
								data-name="Group 67"
								transform="translate(735.006 538.029)"
							>
								<g
									transform="matrix(1, 0, 0, 1, -32.01, -31.03)"
									filter="url(#Rectangle_26)"
								>
									<rect
										id="Rectangle_26-2"
										data-name="Rectangle 26"
										width="131"
										height="84"
										rx="3"
										transform="translate(32 31)"
										fill="#fff"
									/>
								</g>
								<text
									id="Location_access_allowed"
									data-name="Location access allowed"
									transform="translate(8.994 9.971)"
									fill="#6b6969"
									font-size="5"
									font-family="Poppins-Medium, Poppins"
									font-weight="500"
								>
									<tspan x="0" y="0">Location access allowed</tspan>
								</text>
								<text
									id="This_site_can_access_your_location_"
									data-name="This site can access your location,"
									transform="translate(8.994 17.971)"
									fill="#d6d6d6"
									font-size="2"
									font-family="Poppins-Medium, Poppins"
									font-weight="500"
								>
									<tspan x="0" y="0">This site can access your location,</tspan>
								</text>
								<text
									id="Continue_allowing_this_site_to_access_you_location"
									data-name="Continue allowing this site to access you
 location"
									transform="translate(17.994 34.971)"
									fill="#545454"
									font-size="4"
									font-family="Poppins-Medium, Poppins"
									font-weight="500"
								>
									<tspan x="0" y="0">
										Continue allowing this site to access you
									</tspan>
									<tspan x="0" y="5" xml:space="preserve">location</tspan>
								</text>
								<text
									id="Always_block_http:_website.com_from_accessing_your_location"
									data-name="Always block http://website.com from
accessing your location"
									transform="translate(17.994 53.971)"
									fill="#545454"
									font-size="4"
									font-family="Poppins-Medium, Poppins"
									font-weight="500"
								>
									<tspan x="0" y="0">
										Always block http://website.com from
									</tspan>
									<tspan x="0" y="5">accessing your location</tspan>
								</text>
								<g
									id="Group_60"
									data-name="Group 60"
									transform="translate(105.303 72.623)"
								>
									<rect
										id="Rectangle_27"
										data-name="Rectangle 27"
										width="12"
										height="6"
										rx="1"
										transform="translate(-0.309 0.348)"
										fill="#e8e8e8"
									/>
									<text
										id="Done"
										transform="translate(1.691 4.348)"
										fill="#fff"
										font-size="3"
										font-family="Poppins-Medium, Poppins"
										font-weight="500"
									>
										<tspan x="0" y="0">Done</tspan>
									</text>
								</g>
								<g
									id="Group_61"
									data-name="Group 61"
									transform="translate(9.078 72.623)"
								>
									<g
										id="Rectangle_27-2"
										data-name="Rectangle 27"
										transform="translate(-0.084 0.348)"
										fill="none"
										stroke="#f1f1f1"
										stroke-width="0.1"
									>
										<rect width="21" height="7" rx="1" stroke="none" />
										<rect
											x="0.05"
											y="0.05"
											width="20.9"
											height="6.9"
											rx="0.95"
											fill="none"
										/>
									</g>
									<text
										id="Manage"
										transform="translate(1.916 5.348)"
										fill="#d6d5d5"
										font-size="4"
										font-family="Poppins-Medium, Poppins"
										font-weight="500"
									>
										<tspan x="0" y="0">Manage</tspan>
									</text>
								</g>
								<path
									id="Icon_ionic-ios-radio-button-off"
									data-name="Icon ionic-ios-radio-button-off"
									d="M5.433,3.652a1.78,1.78,0,1,1-1.259.521,1.769,1.769,0,0,1,1.259-.521m0-.277A2.058,2.058,0,1,0,7.49,5.433,2.057,2.057,0,0,0,5.433,3.375Z"
									transform="translate(5.809 48.472)"
								/>
								<ellipse
									id="Ellipse_22"
									data-name="Ellipse 22"
									cx="7.5"
									cy="8"
									rx="7.5"
									ry="8"
									transform="translate(3.994 26.971)"
									fill="rgba(48,81,211,0.08)"
								/>
								<g
									id="Icon_ionic-ios-radio-button-on"
									data-name="Icon ionic-ios-radio-button-on"
									transform="translate(9.519 32.517)"
								>
									<path
										id="Path_7"
										data-name="Path 7"
										d="M5.433,3.652a1.78,1.78,0,1,1-1.259.521,1.769,1.769,0,0,1,1.259-.521m0-.277A2.058,2.058,0,1,0,7.49,5.433,2.057,2.057,0,0,0,5.433,3.375Z"
										transform="translate(-3.375 -3.375)"
										fill="#2e7be6"
									/>
									<path
										id="Path_8"
										data-name="Path 8"
										d="M9.88,7.753a1.5,1.5,0,1,0,.44,1.063A1.494,1.494,0,0,0,9.88,7.753Z"
										transform="translate(-6.759 -6.759)"
										fill="#2e7be6"
									/>
								</g>
							</g>
						</g>
					</svg>
				</v-col>
			</v-row>
			<v-btn block large @click="reload" color="primary">
				<v-icon left> mdi-reload </v-icon>
				{{ $tr("reload") }}
			</v-btn>
		</v-card>
		<v-card v-else class="text-center w-full error-page pa-3">
			<v-img
				src="/images/illustrations/500-illustration.svg"
				max-height="300"
				contain
			/>
			<div class="display-2 mt-10">{{ $tr("oops_something_went_wrong") }}</div>
			<div class="mt-3 mb-10">{{ $tr("our_experts_are_working") }}</div>
			<v-text-field solo placeholder="Search website"></v-text-field>
			<v-btn block large @click="back" color="primary">
				{{ $tr("sent_me_back") }}
			</v-btn>
		</v-card>
		<LoadingComponent :loadingProps.sync="loading" />
	</div>
</template>

<script>
import { mapActions } from "vuex";
import LoadingComponent from "../components/LoadingComponent.vue";

export default {
	layout: "simple",
	components: {
		LoadingComponent,
	},
	data() {
		return {
			loading: true,
		};
	},
	async created() {
		if (process.client) {
			if (this.$auth.loggedIn) {
				await this.fetchTranslations({
					language_id: this.$auth.user.translated_language_id,
				});
			} else {
				await this.fetchTranslations({
					baseLanguage: true,
				});
			}
			this.loading = false;
		}
	},

	props: {
		error: {
			type: Object,
			default: () => {},
		},
	},
	methods: {
		back() {
			this.$router.back();
		},
		reload() {
			location.reload();
		},
		...mapActions({
			fetchTranslations: "translations/fetchTranslations",
		}),
	},
};
</script>
<style>
.step-counter {
	height: 30px;
	width: 30px;
	font-size: 14px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: var(--v-primary-lighten4);
	color: var(--v-primary-base);
	margin: 0 auto;
	border-radius: 50%;
}
.error-page {
	max-width: 600px;
}
</style>
