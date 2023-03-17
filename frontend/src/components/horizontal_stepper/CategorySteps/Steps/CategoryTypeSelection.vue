<template>
	<div class="w-full h-full d-flex align-center justify-center pt-5">
		<div class="w-full">
			<CTitle :text="$tr('what_do_you_want_to_create')" class="mb-4" />
			<ItemsSelectionLg
				v-model="form.itemType.$model"
				:items="ItemsSelectionItems"
				paddingBottom="pb-1"
			/>
		</div>
	</div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "../../../new_form_components/Inputs/CTitle.vue";
import ItemsSelectionLg from "../../../new_form_components/Inputs/ItemsSelectionLg.vue";
export default {
	props: {
		form: Object,
	},
	data() {
		return {
			validateRule: GlobalRules.validate,
			ItemsSelectionItems: [
				{
					id: "category",
					title: this.$tr("category"),
					icon: `
						<svg
              viewBox="0 0 39.066 38.414"
            >
              <path
                d="M157.808,95.717h-9.589a3.562,3.562,0,0,1-3.551-3.551V82.577a3.562,3.562,0,0,1,3.551-3.551h9.648a3.562,3.562,0,0,1,3.551,3.551v9.648a3.6,3.6,0,0,1-3.61,3.492Zm22.315,0h-9.648a3.562,3.562,0,0,1-3.551-3.551V82.577a3.562,3.562,0,0,1,3.551-3.551h9.648a3.562,3.562,0,0,1,3.551,3.551v9.648a3.512,3.512,0,0,1-3.551,3.492Zm-22.2,21.723h-9.648a3.562,3.562,0,0,1-3.551-3.551V104.3a3.562,3.562,0,0,1,3.551-3.551h9.648a3.562,3.562,0,0,1,3.551,3.551v9.648a3.63,3.63,0,0,1-3.551,3.492Zm22.256,0h-9.589a3.562,3.562,0,0,1-3.551-3.551V104.3a3.562,3.562,0,0,1,3.551-3.551h9.589a3.562,3.562,0,0,1,3.551,3.551v9.648a3.55,3.55,0,0,1-3.551,3.492Z"
                transform="translate(-144.668 -79.026)"
                fill="#2e7be6"
              />
            </svg>
					`,
				},
				{
					id: "sub_category",
					title: this.$tr("sub_category"),
					icon: `
						<svg
              viewBox="0 0 39.066 38.415"
            >
              <g id="subcategory">
                <path
                  id="Path_354"
                  data-name="Path 354"
                  d="M13.14,16.691H3.551A3.562,3.562,0,0,1,0,13.14V3.551A3.562,3.562,0,0,1,3.551,0H13.2a3.562,3.562,0,0,1,3.551,3.551V13.2a3.6,3.6,0,0,1-3.61,3.492Z"
                  fill="#b9d7ff"
                />
                <path
                  id="Path_355"
                  data-name="Path 355"
                  d="M227.6,16.691h-9.648A3.562,3.562,0,0,1,214.4,13.14V3.551A3.562,3.562,0,0,1,217.947,0H227.6a3.562,3.562,0,0,1,3.551,3.551V13.2a3.512,3.512,0,0,1-3.551,3.492Z"
                  transform="translate(-192.14)"
                  fill="#b9d7ff"
                />
                <path
                  id="Path_356"
                  data-name="Path 356"
                  d="M13.767,225.955H4.119A3.562,3.562,0,0,1,.568,222.4v-9.589a3.562,3.562,0,0,1,3.551-3.551h9.648a3.562,3.562,0,0,1,3.552,3.551v9.648a3.63,3.63,0,0,1-3.551,3.492Z"
                  transform="translate(-0.509 -187.541)"
                  fill="#b9d7ff"
                />
                <path
                  id="Path_357"
                  data-name="Path 357"
                  d="M228.673,225.955h-9.589a3.562,3.562,0,0,1-3.551-3.551v-9.589a3.562,3.562,0,0,1,3.551-3.551h9.589a3.562,3.562,0,0,1,3.551,3.551v9.648a3.55,3.55,0,0,1-3.551,3.492Z"
                  transform="translate(-193.158 -187.541)"
                  fill="#2e7be6"
                />
              </g>
            </svg>
					`,
				},
			],
		};
	},
	methods: {
		async loaded() {},
		async validate() {
			this.form.itemType.$touch();
			return !this.form.itemType.$invalid;
		},
	},
	components: { CTitle, ItemsSelectionLg },
};
</script>
