<template>
	<div class="mt-5">
		<div>
			<CTitle :text="`select_system_&_subsystem`" />
			<div class="custom-card white w-full">
				<p class="pt-2 ps-3 input-title mb-0">
					{{ $tr("select_subsystem") }}
					<CTooltip text="tooltip" />
				</p>
				<v-list v-if="!attrs_sk.boilerplate" class="px-2" dense>
					<v-list-group
						v-for="(system, index) in systems"
						:key="index"
						active-class="custom-active-class"
						class="my-custom-list"
						append-icon="mdi-chevron-down"
						flat
						:ripple="false"
					>
						<template v-slot:activator>
							<v-icon
								class="pe-1"
								:class="checkIfElementSelected(system.sub_systems) > 0 ? 'primary--text' : ''"
								v-html="
									checkIfElementSelected(system.sub_systems) == system.sub_systems.length
										? 'mdi-checkbox-marked'
										: checkIfElementSelected(system.sub_systems) > 0
										? 'mdi-minus-box-outline'
										: 'mdi-checkbox-blank-outline'
								"
							></v-icon>
							<v-list-item-content
								:class="checkIfElementSelected(system.sub_systems) > 0 ? 'primary--text' : ''"
							>
								<v-list-item-title v-text="system.name"></v-list-item-title>
							</v-list-item-content>
						</template>

						<v-list-item
							v-for="(subsystem, j) in system.sub_systems"
							:key="j"
							class="custom-child-list ps-1 ms-4"
							@click="selectSubsystem(subsystem.id)"
						>
							<v-checkbox
								color="primary"
								:value="form.$model.subsystem_ids.includes(subsystem.id)"
							></v-checkbox>
							<v-list-item-content>
								<v-list-item-title v-text="subsystem.name"></v-list-item-title>
							</v-list-item-content>
						</v-list-item>
					</v-list-group>
				</v-list>

				<span v-for="i in 5" :key="i">
					<v-skeleton-loader
						v-if="attrs_sk.boilerplate"
						v-bind="attrs_sk"
						type="list-item-two-line"
					></v-skeleton-loader>
				</span>
			</div>
		</div>
	</div>
</template>

<script>
import InputCard from "../../new_form_components/components/InputCard.vue";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import CTooltip from "../../new_form_components/components/CTooltip.vue";
import GlobalRules from "~/helpers/vuelidate";

export default {
	components: { InputCard, CTitle, CTooltip },
	props: {
		form: Object,
	},
	data: () => ({
		validateRule: GlobalRules.validate,

		model: [],
		active: true,
		systems: [],
		attrs_sk: {
			style: "margin-bottom:0.2px",
			boilerplate: true,
			elevation: 0,
		},
	}),
	methods: {
		async validate() {
			this.form.subsystem_ids.$touch();
			return !this.form.subsystem_ids.$invalid;
		},
		resetValidation() {},
		async loaded() {
			await this.fetchSystems();
      this.attrs_sk.boilerplate=false
		},
		async fetchSystems() {
			try {
				const response = await this.$axios.get("all-system-subsystem");
				this.systems = response?.data;
			} catch (e) {}
		},
		selectSubsystem(id) {
			let subsystem_set = new Set(this.form.$model.subsystem_ids);
			if (subsystem_set.has(id)) subsystem_set.delete(id);
			else subsystem_set.add(id);
			this.form.$model.subsystem_ids = Array.from(subsystem_set);
		},

		checkIfElementSelected(subsystems) {
			let subsystem_set = new Set(this.form.$model.subsystem_ids);
			let flag = 0;
			for (const item of subsystems) {
				if (subsystem_set.has(item.id)) {
					flag++;
				} else {
				}
			}

			return flag;
		},
	},
	async created() {},
};
</script>

<style scoped>
.custom-card {
  border-radius: 16px;
  height: 400px;
}
.input-title {
	font-size: 18px;
	font-weight: 500;
	color: #777;
}
</style>
<style>
.custom-active-class {
	border: 1px solid var(--v-primary-base);
	border-radius: 10px;
}

.my-custom-list .v-list-group__header.v-list-item.v-list-item--link:hover {
	background-color: #e3f2fd !important;
	border-radius: 10px;
}
.my-custom-list .v-list-group__header.v-list-item.v-list-item--link {
	height: 30px;
}

.my-custom-list .v-list-item--link:before {
	background-color: unset !important;
}
.custom-child-list {
	cursor: pointer !important;
	height: 30px;
	/* margin-top: 2px; */
}
.custom-child-list:hover {
	background: #e3f2fd !important;
	border-radius: 10px !important;
}
.my-custom-list .v-list-group__header__append-icon .v-icon {
	font-size: 1.5rem !important;
}
</style>
