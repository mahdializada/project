<template>
	<v-app-bar color="primary" dense >
		<v-toolbar-title class="subtitle-1 font-weight-medium text-capitalize white--text">
			{{ $tr(title) }}
		</v-toolbar-title>
		<v-spacer></v-spacer>

		<v-menu
			
            v-if="showDateRange"
			style="top: sticky !important; z-index: 1000000"
			v-model="showDateMenu"
			:close-on-content-click="false"
			:nudge-right="40"
			transition="scale-transition"
			offset-y
			min-width="auto"
		>
			<template v-slot:activator="{ on, attrs }">
				<v-text-field
					class="white-text-field-background"
					v-model="dateRange"
					:placeholder="$tr('select_date_range')"
					readonly
					hide-details
					dense
					outlined
					v-bind="attrs"
					v-on="on"
				></v-text-field>
			</template>
			<v-date-picker v-model="dateRange" range>
				<v-spacer></v-spacer>
				<v-btn
					text
					color="primary"
					@click="
						() => {
							showDateMenu = false;
							dateRange = null;
						}
					"
				>
					{{ $tr('cancel') }}
				</v-btn>
				<v-btn text color="primary" @click="() => {$emit('getDate', dateRange); showDateMenu = false}">
					{{ $tr('ok') }}
				</v-btn>
			</v-date-picker>
		</v-menu>
	</v-app-bar>
</template>



<script>
export default {
    props: ['title', 'showDateRange', 'selectedRange'],


    data(){
        return {
            showDateMenu: false,
			dateRange: null,
        }
    },

	watch: {
		selectedRange: function (value) {
			this.dateRange = value;
		},
	},
}
</script>


<style >
	div.white-text-field-background > div {
		background-color: white !important;
	}
</style>
