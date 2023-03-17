<template>
	<div
		class="w-full h-full d-flex flex-column align-center justify-center"
		v-if="items.length == successCount"
	>
		<lottie-player
			src="/assets/done_animation.json"
			background="transparent"
			speed="1"
			loop
			autoplay
			style="width: 450px; height: 450px"
		>
		</lottie-player>
		<div class="friqiBase--text" style="font-size: 30px; font-weight: 600">
			{{ $tr("successfully_inserted") }}
		</div>
	</div>
	<div class="connection__container" v-else>
		<CTitle text="result" />
		<div class="mb-3">
			<v-btn @click="key = key + 2" color="primary" class="ma-1">show</v-btn>

			<v-data-table
				v-if="items.length > 0"
				:key="key"
				:headers="headers"
				:items="data"
				:single-expand="true"
				:expanded.sync="expanded"
				show-expand
				height="600px"
				item-key="ad_id"
				:items-per-page="itemPerPage"
				class="elevation-1"
			>
				<template v-slot:expanded-item="{ headers, item }">
					<td :colspan="headers.length" class="" style="text-align: center">
						{{ $tr("Ad Id") }}
						{{
							$tr(
								item.result
									? "successfully_inserted"
									: item.not_found
									? "not_found"
									: item.exists
									? "already_exists"
									: item.invalid
									? "is_invalid"
									: item.message
									? item.message
									: "something_went_wrong"
							)
						}}
					</td>
					<div></div>
				</template>
				<template v-slot:[`item.number`]="{ index }">
					<div>{{ index }}</div>
				</template>
				<template v-slot:[`item.result`]="{ item }">
					<div>
						<v-icon
							:color="item.result ? 'primary' : 'red lighten-3'"
							v-text="
								item.result
									? 'mdi-check-circle-outline'
									: 'mdi-close-circle-outline'
							"
						></v-icon>
						<small class="grey--text px-2">
							{{ item.result ? "Inserted" : "Not Inserted" }}
						</small>
					</div>
				</template>
			</v-data-table>
			<!-- <v-simple-table v-if="items.length > 0" fixed-header height="800px">
				<template v-slot:default>
					<thead>
						<tr>
							<th class="text-left">#</th>
							<th class="text-left">ad_id</th>
							<th class="text-left">result</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(item, index) in items" :key="index">
							<td>{{ index + 1 }}</td>
							<td>{{ item.ad_id }}</td>
							<td style="width: 20px">
								<v-expansion-panels>
									<v-expansion-panel>
										<v-expansion-panel-header>
											<v-icon
												:color="item.result ? 'primary' : 'red lighten-3'"
												v-text="
													item.result
														? 'mdi-check-circle-outline'
														: 'mdi-close-circle-outline'
												"
											></v-icon
										></v-expansion-panel-header>
										<v-expansion-panel-content>
											Lorem ipsum dolor sit amet, consectetur adipiscing elit
										</v-expansion-panel-content>
									</v-expansion-panel>
								</v-expansion-panels>
							</td>
						</tr>
					</tbody>
				</template>
			</v-simple-table> -->
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import MultiInputItem from "~/components/new_form_components/cat_product_selection/MultiInputItem.vue";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";

export default {
	props: {
		data: Array,
	},
	watch: {
		data: function (data) {},
	},
	data() {
		return {
			key: 0,
			expanded: [],
			headers: [
				{
					text: "#",
					value: "number",
				},
				{ text: "ad_id", value: "ad_id" },
				{ text: "result", value: "result" },
			],
			// items: [],
			items: [],
			key: 1,
			itemPerPage: 5,
			successCount: 0,
		};
	},
	methods: {
		setData(data) {
			this.key = this.key + 1;
			this.items = this.items;

			this.key = this.key + 1;
		},
		async loaded(data) {
			this.successCount = 0;
			this.items = data;

			await this.items.map((item) => {
				if (item.result == true) this.successCount = this.successCount + 1;
			});
			if (this.items.length == this.successCount)
				this.$toasterNA("green", this.$tr("successfully_inserted"));
			this.key = this.key + 1;
			this.itemPerPage = 15;
		},
	},
	components: {
		CTitle,
		CTextField,
		MultiInputItem,
	},
};
</script>

<style scoped lang="scss"></style>
