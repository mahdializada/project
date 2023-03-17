<template>
	<v-card>
		<v-card-title class="px-3 pt-3 pb-0 align-start">
			<span class="mx-1">
				<span class="dialog-title"> {{ $tr("select_file_type") }}</span>
				<p class="subtitle-2 pb-0 text-grey">
					{{ $tr("choose_download_types") }}
				</p>
			</span>
			<v-spacer />
			<svg
				@click="onClose"
				width="40px"
				height="40px"
				viewBox="0 0 700 560"
				fill="currentColor"
				style="transform: scaleY(-1); cursor: pointer"
			>
				<g>
					<path
						d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z"
					/>
					<path
						d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z"
					/>
				</g>
			</svg>
		</v-card-title>
		<v-card-text class="d-flex flex-wrap px-3 mt-2">
			<download-csv
				style="display: none"
				id="dwnCsv"
				:name="
					new Date(this.date_now).toLocaleDateString() +
					' - ' +
					filename +
					'.csv'
				"
				:data="jsonData"
			>
			</download-csv>
			<div
				v-for="(download, index) in downloadFormats"
				:key="index"
				style="margin: 10px"
			>
				<v-card
					@click="clickCard(index)"
					width="130px"
					height="130px"
					class="rounded-lg downloadCard"
					style="border: 1px solid transparent"
					:style="
						download.active
							? 'border: 1px solid var(--v-primary-base)'
							: 'border: 1px solid transparent'
					"
				>
					<v-icon
						size="20"
						class="d-flex justify-end mx-1 mt-1"
						:color="download.active ? 'primary' : ''"
						>{{
							download.active
								? "mdi-checkbox-marked-circle"
								: "mdi-checkbox-blank-circle-outline"
						}}</v-icon
					>

					<div style="margin-top: -15px" class="d-flex justify-center">
						<v-avatar size="75" color="grey lighten-4"
							><span v-html="download.icon1"></span
						></v-avatar>
					</div>

					<div class="d-flex justify-center subtitle-2 mt-1">
						{{ download.text }} {{ " " + $tr("file") }}
					</div>
				</v-card>
			</div>
		</v-card-text>
		<v-divider class="mx-3"></v-divider>
		<div class="mx-3 my-2 d-flex justify-space-between align-center">
			{{ $tr("who_download_table") }}
			<v-switch
				v-model="isCreated"
				inset
				class="ma-0"
				hide-details
				dense
			></v-switch>
		</div>
		<div class="mx-3 my-2 d-flex justify-space-between align-center">
			{{ $tr("download_time") }}
			<v-switch
				v-model="isTimeDate"
				inset
				class="ma-0"
				hide-details
				dense
			></v-switch>
		</div>
		<v-card-actions class="px-5 pb-3 mt-5 justify-end">
			<v-btn
				outlined
				class="ms-1 px-2"
				color="primary"
				@click="$emit('closeDownload')"
				>{{ $tr("cancel") }}</v-btn
			>
			<v-btn class="primary px-2" @click="onDownload">{{
				$tr("download")
			}}</v-btn>
		</v-card-actions>
	</v-card>
</template>

<script>
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import FileIcons from "../../configs/FileIcon";
import TextButton from "./buttons/TextButton.vue";
import { saveExcel } from "@progress/kendo-vue-excel-export";
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";
export default {
	name: "NewDownloadPopUp",
	props: {
		selected_header: {
			type: Array,
			required: true,
		},
		content: {
			type: Array,
			required: true,
		},
		filename: {
			type: String,
			default: "Filename",
		},
	},
	components: { CloseBtn, TextButton },
	data() {
		return {
			isTimeDate: false,
			isCreated: false,
			time_date: "",
			created_by: "",
			jsonData: [],
			counter: 0,
			date_now: new Date().toJSON().slice(0, 10).replace(/-/g, "-"),

			downloadFormats: [
				{
					type: "json",
					text: "JSON",
					icon1: FileIcons.json,
					active: false,
				},

				{
					type: "csv",
					text: "CSV",
					icon1: FileIcons.csv,
					active: false,
				},
				{
					type: "txt",
					text: "TXT",
					icon1: FileIcons.txt,
					active: false,
				},
				{
					type: "xml",
					text: "XML",
					icon1: FileIcons.xml,
					active: false,
				},
				{
					type: "sql",
					text: "SQL",
					icon1: FileIcons.sql,
					active: false,
				},
				{
					type: "excel",
					text: "EXCEL",
					icon1: FileIcons.excel,
					active: false,
				},
				{
					type: "pdf",
					text: "PDF",
					icon1: FileIcons.pdf,
					active: false,
				},
			],
		};
	},

	created() {
		this.created_by =
			this.$auth.user.firstname + " " + this.$auth.user.lastname;
		this.time_date =
			new Date(this.date_now).toLocaleTimeString(navigator.language, {
				hour: "2-digit",
				minute: "2-digit",
			}) +
			" | " +
			this.date_now;
	},

	methods: {
		clickCard(index) {
			this.downloadFormats[index].active = !this.downloadFormats[index].active;
		},

		onClose() {
			(this.isTimeDate = false),
				(this.isCreated = false),
				this.$emit("closeDownload");
		},

		async onDownload() {
			await this.getJsonData();
			const selectedFormats = this.downloadFormats.filter(
				(row) => row.active == true
			);

			selectedFormats.forEach((row) => {
				switch (row.type) {
					case "json":
						this.exportJson();
						break;
					case "xml":
						this.exportXML();
						break;
					case "csv":
						this.exportCSV();
						break;
					case "txt":
						this.exportTXT();
						break;
					case "sql":
						this.exportSQL();
						break;
					case "excel":
						this.exportExcel();
						break;
					case "pdf":
						if (this.selected_header.length >= 12) {
							// this.$toastr.w(this.$tr("columns_more_than_10"));
							this.$toasterNA("orange", this.$tr("columns_more_than_10"));
							
						} else {
							this.exportPDF();
						}
						break;
				}
			});
		},
		exportJson() {
			let data = this.jsonData;
			let json = JSON.stringify(data);
			let file = new Blob([json], { type: "text/json" });
			saveAs(
				file,
				new Date(this.date_now).toLocaleDateString() +
					" - " +
					this.filename +
					".json"
			);
		},
		exportXML() {
			this.counter = 0;
			let xml = `<?xml version="1.0" encoding="UTF-8" ?>`;
			xml += this.OBJtoXML(this.jsonData);
			let blob = new Blob([xml], { type: "octet/stream" });
			saveAs(
				blob,
				new Date(this.date_now).toLocaleDateString() +
					" - " +
					this.filename +
					".xml"
			);
		},

		OBJtoXML(obj, firstTime = true) {
			if (
				obj &&
				Object.keys(obj).length === 0 &&
				Object.getPrototypeOf(obj) === Object.prototype
			)
				// Return if Object is null
				return "";

			var xml = firstTime ? "<root>" : `<row-${this.counter}>`;
			for (var prop in obj) {
				xml += obj[prop] instanceof Array ? "" : "<" + prop + ">";
				if (obj[prop] instanceof Array) {
					for (var array in obj[prop]) {
						xml += "<" + prop + ">";
						xml += this.OBJtoXML(new Object(obj[prop][array]), false);
						xml += "</" + prop + ">";
					}
				} else if (typeof obj[prop] == "object") {
					xml += this.OBJtoXML(new Object(obj[prop]), false);
				} else {
					xml += obj[prop];
				}
				xml += obj[prop] instanceof Array ? "" : "</" + prop + ">";
			}
			xml += firstTime ? "</root>" : `</row-${this.counter}>`;

			var xml = xml.replace(/<\/?[0-9]{1,}>/g, "");
			this.counter++;
			return xml;
		},
		exportCSV() {
			var link = document.getElementById("dwnCsv");
			link.click();
		},

		exportTXT() {
			let data = this.jsonData;
			let json = JSON.stringify(data);
			let file = new Blob([json], { type: "text/plain;charset=utf-8" });
			saveAs(
				file,
				new Date(this.date_now).toLocaleDateString() +
					" - " +
					this.filename +
					".txt"
			);
		},

		exportSQL() {},

		async exportExcel() {
			await saveExcel({
				data: this.jsonData,
				fileName:
					new Date(this.date_now).toLocaleDateString() + " - " + this.filename,
				columns: this.get_headers(),
			});
		},

		exportPDF() {
			const doc = new jsPDF("l", "mm", [297, 210]);
			const halign = this.$vuetify.rtl ? "right" : "left";
			doc.autoTable({
				// Change the theme {grid, plain, striped}
				theme: "grid",
				margin: { top: 20, right: 5, bottom: 15, left: 5 },
				styles: {
					fontSize: 8,
					halign: halign,
					cellPadding: { top: 1, right: 0.5, bottom: 1, left: 0.5 },
				},
				didDrawPage: () => {
					var pageSize = doc.internal.pageSize;
					var pageHeight = pageSize.height
						? pageSize.height
						: pageSize.getHeight();
					doc.setFontSize(12);
					doc.text(
						this.filename,
						doc.internal.pageSize.getWidth() / 2 -
							(this.$options.name.length + 5),
						15
					);
					doc.setFontSize(8);
					var str = !this.$vuetify.rtl
						? this.$tr("page") + ":  " + doc.internal.getNumberOfPages()
						: doc.internal.getNumberOfPages() + " :" + this.$tr("page");
					var details = !this.$vuetify.rtl
						? this.$tr("created_by") + ":  " + this.created_by
						: this.created_by + " :" + this.$tr("created_by");
					doc.text(str, 21, pageHeight - 10);
					if (this.isCreated) {
						doc.text(details, 137, pageHeight - 10);
					}
					if (this.isTimeDate) {
						doc.text(
							!this.$vuetify.rtl
								? this.$tr("date") +
										":  " +
										new Date(this.date_now).toLocaleDateString()
								: new Date(this.date_now).toLocaleDateString() +
										" :" +
										this.$tr("date"),
							260,
							pageHeight - 10
						);
					}
				},
				headStyles: { fillColor: "#2562e0" },
				columns: this.get_headers().map((column) => {
					return {
						header: column.title,
						dataKey: column.field,
					};
				}),
				body: this.jsonData,
			});
			doc.save(
				new Date(this.date_now).toLocaleDateString() +
					" - " +
					this.filename +
					".pdf"
			);
		},

		get_headers() {
			let items = this.selected_header;
			return items.map((item) => {
				if (item.value.split(".").length > 1) {
					return {
						field: item.value.split(".")[1],
						title: item.text,
					};
				} else {
					return {
						field: item.value,
						title: item.text,
					};
				}
			});
		},

		getJsonData() {
			if (this.jsonData.length != this.content.length) {
				this.jsonData = this.FilterData();
			}
		},

		get_items() {
			let items = this.content;
			return JSON.parse(JSON.stringify(items)).map((item) => {
				let entries = Object.entries(item);
				for (let x = 0; x < entries.length; x++) {
					if (Array.isArray(entries[x][1])) {
						item[entries[x][0]] = this.get_comma_str(item[entries[x][0]]);
					} else if (
						typeof entries[x][1] === "object" &&
						entries[x][1] !== null
					) {
						item[entries[x][0]] =
							entries[x][1].name ??
							entries[x][1].title ??
							entries[x][1].firstname + " " + entries[x][1].lastname;
					}
				}
				return item;
			});
		},

		get_advertisement_items() {
			let items = this.content;
			items = items.map((ob) => {
				const flattenObj = (ob) => {
					// The object which contains the
					// final result
					let result = {};

					// loop through the object "ob"
					for (const i in ob) {
						if (typeof ob[i] === "object" && !Array.isArray(ob[i])) {
							const temp = flattenObj(ob[i]);
							for (const j in temp) {
								result[j] = temp[j];
							}
						} else {
							result[i] = ob[i];
						}
					}
					return result;
				};

				return flattenObj(ob);
			});

			return items;
		},
		get_comma_str(arr) {
			let arrStr = "";
			const length = arr.length;
			for (let index = 0; index < length; index++) {
				arrStr +=
					length - 1 === index ? arr[index]?.name : arr[index]?.name + ", ";
			}
			return arrStr;
		},
		FilterData() {
			let fileName = this.filename.split("-");

			let items =
				fileName[0] == "advertisement"
					? this.get_advertisement_items(this.content)
					: this.get_items(this.content);

			let headers = this.get_headers(this.selected_header);

			let newItems = items.map((item) => {
				let entries = Object.entries(item);
				let obj = {};
				for (let x = 0; x < entries.length; x++) {
					if (this.isInHeaders(entries[x][0], headers)) {
						obj[entries[x][0]] = entries[x][1];
					}
				}
				return obj;
			});

			return newItems;
		},
		isInHeaders(title, headers) {
			for (let x = 0; x < headers.length; x++) {
				if (headers[x].field == title) {
					return true;
				}
			}
			return false;
		},
	},
};
</script>

<style>
.downloadCard::before {
	border-radius: 12px !important;
}
</style>
