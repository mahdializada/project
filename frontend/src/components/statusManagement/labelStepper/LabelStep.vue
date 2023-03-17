<template>
	<div class="mt-5" style="height: 90%" :key="i">
		<InputCard
			height="100"
			class="mt-2 overflow-auto expand"
			:title="isEdit ? $tr('update_label') : $tr('create_label')"
			:sub_title="isEdit ? $tr('update_label_text') : $tr('create_label_text')"
		>
			<v-expansion-panels flat accordion focusable mandatory>
				<v-timeline class="customTimeLine2" dense flat>
					<v-timeline-item
						class="pb-0"
						v-for="(item, ind) in form.labels.$model"
						:key="ind"
					>
						<template v-slot:icon>
							<v-icon light size="15" color="white">{{ ind + 1 }}</v-icon>
						</template>
						<v-expansion-panel>
							<v-expansion-panel-header
								disable-icon-rotate
								class="py-0 ps-0 pe-2 my-1 text-subtitle-2"
							>
								<div>
									<p class="mb-0 primary--text text-h6">
										{{ $tr("label_name") }}
									</p>
								</div>

								<template v-slot:actions>
									<span class="d-flex justify-center align-center">
										<div
											class="me-3 d-flex align-center"
											@click.stop="() => {}"
										>
											<span class="me-2 grey--text">{{ $tr("archive") }}</span>
											<v-switch
												readonly
												inset
												dense
												@click="clickswitch(item)"
												v-model="item.status"
											>
											</v-switch>
										</div>
										<v-icon @click.stop="closeLabel(item)" color="primary">
											mdi-close
										</v-icon>
									</span>
								</template>
							</v-expansion-panel-header>

							<v-expansion-panel-content>
								<CComboBox
									px="px-0"
									py="py-0"
									dense
									v-model="item.category"
									:placeholder="$tr('choose_item', $tr('category'))"
									:loading="loading"
									:items="labelCategory"
									item-value="title"
									item-text="title"
									prepend-inner-icon="fa fa-th"
									:canAddNewItem="true"
									:title="$tr('label_required', $tr('category'))"
									@onChange="onProductNameChanged(item.category, ind)"
								/>
								<div class="d-flex">
									<CTextField
										dense
										py="py-0"
										v-model="item.label_name"
										:title="$tr('label_required', $tr('label'))"
										:placeholder="$tr('label')"
										prepend-inner-icon="fa fa-atom"
										px="px-0"
									/>

									<CColorPicker
										dense
										px="px-0"
										py="py-0"
										v-model="item.color"
										:title="$tr('label_required', $tr('color'))"
										:currentColor="item.color"
										:placeholder="$tr('color')"
									/>
								</div>
								<div class="w-full">
									<CTextArea
										px="px-0"
										py="py-0"
										:title="$tr('label_required', $tr('title'))"
										:placeholder="$tr('title')"
										v-model.trim="item.title"
										prepend-inner-icon="mdi-clipboard-text-outline"
									/>
								</div>
							</v-expansion-panel-content>
						</v-expansion-panel>
						<v-divider></v-divider>
					</v-timeline-item>
				</v-timeline>
			</v-expansion-panels>
			<v-icon
				v-if="!isEdit"
				size="20"
				class="primary rounded-circle pa-1"
				color="white"
				style="left: 30px !important; top: 30px !important"
				@click="addOneMore"
				>mdi-plus</v-icon
			>
		</InputCard>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";
import CComboBox from "../../new_form_components/Inputs/CComboBox.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CColorPicker from "../../new_form_components/Inputs/CColorPicker.vue";
import CTextArea from "../../new_form_components/Inputs/CTextArea.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";
export default {
	props: {
		form: Object,
		isEdit: Boolean,
	},
	data() {
		return {
			i: 0,
			labelCategory: [],
			loading: false,
			switch1: false,
			validateRule: GlobalRules.validate,
		};
	},
	methods: {
		async validate() {
			return true;
		},
		async fetchLabelCategory() {
			try {
				const response = await this.$axios.get(`labels_category`);
				this.labelCategory = response.data;
			} catch (error) {
				if (error?.response?.status === 404 && !error?.response?.data?.result) {
					this.$toasterNA("orange", this.$tr("sub_system_not_found"));
				} else {
					HandleException.handleApiException(this, error);
				}
			}
		},
		async loaded() {
			this.i++;
			this.loading = true;
			await this.fetchLabelCategory();
			this.loading = false;
		},

		onProductNameChanged(product, i) {
			if (typeof product != "object") {
				this.form.$model.labels[i].category = {
					title: product,
					id: "new",
				};
			}
		},

		closeLabel(item) {
			if (this.form.$model.labels.length > 1) {
				this.form.$model.labels = this.form.$model.labels.filter(
					(row) => row.id != item.id
				);
			}
		},
		addOneMore() {
			this.form.$model.labels.push({
				id: this.generateID(),
				category: "",
				label_name: "",
				color: "#FF0000FF",
				title: "",
				tab: [],
				status: "",
			});
		},
		clickswitch(item) {
			this.form.$model.labels = this.form.$model.labels.map((row) => {
				if (row.id === item.id) {
					row.status = !row.status;
				}
				return row;
			});
			console.log(this.form.$model.labels);
		},

		generateID() {
			return (
				"Id" +
				Math.floor(
					(Date.now() *
						Math.floor(
							Math.random() * Math.floor(Math.random() * Date.now())
						)) /
						(Math.random() * 1000000000)
				)
			);
		},
	},
	components: {
		CAutoComplete,
		CTextField,
		CColorPicker,
		CTextArea,
		InputCard,
		CComboBox,
	},
};
</script>
<style>
.customTimeLine2 .v-timeline-item__dot {
	box-shadow: unset !important;
	position: absolute;
	top: 20px;
	left: calc(50% - 20px);
	border: 1px solid var(--v-primary-base);
	display: flex;
	align-items: center;
	justify-content: center;
	height: 40px;
	width: 40px;
}
.customTimeLine2.v-timeline::before {
	top: 50px !important;
}
.customTimeLine2 .v-expansion-panel-content__wrap {
	padding: 0 !important;
}
.customTimeLine2 .v-expansion-panels {
	z-index: 0 !important;
}
.expand .v-expansion-panels {
	display: block !important;
	z-index: 0 !important;
}
.expand .v-expansion-panel::before {
	box-shadow: none !important;
}
.expand .v-expansion-panel-header:before {
	background-color: var(--v-primary-base) !important;
	border-radius: 10px !important;
	left: -90px !important;
}
</style>
