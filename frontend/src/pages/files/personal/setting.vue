<template>
	<div class="files-container background">
		<PersonalTopTitle
			:lastItem="{ text: 'file_settings' }"
			homeName="file_settings"
			:showFilterBtn="false"
			:showSortBtn="false"
			:showSearch="false"
			:showMore= "false"
		/>

		<Settings :items="allData" :loading="loading" :fileAmounts="fileAmounts" />
	</div>
</template>

<script>
import PersonalTopTitle from "~/components/files/personal/PersonalTopTitle.vue";
import Settings from "~/components/files/personal/Settings.vue";

export default {
	components: {
		PersonalTopTitle,
		Settings,
	},

	data() {
		return {
			isSearching: false,
			allData: {},
			fileAmounts: {},
			loading: true,
		};
	},
	mounted() {
		this.fetchDataSettings();
	},

	methods: {
		async fetchDataSettings() {
			try {
				const response = await this.$axios.get("files/personal/settings");
				this.allData 				= response?.data?.items?.Alldata;
				this.loading 				= false;
				let imageAmount 		= response?.data?.items?.imageFile;
				let videoAmount 		= response?.data?.items?.videoFiles;
				let audioAmount 		= response?.data?.items?.audioFiles;
				let documentAmount 	= response?.data?.items?.documentFiles;
				let otherAmount 		= response?.data?.items?.otherFiles;
				let shareAmount 		= response?.data?.items?.sharesFiles;

				this.fileAmounts = {
					images        : imageAmount,
					videos        : videoAmount,
					audios        : audioAmount,
					documents     : documentAmount,
					others        : otherAmount,
					share_files   : shareAmount
				};
			} catch (err) {}
		},
	},
};
</script>
