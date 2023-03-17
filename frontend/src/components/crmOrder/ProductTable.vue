<template>
	<div class="px-2 py-1">
		<v-timeline align-top dense flat class="customTimeLine">
			<v-row class="pr-2 mt-5">
				<v-col md="6">
					<v-timeline-item
						class="pb-1 timeline-small-dot"
						v-for="key in keys"
						:key="key.key"
						color="primary"
						dense
						icon="mdi-check"
					>
						<div>
							<div class="d-flex">
								<p class="cutom-paragraph">{{ key.key }}</p>
								<v-spacer></v-spacer>
								<p class="p">{{ key.stepper }}</p>
							</div>
							<span v-if="form.$model[key.key]" class="p">
								<p v-if="key.key == 'projectname'">
									{{ form.$model.projectname[0].name }}
								</p>
								<span v-else-if="key.key == 'products'">
									<p
										v-for="product in form.$model.projectname"
										:key="product.name"
									>
										* {{ product.name }}
									</p>
								</span>
								<p v-else>{{ form.$model[key.key] }}</p>
							</span>
						</div>

						<v-divider></v-divider>
					</v-timeline-item>
				</v-col>
				<v-col md="6 px-1">
					<v-timeline-item
						class="pb-1 timeline-small-dot"
						v-for="key in keys2"
						:key="key.key"
						color="primary"
						dense
						icon="mdi-check"
					>
						<div>
							<div class="d-flex">
								<p class="cutom-paragraph">{{ key.key }}</p>
								<v-spacer></v-spacer>
								<p class="p">{{ key.stepper }}</p>
							</div>
							<span v-if="form.$model[key.key]" class="p">
								<p>{{ form.$model[key.key] }}</p>
							</span>
						</div>

						<v-divider></v-divider>
					</v-timeline-item>
				</v-col>
			</v-row>
		</v-timeline>
	</div>
</template>

<script>
import DataTable from "~/components/design/DataTable.vue";
export default {
	components: {
		DataTable,
	},
	props: {
		form: Object,
	},
	data() {
		return {
			selectedItems: [],
			apiCalling: false,
			items: [],
			headers: [],
			keys: [
				{
					key: "projectname",
					stepper: "project",
				},
				{
					key: "source",
					stepper: "project",
				},
				{
					key: "phone_number",
					stepper: "project",
				},

				{
					key: "price",
					stepper: "product_info",
				},
				{
					key: "products",
					stepper: "product",
				},
			],
			keys2: [
				{
					key: "send_brochure",
					stepper: "product_info",
				},
				{
					key: "with_tax",
					stepper: "product_info",
				},
				{
					key: "delay",
					stepper: "product_info",
				},
				{
					key: "note",
					stepper: "product_info",
				},
				{
					key: "address",
					stepper: "address",
				},
			],
		};
	},
	methods: {
		onTableChanges() {},
		onRowClicked() {},

		async loaded() {},

		validate() {
			return true;
		},
	},
};
</script>
<style scoped></style>

<style>
/* v-timeline-item pb-1 timeline-small-dot theme--light */
.timeline-small-dot .v-timeline-item__dot .v-timeline-item__inner-dot {
	width: 24px !important;
	height: 24px;
}
.timeline-small-dot .v-timeline-item__dot {
	background: transparent !important;
	box-shadow: unset !important;
	width: 30px !important;
	height: 30px;
}

.timeline-small-dot
	.v-timeline-item__dot
	.v-timeline-item__inner-dot
	.v-icon.v-icon {
	font-size: 15px;
}
.cutom-paragraph {
	font-size: 18px;
	font-weight: 100;
	color: #777;
}
.p {
	color: #777 !important;
}
</style>
