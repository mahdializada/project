<template>
	<div>
		<PropertyHeader :items="fileData" class="mt-2" />
		<ListItem
			icon="mdi-account"
			header="created_by"
			iconalign="align-baseline"
			class="mt-2"
		>
			<template v-slot:body>
				<p class="comments">
					{{
						fileData.created_by.firstname + " " + fileData.created_by.lastname
					}}
				</p>
			</template>
		</ListItem>

		<ListItem
			iconalign="align-start"
			icon="mdi-information"
			header="description"
			class="mt-3 mb-1"
		>
			<template v-slot:rear-icon>
				<span class="d-flex align-center me-5">
					<v-btn @click="toggleDescription" icon color="primary" outlined small>
						<v-icon size="21">
							{{ desriptionInput ? "mdi-close" : "mdi-pencil" }}
						</v-icon>
					</v-btn>
				</span>
			</template>

			<template v-slot:body>
				<div v-if="fileData && !desriptionInput">
					{{
						fileData.description
							? fileData.description
							: $tr("there_is_no_description")
					}}
				</div>
			</template>
		</ListItem>
		<div class="me-1">
			<ItemDescriptions
				ref="description"
				:description.sync="fileData.description"
				:fileId="fileData.id"
				@closeInput="closeDescription"
			/>
		</div>

		<!-- <ListItem icon="mdi-share" classes="align-center" header="Private">
      <template v-slot:rear-icon>
        <v-btn icon color="primary" small>
          <v-icon>mdi-pencil-circle-outline</v-icon>
        </v-btn>
      </template>
    </ListItem>
    <ListItem
      icon="mdi-link-variant"
      header="https://www.yourwebsite.com/path"
    />
    <ListItem
      iconalign="align-center"
      icon="mdi-tag"
      header="No Label"
      class="mt-0"
    >
      <template v-slot:rear-icon>
        <v-btn icon color="primary" small>
          <v-icon>mdi-plus-circle-outline</v-icon>
        </v-btn>
      </template>
    </ListItem> -->
		<ListItem
			icon="mdi-file"
			iconalign="align-center"
			header="Type"
			class="mt-0"
		>
			<template v-slot:body>
				<p class="comments text-uppercase">{{ fileData.extension }}</p>
			</template>
		</ListItem>
		<ListItem
			iconalign="align-center"
			icon="mdi-calendar"
			header="Created at"
			class="mt-0"
		>
			<template v-slot:body>
				<p class="comments">
					{{ parseDate(fileData.created_at) }} -
					{{ parseDate(fileData.created_at, "time") }}
				</p>
			</template>
		</ListItem>
		<ListItem
			icon="mdi-tooltip-edit"
			header="Last Modified At"
			class="mt-0"
			iconalign="align-center"
		>
			<template v-slot:body>
				<p class="comments">
					{{ parseDate(fileData.updated_at) }} -
					{{ parseDate(fileData.updated_at, "time") }}
				</p>
			</template>
		</ListItem>
		<ListItem
			v-if="isShared"
			:size="`16`"
			icon=" fa-users"
			:header="$tr('shared_with')"
			class="mt-0"
		>
			<template v-slot:body>
				<div class="d-flex flex-wrap py-1">
					<UserChip
						class="mb-1"
						v-for="(shares, i) in fileData.file_shares"
						:key="i"
						:name="`${shares.shared_to.firstname} ${shares.shared_to.lastname}`"
						:avatar="shares.shared_to.image"
						@close="removeFromShare(shares.id, fileData)"
						:showCloseBtn="!isShare"
						:selectedClose="!isShare"
						hasImage
					/>
				</div>
			</template>
		</ListItem>
	</div>
</template>
<script>
import moment from "moment-timezone";
import ListItem from "./ListItem.vue";
import PropertyHeader from "./PropertyHeader.vue";
import UserChip from "../../../design/UserChip.vue";
import ItemDescriptions from "./ItemDescriptions.vue";

export default {
	name: "list-collection",
	components: {
		ListItem,
		PropertyHeader,
		UserChip,
		ItemDescriptions,
	},
	props: {
		fileData: Object,
		isShare: Boolean,
	},
	data() {
		return {
			desriptionInput: false,
		};
	},

	computed: {
		isShared: function () {
			return this.fileData?.file_shares?.length > 0;
		},
	},

	methods: {
		closeDescription() {
			this.desriptionInput = false;
			const input = this.$refs.description;
			if (input) {
				input.toggleInput(this.fileData.description, false);
			}
		},
		toggleDescription() {
			const input = this.$refs.description;
			if (this.fileData && input) {
				this.desriptionInput = !this.desriptionInput;
				input.toggleInput(this.fileData.description, this.desriptionInput);
			}
		},

		parseDate(timestamps, type = "date") {
			if (type == "time") {
				return moment(timestamps)
					.locale(this.$vuetify.lang.current)
					.format("h:mm: A");
			}
			return moment(timestamps)
				.locale(this.$vuetify.lang.current)
				.format("MMMM Do YYYY");
		},
		removeFromShare(share_id, item) {
			this.$root.$emit("removeFromShare", share_id, item);
		},
	},
};
</script>
