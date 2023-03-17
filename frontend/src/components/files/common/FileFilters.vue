/**
 * @author Sayed Nasim Alizai
 * @email Sayedhakimi449@gmail.com
 * @create date 2022-05-28 14:30:31
 * @modify date 2022-05-28 14:30:31
 * @desc [description]
 */
<template>
	<v-card width="400">
		<v-card-text>
			<div class="d-flex align-center">
				<span class="text-no-wrap font-weight-bold me-2" style="width: 35.5%"
					>{{ $tr("file_type") }}
				</span>
				<div class="w-full">
					<CustomInput
						:items="fileTypes"
						item-value="id"
						item-text="name"
						v-model="selectedfileTypesId"
						
						type="autocomplete"
						:hideDetails="true"
						class="surface my-1"
						:autoSelectFirst="true"
						:filter_type="true"
					/>
				</div>
			</div>
			<div class="d-flex align-center" v-if="showTpye">
				<span class="text-no-wrap font-weight-bold me-2" style="width: 35.5%">{{
					$tr("type")
				}}</span>
				<div class="w-full">
					<CustomInput
						:items="fileTypesDataType"
						item-value="id"
						item-text="name"
						v-model="selectedfileTypesDataTypeId"
						
						type="autocomplete"
						:hideDetails="true"
						class="surface my-1"
					/>
				</div>
			</div>

			<div class="d-flex align-center" v-if="isOwner">
				<span class="text-no-wrap font-weight-bold me-2" style="width: 35.5%"
					>{{ $tr("owner") }}
				</span>
				<div class="w-full">
					<CustomInput
						:items="owners"
						item-value="id"
						item-text="name"
						v-model="selectedownersId"
						
						type="autocomplete"
						:hideDetails="true"
						class="surface my-1"
						:filter_type="true"
					/>
				</div>
			</div>

			<div class="d-flex align-center">
				<span class="text-no-wrap font-weight-bold me-2" style="width: 25%"
					>{{ $tr("date_modified") }}
				</span>
				<div class="w-full">
					<CustomInput
						:items="dateModifieds"
						item-value="id"
						item-text="name"
						v-model="selecteddateModifiedsId"		
						type="autocomplete"
						:hideDetails="true"
						class="surface my-1"
						:autoSelectFirst="true"
						:filter_type="true"
					/>
				</div>
			</div>

			<div
				class="d-flex align-center"
				v-if="selecteddateModifiedsId == 'custom'"
			>
				<span class="text-no-wrap font-weight-bold me-2" style="width: 35.5%"
					>{{ $tr("date") }}
				</span>
				<div class="w-full d-flex align-center my-1">
					<v-menu
						v-model="fromDatePicker"
						:close-on-content-click="false"
						transition="scale-transition"
						offset-y
						min-width="auto"
					>
						<template v-slot:activator="{ on, attrs }">
							<v-text-field
								v-model="startdate"
								readonly
								v-bind="attrs"
								v-on="on"
								hide-details
								dense
								outlined
								class="surface"
								:placeholder="$tr('please_select_date')"
							></v-text-field>
						</template>
						<v-date-picker
							:max="currentDate"
							v-model="startdate"
							no-title
							@input="fromDatePicker = false"
						></v-date-picker>
					</v-menu>

					<span class="mx-1">{{ $tr("to") }}</span>
					<v-menu
						v-model="toDatePicker"
						:close-on-content-click="false"
						:nudge-left="160"
						transition="scale-transition"
						offset-y
						min-width="auto"
					>
						<template v-slot:activator="{ on, attrs }">
							<v-text-field
								v-model="enddate"
								readonly
								:disabled="disabled"
								v-bind="attrs"
								hide-details
								dense
								v-on="on"
								outlined
								class="surface"
								:placeholder="$tr('please_select_date')"
							></v-text-field>
						</template>
						<v-date-picker
							:min="startdate"
							:max="currentDate"
							no-title
							v-model="enddate"
							@input="toDatePicker = false"
						></v-date-picker>
					</v-menu>
				</div>
			</div>
		</v-card-text>
		<div class="px-2 pb-2 d-flex justify-end">
			<v-btn elevation="0" right color="primary--text" @click="resetAll">{{
				$tr("clear")
			}}</v-btn>
			<v-btn
				@click="submitFilter"
				elevation="0"
				right
				color="primary"
				class="px-4 ms-2"
				>{{ $tr("filter") }}</v-btn
			>
		</div>
	</v-card>
</template>

<script>
import CustomInput from "../../design/components/CustomInput.vue";
import { mapMutations, mapState } from "vuex";

export default {
	components: {
		CustomInput,
	},
	props: {
		isOwner: {
			type: Boolean,
			default: false,
		},
	},

	computed: {
		...mapState("files", ["filters"]),
	},

	data() {
		return {
			currentDate: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
				.toISOString()
				.substr(0, 10),
			startdate: "",
			enddate: "",
			fromDatePicker: false,
			toDatePicker: false,
			fileTypes: ["any", "folder", "image", "video", "audio", "document"],
			owners: ["anyone", "owned_by_me", "not_owned_by_me", "specific_person"],
			fileTypesData: {
				video: ["any", "mp4", "mkv", "ogg", "mov", "webm"],
				image: ["any", "png", "jpg", "jpeg", "gif", "svg", "ico", "webp"],
				audio: ["any", "mp3", "wav"],
				document: [
					"any",
					'txt',
					"pdf",
					"doc",
					"docm",
					"docx",
					"dot",
					"dotx",
					"xlsx",
					"xlsm",
					"xltx",
					"xltm",
					"xls",
					"xlt",
					"xls",
					"xla",
					"xlw",
					"xlr",
					"adp",
					"adn",
					"accdb",
					"laccdb",
					"accdw",
					"accdc",
					"accda",
					"accdr",
					"accdt",
					"mdb",
					"mda",
					"mdw",
					"mdf",
					"mde",
					"accde",
					"mam",
					"mad",
					"maq",
					"mar",
					"mat",
					"maf",
					"pptx",
					"pptm",
					"ppt",
					"potx",
					"potm",
					"pot",
					"ppsx",
					"ppsm",
					"pps",
					"ppam",
					"ppa",
					"psd",
					"psdc",
					"psb",
					"ai",
					"eps",
					"ait",
					"svgz",
					"indd",
					"indl",
					"indt",
					"indb",
					"inx",
					"idml",
					"prel",
					"prproj",
					"aaf",
					"aep",
					"aet",
					"aepx",
					"zip",
					"rar",
				],
			},
			dateModifieds: [
				"any_time",
				"today",
				"yesterday",
				"last_seven_day",
				"custom",
			],
			selectedfileTypesId: "any",
			selectedfileTypesDataTypeId: "any",
			selecteddateModifiedsId: "any_time",
			selectedownersId:'anyone',
			disabled: true,
			showTpye: false,
			fileTypesDataType: [],
		};
	},

	watch: {
		startdate: function () {
			this.disabled = false;
		},
		selectedfileTypesId: function (val) {
			switch (val) {
				case "folder":
					this.showTpye = false;
					this.fileTypesDataType = [];
					break;
				case "image":
					this.showTpye = true;
					this.fileTypesDataType = this.fileTypesData.image;
					this.selectedfileTypesDataTypeId = "any";
					break;
				case "video":
					this.showTpye = true;
					this.fileTypesDataType = this.fileTypesData.video;
					this.selectedfileTypesDataTypeId = "any";
					break;
				case "audio":
					this.showTpye = true;
					this.fileTypesDataType = this.fileTypesData.audio;
					this.selectedfileTypesDataTypeId = "any";
					break;
				case "document":
					this.showTpye = true;
					this.fileTypesDataType = this.fileTypesData.document;
					this.selectedfileTypesDataTypeId = "any";
					break;
				default:
					this.showTpye = false;
					this.fileTypesDataType = [];
			}
		},
	},

	methods: {
		...mapMutations("files", ["changeFilter", "resetFilterState"]),

		submitFilter() {
			let filters = {
				selectedfileTypesId: this.selectedfileTypesId,
				selectedfileTypesDataTypeId: this.selectedfileTypesDataTypeId,
				selecteddateModifiedsId: this.selecteddateModifiedsId,
				startdate: this.startdate,
				enddate: this.enddate,
			};
			this.$emit("submitFilter", filters);
		},

		resetAll() {
			this.selectedfileTypesId = "any";
			this.selectedfileTypesDataTypeId = "any";
			this.selecteddateModifiedsId = "any_time";
			this.startdate = "";
			this.enddate = "";
			this.disabled = true;
			this.$emit('clearFilter',{selectedfileTypesId:this.selectedfileTypesId,
			selecteddateModifiedsId:this.selecteddateModifiedsId
			})
		},
	},
};
</script>

