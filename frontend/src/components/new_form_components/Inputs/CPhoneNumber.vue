<template>
	<InputCard pbNone v-bind="$attrs" v-on="$listeners">
		<template slot-scope="{ attrs, listeners }">
			<v-text-field
				:class="`form-custom-number1  ${small ? 'small' : ''}`"
				background-color="var(--new-input-bg)"
				outlined
				:rounded="rounded"
				v-bind="attrs"
				v-on="listeners"
			>
				<template v-slot:prepend-inner>
					<span>
						<v-select
							v-model="selectedCountry"
							:value="defaultCountryName"
							class="ma-0 pa-0 phone-number-input-select-option"
							:menu-props="{
								bottom: true,
								offsetY: true,
								nudgeTop: -10,
							}"
							hide-details
							:items="countries"
							item-value="name"
							item-text="name"
						>
							<template v-slot:[`item`]="{ item }">
								<div class="d-flex align-center">
									<div class="me-1">
										<FlagIcon :flag="item.iso2.toLowerCase()" />
									</div>
									<div>
										<div class="text-center">
											{{ item.name }} ({{ item.phone_code }})
										</div>
									</div>
								</div>
							</template>

							<template v-slot:[`selection`]="{ item }">
								<div class="d-flex align-center justify-content-between">
									<div class="me-1 mt-n1">
										<FlagIcon :flag="item.iso2.toLowerCase()" />
									</div>
									<div class="text-center" style="width: 62px !important">
										{{ item.phone_code }}
									</div>
								</div>
								<v-divider vertical class="grey lighten-2"></v-divider>
							</template>
						</v-select>
					</span>
					<v-icon dark size="25"> mdi-cellphone </v-icon>
				</template>
			</v-text-field>
		</template>
	</InputCard>
</template>
<script>
import InputCard from "../components/InputCard.vue";

import FlagIcon from "~/components/common/FlagIcon.vue";
export default {
	props: {
		small: Boolean,

		defaultCountryName: {
			type: String,
			default: "United Arab Emirates",
		},

		rounded: {
			type: Boolean,
			default: true,
		},
	},
	methods: {
		add() {
			this.$emit("add");
		},
	},
	components: {
		InputCard,

		FlagIcon,
	},
	data() {
		return {
			selectedCountry: this.defaultCountryName,
			isFetchingCountries: false,
			countries: [
				{
					id: "0013f645-6bc1-4169-88e2-f05b7cc5e505",
					name: "Iraq",
					iso2: "IQ",
					phone_code: "964",
				},
				{
					id: "00d10c94-8dfc-4ce7-897c-2f6016eafd25",
					name: "Ecuador",
					iso2: "EC",
					phone_code: "593",
				},
				{
					id: "032383f2-2a75-4ad1-916e-de1fe11252b5",
					name: "Bahamas The",
					iso2: "BS",
					phone_code: "+1-242",
				},
				{
					id: "047b6a3f-dbf0-4fa1-94af-4996018b2bdf",
					name: "Botswana",
					iso2: "BW",
					phone_code: "267",
				},
				{
					id: "06837942-164d-40e9-9994-c4d0003d09ae",
					name: "Angola",
					iso2: "AO",
					phone_code: "244",
				},
				{
					id: "07b910eb-9655-4cf3-b7d0-3eebb463a582",
					name: "Wallis And Futuna Islands",
					iso2: "WF",
					phone_code: "681",
				},
				{
					id: "09af4077-ec77-478b-9c98-6e31aaa34177",
					name: "Chad",
					iso2: "TD",
					phone_code: "235",
				},
				{
					id: "0a4d2352-4c1c-4c99-971b-687491abf60e",
					name: "Austria",
					iso2: "AT",
					phone_code: "43",
				},
				{
					id: "0a9356da-8350-4c63-b528-6975b8a39c1c",
					name: "Argentina",
					iso2: "AR",
					phone_code: "54",
				},
				{
					id: "0c60e3f2-da65-417a-92c5-31220649c9dc",
					name: "Guam",
					iso2: "GU",
					phone_code: "+1-671",
				},
				{
					id: "0ca88709-260a-4e76-8a7a-05550fe6a924",
					name: "Vanuatu",
					iso2: "VU",
					phone_code: "678",
				},
				{
					id: "0d988d39-7d50-41bf-bd55-48707f61ea3f",
					name: "Brazil",
					iso2: "BR",
					phone_code: "55",
				},
				{
					id: "0e0be9ce-31de-4a85-abbe-db7a7a5dca1a",
					name: "Tanzania",
					iso2: "TZ",
					phone_code: "255",
				},
				{
					id: "0f380c43-0b23-42f4-bd84-771d198cd52a",
					name: "Palestinian Territory Occupied",
					iso2: "PS",
					phone_code: "970",
				},
				{
					id: "12dd2a26-a456-4f1c-b216-13357d222a14",
					name: "Sweden",
					iso2: "SE",
					phone_code: "46",
				},
				{
					id: "139a44c0-93b3-4814-b436-e4e70ba7b690",
					name: "Macedonia",
					iso2: "MK",
					phone_code: "389",
				},
				{
					id: "1491213b-5fe5-4859-98e0-2617942f8a80",
					name: "Virgin Islands (US)",
					iso2: "VI",
					phone_code: "+1-340",
				},
				{
					id: "179a0057-d5fb-4ffc-a2d7-bd870e290795",
					name: "Tuvalu",
					iso2: "TV",
					phone_code: "688",
				},
				{
					id: "17e86c8b-022e-47c3-b900-5ede74cbff73",
					name: "Bermuda",
					iso2: "BM",
					phone_code: "+1-441",
				},
				{
					id: "184dd2f8-6a7c-4654-91ea-78a65739d520",
					name: "Russia",
					iso2: "RU",
					phone_code: "7",
				},
				{
					id: "1897a6e7-f4a9-46ff-a029-c39b61adf9af",
					name: "Mexico",
					iso2: "MX",
					phone_code: "52",
				},
				{
					id: "1923dc2e-9503-4a8c-ae84-7df2dc57c412",
					name: "Kiribati",
					iso2: "KI",
					phone_code: "686",
				},
				{
					id: "1b95e9e1-31cd-43eb-9c53-2a7bae61f634",
					name: "Solomon Islands",
					iso2: "SB",
					phone_code: "677",
				},
				{
					id: "1bdcaea3-d2e0-409e-9a67-ace23dbb773c",
					name: "Armenia",
					iso2: "AM",
					phone_code: "374",
				},
				{
					id: "1c728f97-849a-4a2d-8cd7-6944532b21c5",
					name: "Indonesia",
					iso2: "ID",
					phone_code: "62",
				},
				{
					id: "1ca00414-6ab0-4ea0-967b-f47b5d4ae9e9",
					name: "United Kingdom",
					iso2: "GB",
					phone_code: "44",
				},
				{
					id: "1d7c6e9a-448a-480b-a37e-3a61c3895233",
					name: "Uruguay",
					iso2: "UY",
					phone_code: "598",
				},
				{
					id: "1fa6cd22-fbec-4ca0-b7f3-0b137746373a",
					name: "Hong Kong S.A.R.",
					iso2: "HK",
					phone_code: "852",
				},
				{
					id: "2059430d-eed8-4b75-a3dd-0ea0aa97578b",
					name: "Lesotho",
					iso2: "LS",
					phone_code: "266",
				},
				{
					id: "21ab52ec-3723-48e0-9297-e86f97fc8a95",
					name: "South Africa",
					iso2: "ZA",
					phone_code: "27",
				},
				{
					id: "231eddeb-19c5-4a99-bea4-1db96ea47ef0",
					name: "Tunisia",
					iso2: "TN",
					phone_code: "216",
				},
				{
					id: "23c88184-734e-4fc9-b2cc-10e35f1f471d",
					name: "Mozambique",
					iso2: "MZ",
					phone_code: "258",
				},
				{
					id: "251a90cb-a226-405b-8acf-670de4c6ba77",
					name: "United States Minor Outlying Islands",
					iso2: "UM",
					phone_code: "1",
				},
				{
					id: "25595cda-5d4e-4757-b669-d84ef34b2e3d",
					name: "Turks And Caicos Islands",
					iso2: "TC",
					phone_code: "+1-649",
				},
				{
					id: "255c0ec5-88db-47ef-9096-01b30d4310aa",
					name: "Malta",
					iso2: "MT",
					phone_code: "356",
				},
				{
					id: "25b231f7-9952-4114-89d5-b5c3b59c009a",
					name: "Slovenia",
					iso2: "SI",
					phone_code: "386",
				},
				{
					id: "270d02e4-90b4-48e6-8cb9-42e92d6dc4e8",
					name: "Kazakhstan",
					iso2: "KZ",
					phone_code: "7",
				},
				{
					id: "271169ae-ee20-4784-9918-a359c875dcc9",
					name: "Qatar",
					iso2: "QA",
					phone_code: "974",
				},
				{
					id: "27c293d3-a9f4-444f-89db-4cb8805dea72",
					name: "Madagascar",
					iso2: "MG",
					phone_code: "261",
				},
				{
					id: "28780922-f9c3-4384-ae6c-536fed09c417",
					name: "Sint Maarten (Dutch part)",
					iso2: "SX",
					phone_code: "1721",
				},
				{
					id: "28a77412-de0b-4a91-a521-75e7db68896e",
					name: "Bouvet Island",
					iso2: "BV",
					phone_code: "0055",
				},
				{
					id: "2acb5ac2-1b90-4ecb-91bf-9bfc333a78ab",
					name: "Samoa",
					iso2: "WS",
					phone_code: "685",
				},
				{
					id: "2cad52b0-8e32-41f6-b0e6-16dbbf2b3f68",
					name: "Antarctica",
					iso2: "AQ",
					phone_code: "672",
				},
				{
					id: "2d9f29da-3103-4351-8b13-b08470f36053",
					name: "American Samoa",
					iso2: "AS",
					phone_code: "+1-684",
				},
				{
					id: "2deaea8a-bf86-4b1e-bc85-d78c200f10ea",
					name: "Guernsey and Alderney",
					iso2: "GG",
					phone_code: "+44-1481",
				},
				{
					id: "2e9e89ea-6ddd-4b2a-b9a6-8fc440b11e40",
					name: "Finland",
					iso2: "FI",
					phone_code: "358",
				},
				{
					id: "2f212763-3dd4-44f6-bf23-08d6a285f2bb",
					name: "Greenland",
					iso2: "GL",
					phone_code: "299",
				},
				{
					id: "2f389cf6-b52c-40e4-9cf3-8a07096af79b",
					name: "Jamaica",
					iso2: "JM",
					phone_code: "+1-876",
				},
				{
					id: "300cfe8a-ae77-46de-9c00-846adad95608",
					name: "Honduras",
					iso2: "HN",
					phone_code: "504",
				},
				{
					id: "30ef9fcd-4a22-4638-8070-2f3f3aad4709",
					name: "Croatia",
					iso2: "HR",
					phone_code: "385",
				},
				{
					id: "31282392-1b55-446d-9c26-83a77c35112e",
					name: "Moldova",
					iso2: "MD",
					phone_code: "373",
				},
				{
					id: "32561530-f13d-400a-bd87-29d89ca36bb0",
					name: "Hungary",
					iso2: "HU",
					phone_code: "36",
				},
				{
					id: "32c73cac-c825-4a65-b121-7be2ee6ed285",
					name: "Belarus",
					iso2: "BY",
					phone_code: "375",
				},
				{
					id: "3468036c-c158-43b5-a46a-fc21b8d5223b",
					name: "Nauru",
					iso2: "NR",
					phone_code: "674",
				},
				{
					id: "353d2664-bc66-49c5-af64-93179ee20cc4",
					name: "Lebanon",
					iso2: "LB",
					phone_code: "961",
				},
				{
					id: "360f95da-97bd-4838-a4a1-97864461f497",
					name: "India",
					iso2: "IN",
					phone_code: "91",
				},
				{
					id: "37def630-0991-47b9-9871-c51712b34251",
					name: "Gambia The",
					iso2: "GM",
					phone_code: "220",
				},
				{
					id: "386c6646-a623-4dae-849c-a546802824ff",
					name: "Grenada",
					iso2: "GD",
					phone_code: "+1-473",
				},
				{
					id: "3956d978-45e5-494d-af76-4252f2dd5ad8",
					name: "Iran",
					iso2: "IR",
					phone_code: "98",
				},
				{
					id: "3a176e91-6ab4-4e65-afcc-7bd796f1014d",
					name: "Puerto Rico",
					iso2: "PR",
					phone_code: "+1-787 and 1-939",
				},
				{
					id: "3af75e9c-6e3b-45e6-ba73-468c6c18affe",
					name: "Lithuania",
					iso2: "LT",
					phone_code: "370",
				},
				{
					id: "3b4cf168-d9ec-422a-a204-3e5303f91008",
					name: "Equatorial Guinea",
					iso2: "GQ",
					phone_code: "240",
				},
				{
					id: "3c0d44c0-6484-4f1e-b444-04a666979e8d",
					name: "South Georgia",
					iso2: "GS",
					phone_code: "500",
				},
				{
					id: "3c2eed9d-be28-45d1-aded-db6dd9f64ed9",
					name: "Portugal",
					iso2: "PT",
					phone_code: "351",
				},
				{
					id: "3d8e61b8-6ba9-48d2-a181-c31fac0729fc",
					name: "Sierra Leone",
					iso2: "SL",
					phone_code: "232",
				},
				{
					id: "3f4547fc-c3f3-49c5-8a3d-0ee7b5db9612",
					name: "Saint-Barthelemy",
					iso2: "BL",
					phone_code: "590",
				},
				{
					id: "421b1c3c-0b88-427f-8088-89c097bb9239",
					name: "Aland Islands",
					iso2: "AX",
					phone_code: "+358-18",
				},
				{
					id: "45c2c4a5-1983-473b-ae9b-aa540b7a92a0",
					name: "Comoros",
					iso2: "KM",
					phone_code: "269",
				},
				{
					id: "461e2387-ce21-4316-af6f-3ef64e477723",
					name: "Ghana",
					iso2: "GH",
					phone_code: "233",
				},
				{
					id: "47a8dd79-d894-47b1-b2a1-7d949df8ada1",
					name: "East Timor",
					iso2: "TL",
					phone_code: "670",
				},
				{
					id: "47ac78a4-c1fa-45fc-9afc-8dd1cf2ab057",
					name: "Western Sahara",
					iso2: "EH",
					phone_code: "212",
				},
				{
					id: "481c1fc3-8e3a-4032-aaa6-db3c155891a6",
					name: "Anguilla",
					iso2: "AI",
					phone_code: "+1-264",
				},
				{
					id: "48c2b9d4-1922-4db1-8eed-1ea44d11893f",
					name: "Vietnam",
					iso2: "VN",
					phone_code: "84",
				},
				{
					id: "4ce5609b-b1ff-4708-afad-76b0927fbadf",
					name: "Oman",
					iso2: "OM",
					phone_code: "968",
				},
				{
					id: "4da56f04-9773-48a6-9fc1-43eca7c3bcf3",
					name: "Kuwait",
					iso2: "KW",
					phone_code: "965",
				},
				{
					id: "4ddd3a90-6d88-4d6d-bf26-1918a03e29a0",
					name: "Bahrain",
					iso2: "BH",
					phone_code: "973",
				},
				{
					id: "501e1868-2deb-4ecc-b16b-63facfb88abb",
					name: "Cape Verde",
					iso2: "CV",
					phone_code: "238",
				},
				{
					id: "51039a6b-a9e2-4f50-a8c3-ab332c9d9aff",
					name: "French Guiana",
					iso2: "GF",
					phone_code: "594",
				},
				{
					id: "51180355-1246-4b03-91d0-7b99123ea219",
					name: "Canada",
					iso2: "CA",
					phone_code: "1",
				},
				{
					id: "514c8844-c93b-4349-bcad-9bebfcd281b9",
					name: "Switzerland",
					iso2: "CH",
					phone_code: "41",
				},
				{
					id: "5173c265-444b-4699-a4be-574bf1af6378",
					name: "Northern Mariana Islands",
					iso2: "MP",
					phone_code: "+1-670",
				},
				{
					id: "524d1e6b-6174-4de3-9cd1-04c774100da1",
					name: "Jersey",
					iso2: "JE",
					phone_code: "+44-1534",
				},
				{
					id: "531c062b-58c7-4aa9-b960-2685e164990c",
					name: "Faroe Islands",
					iso2: "FO",
					phone_code: "298",
				},
				{
					id: "53a2e574-f2c4-468d-b171-89a1a95cb2aa",
					name: "Antigua And Barbuda",
					iso2: "AG",
					phone_code: "+1-268",
				},
				{
					id: "53b92ad8-0da5-4fb1-b4ab-d158b303a112",
					name: "Belize",
					iso2: "BZ",
					phone_code: "501",
				},
				{
					id: "54be0a2b-0483-4855-9182-4fa9f2c4b6a7",
					name: "France",
					iso2: "FR",
					phone_code: "33",
				},
				{
					id: "55f0f737-aa0d-4958-8bb8-889b871acc91",
					name: "Seychelles",
					iso2: "SC",
					phone_code: "248",
				},
				{
					id: "56b4f3e6-deb8-4382-944c-663557856b4d",
					name: "Guadeloupe",
					iso2: "GP",
					phone_code: "590",
				},
				{
					id: "56d37d12-7f1f-4ed1-9403-0e250a9f74dd",
					name: "Palau",
					iso2: "PW",
					phone_code: "680",
				},
				{
					id: "58da6ab0-859f-4175-adc0-d1fb11911ac4",
					name: "Bulgaria",
					iso2: "BG",
					phone_code: "359",
				},
				{
					id: "5915331e-f3c6-41a9-a25b-91aa45fbb3ef",
					name: "Peru",
					iso2: "PE",
					phone_code: "51",
				},
				{
					id: "593c8761-26a7-42a4-bd58-16c1a3d34d31",
					name: "Netherlands",
					iso2: "NL",
					phone_code: "31",
				},
				{
					id: "5aca1149-70d2-4586-b0c4-6c247b57bd46",
					name: "Eritrea",
					iso2: "ER",
					phone_code: "291",
				},
				{
					id: "5be41491-cfcf-46d9-a8f7-f21bc58605be",
					name: "Germany",
					iso2: "DE",
					phone_code: "49",
				},
				{
					id: "5c2a5c6b-538d-4aa7-97fa-256a2882cedc",
					name: "Suriname",
					iso2: "SR",
					phone_code: "597",
				},
				{
					id: "5cad49af-4106-4f0d-bdc9-e2f5dc38c8bd",
					name: "Slovakia",
					iso2: "SK",
					phone_code: "421",
				},
				{
					id: "5ee8e18a-a43d-4e18-ae9e-1e7d0eeee365",
					name: "Christmas Island",
					iso2: "CX",
					phone_code: "61",
				},
				{
					id: "5f9a38be-ae0a-4d82-a3b6-26ff65893031",
					name: "Virgin Islands (British)",
					iso2: "VG",
					phone_code: "+1-284",
				},
				{
					id: "6137c228-843a-4301-848c-b0fac9b9b070",
					name: "Svalbard And Jan Mayen Islands",
					iso2: "SJ",
					phone_code: "47",
				},
				{
					id: "618a4fdd-23a9-4289-ac70-0a796d63d449",
					name: "Japan",
					iso2: "JP",
					phone_code: "81",
				},
				{
					id: "62608aba-f904-4c29-95c6-944c833a7d58",
					name: "Ethiopia",
					iso2: "ET",
					phone_code: "251",
				},
				{
					id: "64a6406a-0679-4eb4-a9ea-93b5f9e07d57",
					name: "Turkmenistan",
					iso2: "TM",
					phone_code: "993",
				},
				{
					id: "657aff38-e3b5-42b3-a16d-33f615079a08",
					name: "Niue",
					iso2: "NU",
					phone_code: "683",
				},
				{
					id: "65ccae57-2860-4c98-89f4-83fb9584530f",
					name: "Kosovo",
					iso2: "XK",
					phone_code: "383",
				},
				{
					id: "688abd1a-baf7-4617-bd87-45853c11eaee",
					name: "Uganda",
					iso2: "UG",
					phone_code: "256",
				},
				{
					id: "68d72c84-02f9-4499-8f7e-de8dd1f7473d",
					name: "Saint-Martin (French part)",
					iso2: "MF",
					phone_code: "590",
				},
				{
					id: "69685a46-9599-401f-b92d-74399e56a64f",
					name: "Somalia",
					iso2: "SO",
					phone_code: "252",
				},
				{
					id: "6a477550-c81a-45f2-a9e1-ad3af711ac19",
					name: "New Zealand",
					iso2: "NZ",
					phone_code: "64",
				},
				{
					id: "6c20b4df-430d-4c64-9543-35632f5ca2ad",
					name: "Sri Lanka",
					iso2: "LK",
					phone_code: "94",
				},
				{
					id: "6cc0e078-73b8-4a36-9c6e-2b3309a4300c",
					name: "Senegal",
					iso2: "SN",
					phone_code: "221",
				},
				{
					id: "6da5f876-8b64-4cf4-a1f3-3e8722f786fa",
					name: "Albania",
					iso2: "AL",
					phone_code: "355",
				},
				{
					id: "6db09991-c69f-46db-b85d-8db7cfbb990d",
					name: "Azerbaijan",
					iso2: "AZ",
					phone_code: "994",
				},
				{
					id: "6ddac25a-a823-47db-b859-85ea1dea60c9",
					name: "French Southern Territories",
					iso2: "TF",
					phone_code: "262",
				},
				{
					id: "70e05459-77b7-4e66-8758-b7db8775313c",
					name: "Cyprus",
					iso2: "CY",
					phone_code: "357",
				},
				{
					id: "729a7244-9e13-4897-824e-163f191eec0e",
					name: "Philippines",
					iso2: "PH",
					phone_code: "63",
				},
				{
					id: "7327bfe9-b478-48f9-a892-5f471f38c8ed",
					name: "Guyana",
					iso2: "GY",
					phone_code: "592",
				},
				{
					id: "734f48f2-0382-4904-860b-67bdac856806",
					name: "Marshall Islands",
					iso2: "MH",
					phone_code: "692",
				},
				{
					id: "738e621f-0dc4-4ca0-8f09-f638bde8795f",
					name: "Falkland Islands",
					iso2: "FK",
					phone_code: "500",
				},
				{
					id: "74fe70bc-8829-48a6-9c3e-d38411e5e41b",
					name: "Djibouti",
					iso2: "DJ",
					phone_code: "253",
				},
				{
					id: "760d8b81-9e93-4eee-a149-086b0528bef4",
					name: "Montenegro",
					iso2: "ME",
					phone_code: "382",
				},
				{
					id: "7615e014-9c98-4ebd-a935-e7bf85e63080",
					name: "Tokelau",
					iso2: "TK",
					phone_code: "690",
				},
				{
					id: "763f0496-2b37-451e-8fb3-0f715272a575",
					name: "Liberia",
					iso2: "LR",
					phone_code: "231",
				},
				{
					id: "76757af8-224b-4954-adfe-8bc355c2caa9",
					name: "Togo",
					iso2: "TG",
					phone_code: "228",
				},
				{
					id: "77860dc1-7b81-4c06-b315-844e62639993",
					name: "Yemen",
					iso2: "YE",
					phone_code: "967",
				},
				{
					id: "79c2d99e-5e3b-4893-859e-ec8a56ffaf16",
					name: "Nepal",
					iso2: "NP",
					phone_code: "977",
				},
				{
					id: "7a93e48d-25ec-426d-89a3-de47cf223926",
					name: "Saint Helena",
					iso2: "SH",
					phone_code: "290",
				},
				{
					id: "7b2e4cea-7c38-4657-b7db-2f922f0be738",
					name: "Libya",
					iso2: "LY",
					phone_code: "218",
				},
				{
					id: "7d462f32-12a9-48bc-a7c8-e37496c5889c",
					name: "Greece",
					iso2: "GR",
					phone_code: "30",
				},
				{
					id: "7d9d657a-787b-492b-8210-a156ce8ed46a",
					name: "Kenya",
					iso2: "KE",
					phone_code: "254",
				},
				{
					id: "7dcad706-b448-4ce0-9bd7-abd7a079aee1",
					name: "Colombia",
					iso2: "CO",
					phone_code: "57",
				},
				{
					id: "8282a07c-92c8-4600-b5df-a51f0e5900c9",
					name: "Estonia",
					iso2: "EE",
					phone_code: "372",
				},
				{
					id: "83163030-cc3e-44a7-bd70-3d87b86788fe",
					name: "Turkey",
					iso2: "TR",
					phone_code: "90",
				},
				{
					id: "8404acc0-08fd-4b36-bb3e-e731c0b4e779",
					name: "Dominican Republic",
					iso2: "DO",
					phone_code: "+1-809 and 1-829",
				},
				{
					id: "8432417a-c320-43ec-886c-254ed0def654",
					name: "Algeria",
					iso2: "DZ",
					phone_code: "213",
				},
				{
					id: "847e0522-4f03-42cb-8c96-62b62c053577",
					name: "Guinea",
					iso2: "GN",
					phone_code: "224",
				},
				{
					id: "849fda4a-d9f0-435b-a581-45c48493ae3f",
					name: "Swaziland",
					iso2: "SZ",
					phone_code: "268",
				},
				{
					id: "854b8eef-c42c-4495-aa42-136741c8e797",
					name: "Liechtenstein",
					iso2: "LI",
					phone_code: "423",
				},
				{
					id: "86f05a1a-a82d-4135-8361-435dddf02383",
					name: "Mongolia",
					iso2: "MN",
					phone_code: "976",
				},
				{
					id: "87ce64e1-ce78-4153-8e1c-9fb0c42e451a",
					name: "Congo",
					iso2: "CG",
					phone_code: "242",
				},
				{
					id: "89099615-6af4-4f0b-bbeb-07b9d602ed41",
					name: "Fiji Islands",
					iso2: "FJ",
					phone_code: "679",
				},
				{
					id: "894a46f2-24a8-465c-a4b9-c482ce515432",
					name: "Micronesia",
					iso2: "FM",
					phone_code: "691",
				},
				{
					id: "8a007555-e411-457b-9618-b0bb65942e87",
					name: "China",
					iso2: "CN",
					phone_code: "86",
				},
				{
					id: "8b046f31-ef7c-4765-9dba-2b3b468652e0",
					name: "Cameroon",
					iso2: "CM",
					phone_code: "237",
				},
				{
					id: "8ba33b89-7941-4ce2-8451-5be3bbe88614",
					name: "South Korea",
					iso2: "KR",
					phone_code: "82",
				},
				{
					id: "8c8ce3c9-fb98-46be-bcbf-c033c8dafdd3",
					name: "United States",
					iso2: "US",
					phone_code: "1",
				},
				{
					id: "90ca5482-3c9e-4db4-96ee-d3003c0a8992",
					name: "Sao Tome and Principe",
					iso2: "ST",
					phone_code: "239",
				},
				{
					id: "924bccb8-f972-40ec-91f6-447e599c1681",
					name: "Australia",
					iso2: "AU",
					phone_code: "61",
				},
				{
					id: "93941ea0-15e4-40d6-8972-383a35c5f883",
					name: "Rwanda",
					iso2: "RW",
					phone_code: "250",
				},
				{
					id: "940a6ea5-b5c8-4949-97db-b87907056afe",
					name: "Norway",
					iso2: "NO",
					phone_code: "47",
				},
				{
					id: "942e7fef-68cc-43ce-9fdd-f6b3ee48b7fa",
					name: "Guatemala",
					iso2: "GT",
					phone_code: "502",
				},
				{
					id: "9567fdde-137e-4114-8217-7fe93550166a",
					name: "Reunion",
					iso2: "RE",
					phone_code: "262",
				},
				{
					id: "95edab6f-6efd-47ae-ad44-225bf17d8925",
					name: "Tonga",
					iso2: "TO",
					phone_code: "676",
				},
				{
					id: "963d10d1-73cb-4fb2-9b3b-9f09f44d3472",
					name: "Pitcairn Island",
					iso2: "PN",
					phone_code: "870",
				},
				{
					id: "96f0b153-9afd-4cab-b12d-a431a365b937",
					name: "Cook Islands",
					iso2: "CK",
					phone_code: "682",
				},
				{
					id: "9c2e2c58-5c11-46bc-bc44-ee589c9c331b",
					name: "Pakistan",
					iso2: "PK",
					phone_code: "92",
				},
				{
					id: "9e00c472-37f3-4f50-9b9a-7382ed93ed0c",
					name: "Syria",
					iso2: "SY",
					phone_code: "963",
				},
				{
					id: "9e1e712a-4fe9-466d-90ce-14ca5ed48d36",
					name: "Niger",
					iso2: "NE",
					phone_code: "227",
				},
				{
					id: "9e61e9fb-1b18-4e9b-82d9-50f109e45c49",
					name: "Bangladesh",
					iso2: "BD",
					phone_code: "880",
				},
				{
					id: "9e6e7da2-b283-4710-a34e-8472b81d07e1",
					name: "Namibia",
					iso2: "NA",
					phone_code: "264",
				},
				{
					id: "a0013a2a-2a5f-4c55-84a0-ab386fd1dc6a",
					name: "Papua new Guinea",
					iso2: "PG",
					phone_code: "675",
				},
				{
					id: "a0b34825-b969-4741-ba1f-a0a4441c5c9e",
					name: "Cote D'Ivoire (Ivory Coast)",
					iso2: "CI",
					phone_code: "225",
				},
				{
					id: "a2c0e3cf-c222-463b-bac1-ced01d0eabc9",
					name: "Sudan",
					iso2: "SD",
					phone_code: "249",
				},
				{
					id: "a2ced8a7-a31b-4106-933f-378a8804d37b",
					name: "Zambia",
					iso2: "ZM",
					phone_code: "260",
				},
				{
					id: "a2d2a7aa-570f-4435-b9bf-6d94dd3651fa",
					name: "Denmark",
					iso2: "DK",
					phone_code: "45",
				},
				{
					id: "a3653501-64d7-4134-bee7-3255c6f335c3",
					name: "Chile",
					iso2: "CL",
					phone_code: "56",
				},
				{
					id: "a437462e-d4bd-4f9e-8a56-4c1c2942b36a",
					name: "Nicaragua",
					iso2: "NI",
					phone_code: "505",
				},
				{
					id: "a4d07ddd-d4f8-424f-9a7d-90b7011a53bc",
					name: "British Indian Ocean Territory",
					iso2: "IO",
					phone_code: "246",
				},
				{
					id: "a4d32268-4901-4da3-81be-a2f1c6718d5e",
					name: "Man (Isle of)",
					iso2: "IM",
					phone_code: "+44-1624",
				},
				{
					id: "a74c1ff6-981c-4fef-b9c9-b098b798b3db",
					name: "Central African Republic",
					iso2: "CF",
					phone_code: "236",
				},
				{
					id: "a9ff85a6-658e-49f3-8d05-5a88936fc351",
					name: "Mauritius",
					iso2: "MU",
					phone_code: "230",
				},
				{
					id: "ab88e9fb-3240-4354-a9fd-5f35e2fc3132",
					name: "United Arab Emirates",
					iso2: "AE",
					phone_code: "971",
				},
				{
					id: "ab98e78b-a9c6-48c8-9f69-ae98038914d2",
					name: "Burundi",
					iso2: "BI",
					phone_code: "257",
				},
				{
					id: "adeeaf2a-9ff5-4e34-96c3-48580e75ddbd",
					name: "Czech Republic",
					iso2: "CZ",
					phone_code: "420",
				},
				{
					id: "af67bd4f-5140-4b4e-a890-afeb67cb3a60",
					name: "Zimbabwe",
					iso2: "ZW",
					phone_code: "263",
				},
				{
					id: "b8b2d945-cb8e-4083-baff-1e9647fcb9a3",
					name: "Panama",
					iso2: "PA",
					phone_code: "507",
				},
				{
					id: "b8c19513-c5af-450c-a21a-0766cc276d99",
					name: "Saint Lucia",
					iso2: "LC",
					phone_code: "+1-758",
				},
				{
					id: "baa65584-8e6e-4b6a-a8f3-78f0a28796f6",
					name: "Myanmar",
					iso2: "MM",
					phone_code: "95",
				},
				{
					id: "bb1fc7e6-b93d-4e63-b9f4-fb2259f5f972",
					name: "Israel",
					iso2: "IL",
					phone_code: "972",
				},
				{
					id: "bba5ceb0-6d8f-47af-a44e-20b42f2ac4fc",
					name: "Malawi",
					iso2: "MW",
					phone_code: "265",
				},
				{
					id: "bbb390f7-94b6-4736-9ef6-d4337c1b67bf",
					name: "Bolivia",
					iso2: "BO",
					phone_code: "591",
				},
				{
					id: "bc8cdfa2-5523-4fd5-9ad8-1859ae50c370",
					name: "Haiti",
					iso2: "HT",
					phone_code: "509",
				},
				{
					id: "bd8df794-814a-44e6-9a24-318ebd32b162",
					name: "Taiwan",
					iso2: "TW",
					phone_code: "886",
				},
				{
					id: "bdfe830f-3c79-4f03-8e21-f102f020c7e4",
					name: "Burkina Faso",
					iso2: "BF",
					phone_code: "226",
				},
				{
					id: "be6ec393-a5e6-40a5-b78f-58adffe6a2af",
					name: "Laos",
					iso2: "LA",
					phone_code: "856",
				},
				{
					id: "c0082b58-c39a-49b2-a1e2-474e621f6a26",
					name: "South Sudan",
					iso2: "SS",
					phone_code: "211",
				},
				{
					id: "c58d144d-0721-4f03-b087-e3a459eb6f2f",
					name: "Democratic Republic of the Congo",
					iso2: "CD",
					phone_code: "243",
				},
				{
					id: "c59d7caf-c45f-4006-a4d6-513690b2ca7e",
					name: "Paraguay",
					iso2: "PY",
					phone_code: "595",
				},
				{
					id: "c638a3fb-ef1f-4ab6-b4c7-bed0512b1b7b",
					name: "French Polynesia",
					iso2: "PF",
					phone_code: "689",
				},
				{
					id: "c63b6686-7890-4ff9-8d63-f26d69ad4b02",
					name: "Cambodia",
					iso2: "KH",
					phone_code: "855",
				},
				{
					id: "c68b62fc-f759-4cbb-9872-ec4053f82148",
					name: "Bonaire, Sint Eustatius and Saba",
					iso2: "BQ",
					phone_code: "599",
				},
				{
					id: "c70e6134-4d72-4a70-93cb-6729fcac9dbc",
					name: "Mayotte",
					iso2: "YT",
					phone_code: "262",
				},
				{
					id: "c8410f95-586f-431a-9f76-98ad1809f4ff",
					name: "Afghanistan",
					iso2: "AF",
					phone_code: "93",
				},
				{
					id: "c8c10c9c-e963-4b12-b36e-05f8cb4272e6",
					name: "Martinique",
					iso2: "MQ",
					phone_code: "596",
				},
				{
					id: "c903e099-00cc-4882-a148-efd0de825487",
					name: "Gibraltar",
					iso2: "GI",
					phone_code: "350",
				},
				{
					id: "c9d4c7de-cf1b-4b41-b27e-4306b97d9b28",
					name: "Vatican City State (Holy See)",
					iso2: "VA",
					phone_code: "379",
				},
				{
					id: "cd1ab756-7ba3-40d3-b98d-bf048f4c5037",
					name: "Poland",
					iso2: "PL",
					phone_code: "48",
				},
				{
					id: "ce1b0338-eac8-4fb3-a0af-7f554dd20cb2",
					name: "Montserrat",
					iso2: "MS",
					phone_code: "+1-664",
				},
				{
					id: "cee6a992-b093-4c45-9af9-88d674d6b7ac",
					name: "Malaysia",
					iso2: "MY",
					phone_code: "60",
				},
				{
					id: "d0b787bf-6f91-4598-a6ab-11dccf2de39a",
					name: "San Marino",
					iso2: "SM",
					phone_code: "378",
				},
				{
					id: "d1751ef0-d672-49b3-a685-1732c2c2498c",
					name: "Morocco",
					iso2: "MA",
					phone_code: "212",
				},
				{
					id: "d2b83ad7-aa61-4317-9498-4e569cb57224",
					name: "Italy",
					iso2: "IT",
					phone_code: "39",
				},
				{
					id: "d2e59197-dc4a-4add-ab6a-1ae18309a10d",
					name: "Cayman Islands",
					iso2: "KY",
					phone_code: "+1-345",
				},
				{
					id: "d3fe7141-2dc9-42ea-8568-8fc1a08297f7",
					name: "Guinea-Bissau",
					iso2: "GW",
					phone_code: "245",
				},
				{
					id: "d625e399-28b7-4fe7-a2f2-d4a9491a8b4b",
					name: "Kyrgyzstan",
					iso2: "KG",
					phone_code: "996",
				},
				{
					id: "d64e5195-3418-4447-a8d8-048528172148",
					name: "Gabon",
					iso2: "GA",
					phone_code: "241",
				},
				{
					id: "d6a968d8-9aaf-46f4-9672-6f6d5b80ad83",
					name: "Cocos (Keeling) Islands",
					iso2: "CC",
					phone_code: "61",
				},
				{
					id: "d6be06a8-726a-4a6a-969b-d6ce252015d6",
					name: "Nigeria",
					iso2: "NG",
					phone_code: "234",
				},
				{
					id: "d8603552-f3b4-43e0-9383-53feb100a34e",
					name: "Georgia",
					iso2: "GE",
					phone_code: "995",
				},
				{
					id: "d899da2b-6fd4-4cc1-91b6-1229391379f0",
					name: "Luxembourg",
					iso2: "LU",
					phone_code: "352",
				},
				{
					id: "d8a394c1-8de6-4163-ac83-2bad1957cc04",
					name: "Saudi Arabia",
					iso2: "SA",
					phone_code: "966",
				},
				{
					id: "d8d49277-6ec4-4a47-bee8-15f2bf1c68c7",
					name: "Macau S.A.R.",
					iso2: "MO",
					phone_code: "853",
				},
				{
					id: "d9ab7408-646f-4b08-8b3d-a9a57030e48f",
					name: "Latvia",
					iso2: "LV",
					phone_code: "371",
				},
				{
					id: "dafb2090-d0ef-4a58-9a57-3329e1da7ef1",
					name: "Barbados",
					iso2: "BB",
					phone_code: "+1-246",
				},
				{
					id: "db27069a-92aa-4106-8f30-d8a4a89659f5",
					name: "Saint Vincent And The Grenadines",
					iso2: "VC",
					phone_code: "+1-784",
				},
				{
					id: "db47d6d1-250a-46b2-93ed-630986847608",
					name: "Bosnia and Herzegovina",
					iso2: "BA",
					phone_code: "387",
				},
				{
					id: "dd7d5e9d-4689-4f3b-9a62-70dbed30ecf1",
					name: "Belgium",
					iso2: "BE",
					phone_code: "32",
				},
				{
					id: "dde50559-f8e8-4b9c-b970-20b661639eef",
					name: "Jordan",
					iso2: "JO",
					phone_code: "962",
				},
				{
					id: "df4d35b6-913f-4dcc-bb6e-267bb168870c",
					name: "Cuba",
					iso2: "CU",
					phone_code: "53",
				},
				{
					id: "df72332b-8e02-482a-b3da-52bf86fe3db2",
					name: "Bhutan",
					iso2: "BT",
					phone_code: "975",
				},
				{
					id: "df898295-72b2-4863-b542-c6a981591023",
					name: "Saint Pierre and Miquelon",
					iso2: "PM",
					phone_code: "508",
				},
				{
					id: "e0668209-eef8-4d9c-93a1-3ef1251349f3",
					name: "Andorra",
					iso2: "AD",
					phone_code: "376",
				},
				{
					id: "e0672cd3-ff66-41de-af08-e17d9cdc326c",
					name: "Saint Kitts And Nevis",
					iso2: "KN",
					phone_code: "+1-869",
				},
				{
					id: "e200c349-b1a8-4245-84aa-52322a88b608",
					name: "Costa Rica",
					iso2: "CR",
					phone_code: "506",
				},
				{
					id: "e378ed98-12b8-4838-acc5-6a988408c92d",
					name: "New Caledonia",
					iso2: "NC",
					phone_code: "687",
				},
				{
					id: "e3f45d9b-04e7-45a4-9213-413ac2f6ed7a",
					name: "Romania",
					iso2: "RO",
					phone_code: "40",
				},
				{
					id: "e626723c-4853-4a96-bdf2-f425766744e4",
					name: "Singapore",
					iso2: "SG",
					phone_code: "65",
				},
				{
					id: "e6eb5c06-0406-40ba-ab45-6b6319e54635",
					name: "Iceland",
					iso2: "IS",
					phone_code: "354",
				},
				{
					id: "e70c95a3-cbb6-4344-8114-d8723dc43d1f",
					name: "Curaçao",
					iso2: "CW",
					phone_code: "599",
				},
				{
					id: "ead8643c-73c4-4ffc-bc9f-0cc0c3ab03a7",
					name: "Uzbekistan",
					iso2: "UZ",
					phone_code: "998",
				},
				{
					id: "eafbb0c9-4d0b-4943-9caf-711e8b2bf7fd",
					name: "Ukraine",
					iso2: "UA",
					phone_code: "380",
				},
				{
					id: "ecd3a92c-6eda-4f02-8909-00233f44b20e",
					name: "Monaco",
					iso2: "MC",
					phone_code: "377",
				},
				{
					id: "ed9eb12d-57a1-46cf-8765-c48fd4b09cd8",
					name: "Aruba",
					iso2: "AW",
					phone_code: "297",
				},
				{
					id: "ee461311-6ece-4ed3-b434-0834a3f28fc4",
					name: "Heard Island and McDonald Islands",
					iso2: "HM",
					phone_code: "672",
				},
				{
					id: "ee8fc7e6-b3d9-4718-abe3-a1da8ff6a670",
					name: "Tajikistan",
					iso2: "TJ",
					phone_code: "992",
				},
				{
					id: "eece2e24-425b-483d-ae03-d5ef3bb4bf52",
					name: "Thailand",
					iso2: "TH",
					phone_code: "66",
				},
				{
					id: "f128a19c-8616-42af-b23e-154a4c2828f9",
					name: "Mauritania",
					iso2: "MR",
					phone_code: "222",
				},
				{
					id: "f2fda255-e0da-49e6-a05d-cac9f5499b92",
					name: "Maldives",
					iso2: "MV",
					phone_code: "960",
				},
				{
					id: "f3612cc5-41f9-4548-8b8d-061e9220e955",
					name: "Venezuela",
					iso2: "VE",
					phone_code: "58",
				},
				{
					id: "f4001225-16d0-47f4-84cd-23b89327e567",
					name: "Brunei",
					iso2: "BN",
					phone_code: "673",
				},
				{
					id: "f485fddb-3f55-470a-bffa-ac4b09c527b9",
					name: "Serbia",
					iso2: "RS",
					phone_code: "381",
				},
				{
					id: "f4b6ac48-cc3e-4d50-acc1-a3429d844aaf",
					name: "North Korea",
					iso2: "KP",
					phone_code: "850",
				},
				{
					id: "f64a5996-104e-42c6-aa6c-00d516bdae81",
					name: "Ireland",
					iso2: "IE",
					phone_code: "353",
				},
				{
					id: "f9fbd62e-32a6-4354-ba4f-11f68becce57",
					name: "Spain",
					iso2: "ES",
					phone_code: "34",
				},
				{
					id: "fb879989-74d1-4bc9-8d47-590fd355c7cb",
					name: "Norfolk Island",
					iso2: "NF",
					phone_code: "672",
				},
				{
					id: "fccb082f-58b9-425e-b00a-7325d9395520",
					name: "Mali",
					iso2: "ML",
					phone_code: "223",
				},
				{
					id: "fd395d15-1daf-416d-8b6c-3db5f1e34b0f",
					name: "Trinidad And Tobago",
					iso2: "TT",
					phone_code: "+1-868",
				},
				{
					id: "fd8c9069-8a2d-4af5-9d51-d838b720be33",
					name: "Benin",
					iso2: "BJ",
					phone_code: "229",
				},
				{
					id: "fe86e558-2fc6-4691-b40f-5b3467e89653",
					name: "Dominica",
					iso2: "DM",
					phone_code: "+1-767",
				},
				{
					id: "fedbeca1-4a8b-4977-aceb-da1de3db27ea",
					name: "El Salvador",
					iso2: "SV",
					phone_code: "503",
				},
				{
					id: "ffa70843-27cc-4270-bbf4-8ce8697e3094",
					name: "Egypt",
					iso2: "EG",
					phone_code: "20",
				},
			],
		};
	},
};
</script>

<style>
.form-custom-text-field .v-input__prepend-inner {
	margin-top: 16px;
	margin-right: 8px !important;
}
.form-custom-text-field.has-append .v-input__slot {
	padding-right: 8px !important;
}
.v-application--is-rtl .form-custom-text-field.has-append .v-input__slot {
	padding-right: 24px !important;
	padding-left: 8px;
}
.form-custom-text-field.has-append .v-input__append-inner {
	margin-top: 8px !important;
}
.form-custom-text-field.small .v-btn {
	height: 28px;
	width: 28px;
}
.form-custom-text-field.has-append.small .v-input__append-inner {
	margin-top: 6px !important;
}
.form-custom-text-field.has-append.small .v-input__slot {
	padding-right: 6px !important;
	padding-left: 24px !important;
}
.v-application--is-rtl .form-custom-text-field.has-append.small .v-input__slot {
	padding-right: 24px !important;
	padding-left: 6px !important;
}
.single-text-filed-values {
	max-height: 78px;
	overflow-y: auto;
}
.single-text-filed-value {
	border: 1px solid rgba(0, 0, 0, 0.05);
	border-radius: 4px;
	color: #777;
	font-size: 12px;
	margin: 4px;
}
.single-text-filed-value .v-btn {
	height: 28px;
	width: 28px;
}

.phone-number-input-select-option .v-select__slot {
	margin: -3px !important;
}
.phone-number-input-select-option .v-input__append-inner {
	margin: 0px !important;
}
.phone-number-input-select-option {
	max-width: 120px !important;
	min-width: 120px !important;
}
.phone-number-input-select-option > .v-input__control > .v-input__slot:before,
.phone-number-input-select-option > .v-input__control > .v-input__slot:after {
	display: none !important;
}
/* .form-custom-number1 {
	max-width: 800px !important;
} */

.form-custom-number1 input[type="number"]::-webkit-inner-spin-button,
.form-custom-number1 input[type="number"]::-webkit-outer-spin-button {
	-webkit-appearance: none;
	margin: 0;
}

.phone-number-input-select-option > .v-input__append-inner > .v-input__icon,
.v-input__icon--append > .v-icon:before,
.phone-number-input-select-option > .v-input__append-inner > .v-input__icon,
.v-input__icon--append > .v-icon:before {
	display: none !important;
}
</style>
