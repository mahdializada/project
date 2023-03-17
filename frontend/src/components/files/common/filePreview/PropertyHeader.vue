/** * @author Sayed Nasim Alizai * @email Sayedhakimi449@gmail.com * @create
date 2022-05-29 16:02:02 * @modify date 2022-05-29 16:02:02 * @desc
[description] */

<!-- please do not make it bad again !!!!! 😒😒😒😒 -->
<template>
	<v-card-title
		:class="`py-1 ${
			$vuetify.theme.dark ? ' dark' : ' grey lighten-3 '
		} rounded-sm justify-space-between flex-column`"
	>
		<div class="my-1 w-full">
			<v-tooltip bottom>
				<template v-slot:activator="{ on, attrs }">
					<p v-bind="attrs" v-on="on" style="font-size: 13px">
						{{ items === null ? "" : items.name }}
					</p>
				</template>
				<span> {{ items === null ? "" : items.name }}</span>
			</v-tooltip>
			<!-- <v-avatar color="blue lighten-4" size="30">
        <v-icon color="primary" size="20">mdi-account</v-icon>
      </v-avatar>
      <span
        v-if="items && items.created_by"
        class="font-weight-light text-truncate"
        style="font-size: 12px"
      >
        {{ items.created_by.firstname + " " + items.created_by.lastname }}
      </span> -->
		</div>
		<div class="d-flex align-center justify-end mt-1 w-full">
			<span class="text-center me-1">
				<v-avatar color="blue lighten-4" size="30">
					<v-icon color="primary" size="20">fas fa-eye</v-icon>
				</v-avatar>
				<p class="font-weight-light" style="font-size: 12px">
					{{ countFormatter("views_count") | kFormatter }}
				</p>
			</span>
			<span class="text-center mx-1">
				<v-avatar color="blue lighten-4" size="30">
					<v-icon color="primary" size="20">mdi-cloud-download</v-icon>
				</v-avatar>
				<p class="font-weight-light" style="font-size: 12px">
					{{ countFormatter("downloads_count") | kFormatter }}
				</p>
			</span>
			<span class="text-center ms-1">
				<v-avatar color="blue lighten-4" size="30">
					<v-icon color="primary" size="20">mdi-comment</v-icon>
				</v-avatar>
				<p class="font-weight-light" style="font-size: 12px">
					{{ countFormatter("comments_count") | kFormatter }}
				</p>
			</span>
		</div>
	</v-card-title>
</template>
<script>
export default {
	name: "property-header",
	props: {
		items: Object,
	},

	computed: {
		countFormatter() {
			return (val) => (this.items && this.items[val] > 0 ? this.items[val] : 0);
		},
	},
};
</script>
<style scoped>
.header {
	font-size: 13px;
}
p {
	margin-bottom: 0px;
}
</style>
