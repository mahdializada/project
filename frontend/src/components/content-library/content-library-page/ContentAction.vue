<template>
  <v-row class="content_cta rounded px-1 justify-space-between align-center">
    <div class="select_all">
      <v-checkbox
        v-model="selectAll"
        label="Select All"
        @click="$emit('onSelectAll', selectAll)"
      ></v-checkbox>
    </div>
    <div class="action_btns">
      <v-btn
        color="success"
        @click="openChangeStatusDialog"
        v-if="filter_type == 'media'"
      >
        <v-icon left>mdi-dip-switch</v-icon>
        Status
      </v-btn>

      <v-btn style="background-color: #2dccff" dark @click="openLabelDialog">
        <svg
          class="mr-1"
          xmlns="http://www.w3.org/2000/svg"
          width="13.231"
          height="13.226"
          viewBox="0 0 13.231 13.226"
        >
          <path
            id="labels"
            d="M98.949,327.508q0-1.123,0-2.245a1.326,1.326,0,0,1,1.374-1.383c1.5,0,3.01,0,4.515,0a1.35,1.35,0,0,1,1,.4q2.967,2.963,5.927,5.933a1.3,1.3,0,0,1-.062,1.948q-2.231,2.239-4.471,4.47a1.775,1.775,0,0,1-.408.31,1.244,1.244,0,0,1-1.422-.132,2.347,2.347,0,0,1-.245-.228q-2.847-2.846-5.695-5.691a1.594,1.594,0,0,1-.517-1.238C98.964,328.937,98.949,328.222,98.949,327.508Zm1.33-1.323a.984.984,0,0,0,.968,1,1,1,0,0,0,1-1,.983.983,0,0,0-.989-.978A.965.965,0,0,0,100.28,326.185Z"
            transform="translate(-98.944 -323.878)"
            fill="#fff"
          />
        </svg>
        Label
      </v-btn>

      <v-btn style="background-color: #ff0070" dark @click="openRemarkDialog">
        <svg
          class="mr-1"
          xmlns="http://www.w3.org/2000/svg"
          width="15.71"
          height="15.686"
          viewBox="0 0 15.71 15.686"
        >
          <path
            id="remark"
            d="M178.4,248.22c1.286,0,2.571-.009,3.857,0a3.9,3.9,0,0,1,3.892,3.1,4.615,4.615,0,0,1,.1.983c.009,1.7.007,3.4,0,5.106a3.927,3.927,0,0,1-4.115,4.1c-1.566.005-1.235-.17-2.2,1.086-.179.233-.352.47-.529.7a1.135,1.135,0,0,1-2,.01c-.325-.429-.656-.854-.968-1.293a1.177,1.177,0,0,0-1.025-.519,8.812,8.812,0,0,1-2.071-.174,3.82,3.82,0,0,1-2.783-3.721c-.02-1.824-.026-3.649,0-5.473a3.915,3.915,0,0,1,3.981-3.9C175.825,248.209,177.111,248.22,178.4,248.22Zm-.013,5.291c1.284,0,2.569,0,3.853,0,.387,0,.619-.185.654-.5.048-.423-.224-.674-.749-.675q-3.743,0-7.486,0c-.494,0-.755.216-.751.6s.252.569.735.569Q176.511,253.513,178.383,253.511Zm-1.576,3.938c.759,0,1.518.006,2.276,0a.627.627,0,0,0,.692-.6.6.6,0,0,0-.676-.567q-2.276-.006-4.553,0a.8.8,0,0,0-.41.115.525.525,0,0,0-.2.649.6.6,0,0,0,.633.4C175.313,257.45,176.06,257.448,176.807,257.449Z"
            transform="translate(-170.54 -248.216)"
            fill="#fff"
          />
        </svg>

        Remark
      </v-btn>

      <v-btn
        dark
        color="red"
        class="ml-2"
        @click="onDeleteClicked"
        v-if="filter_type == 'media' || filter_type == 'history'"
      >
        <svg
          class="mr-1"
          xmlns="http://www.w3.org/2000/svg"
          width="18"
          height="18"
          viewBox="0 0 18.583 18.604"
        >
          <path
            id="delete"
            d="M-9499.454-10612.4a2.69,2.69,0,0,1-2.916-2.927v-10.255c-.576,0-1.083,0-1.59,0a.931.931,0,0,1-1.041-.881.921.921,0,0,1,1.02-.9q7.767,0,15.526,0a.921.921,0,0,1,1.037.88.927.927,0,0,1-1.024.9c-.518,0-1.037,0-1.605,0v10.241a2.7,2.7,0,0,1-2.942,2.94l-3.231,0Zm4.138-9.577q-.009,2.1,0,4.19c0,.616.322.969.861.969a.869.869,0,0,0,.883-.963c0-.7,0-1.4,0-2.095s0-1.4,0-2.1a.872.872,0,0,0-.875-.971C-9494.988-10622.941-9495.314-10622.59-9495.316-10621.976Zm-3.527-.035q-.007,2.137,0,4.271a.857.857,0,0,0,.881.923.834.834,0,0,0,.861-.929c.008-.712,0-1.425,0-2.137s.008-1.425,0-2.137a.837.837,0,0,0-.87-.923A.862.862,0,0,0-9498.844-10622.011Zm2.633-6.205c-.549,0-1.1.008-1.646,0a.9.9,0,0,1-.987-.878.885.885,0,0,1,.969-.894q1.668-.019,3.332,0a.884.884,0,0,1,.968.9.9.9,0,0,1-.989.875c-.219,0-.438.006-.657.006Z"
            transform="translate(9505.501 10630.501)"
            fill="#fff"
            stroke="rgba(0,0,0,0)"
            stroke-width="1"
          />
        </svg>

        Delete
      </v-btn>
    </div>
    <div class="other_btns d-flex align-center">
      <v-btn
        color="primary"
        @click="openInsertDialog"
        class="me-1"
        v-if="filter_type == 'media'"
      >
        <v-icon left>mdi-plus-thick</v-icon>
        Add Media
      </v-btn>
      <FilterDateRange
        ref="filterDateRange"
        :dateRangeProp="date_range"
        :hide-title="true"
        :nudge_left="150"
        @dateChanged="$emit('onDateChanged', $event)"
      />

      <v-btn class="ml-2" fab icon color="primary" @click="$emit('onFilter')">
        <v-badge dot :value="filters.length > 0">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="19.444"
            height="17.77"
            viewBox="0 0 19.444 17.77"
          >
            <path
              id="Path_2156"
              data-name="Path 2156"
              d="M-6343.477-4389.457a2.534,2.534,0,0,1-1.428-2.814,2.509,2.509,0,0,1,2.333-1.974,2.4,2.4,0,0,1,2.376,1.4.417.417,0,0,0,.451.262c.745-.011,1.489-.006,2.234,0a.826.826,0,0,1,.923.833c0,.518-.335.817-.923.83h-1.1c-.118,0-.236.009-.354,0a1.364,1.364,0,0,0-1.545.718,2.133,2.133,0,0,1-1.888.974A2.672,2.672,0,0,1-6343.477-4389.457Zm-10.545-1.462c-.626,0-.966-.3-.966-.832s.362-.832.967-.832q3.218,0,6.439,0a.83.83,0,0,1,.944.815c.01.534-.33.843-.947.846-1.086,0-2.172,0-3.259,0h-3.179Zm2.663-5.561a.446.446,0,0,0-.476-.291c-.76.015-1.518.012-2.276,0a.836.836,0,0,1-.845-.583.793.793,0,0,1,.314-.933,1.366,1.366,0,0,1,.591-.167c.376-.026.758-.007,1.135-.007.106,0,.211-.008.315,0a1.366,1.366,0,0,0,1.525-.685,2.33,2.33,0,0,1,2.9-.793,2.5,2.5,0,0,1,1.53,2.649,2.507,2.507,0,0,1-2.076,2.157,2.2,2.2,0,0,1-.426.042A2.525,2.525,0,0,1-6351.359-4396.48Zm7.3-.29a.857.857,0,0,1-.857-.618.808.808,0,0,1,.392-.94,1.5,1.5,0,0,1,.6-.127c1.06-.012,2.119-.005,3.179-.005s2.146-.006,3.221,0a.836.836,0,0,1,.93.952c-.046.458-.411.738-.99.74q-2.139,0-4.278,0c-.246,0-.489,0-.733,0h-.6C-6343.48-4396.762-6343.77-4396.764-6344.059-4396.77Zm2.453-6.71a2.551,2.551,0,0,1,2.53-2.519,2.506,2.506,0,0,1,2.489,2.5,2.471,2.471,0,0,1-2.492,2.522h-.009A2.512,2.512,0,0,1-6341.605-4403.48Zm-12.489.837a.82.82,0,0,1-.893-.828.8.8,0,0,1,.881-.833c1.662-.006,3.323,0,4.983,0s3.323,0,4.985,0a.8.8,0,0,1,.829.588.764.764,0,0,1-.312.9,1.025,1.025,0,0,1-.544.167q-3.147.01-6.3.009Z"
              transform="translate(6355.529 4406.499)"
              fill="#555"
              stroke="rgba(0,0,0,0)"
              stroke-width="1"
            />
          </svg>
        </v-badge>
      </v-btn>
    </div>
  </v-row>
</template>
<script>
import moment from "moment";
import FilterDateRange from "../../advertisement/FilterDateRange.vue";

export default {
  props: { selectedItems: Array, filters: Array, filter_type: String },
  data() {
    return {
      selectAll: false,
      date_range: {
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
      },
    };
  },
  methods: {
    openInsertDialog() {
      this.$emit("openInsertDialog");
    },
    openLabelDialog() {
      this.$emit("openLabelDialog");
    },
    openRemarkDialog() {
      this.$emit("openRemarkDialog");
    },
    openChangeStatusDialog() {
      this.$emit("openChangeStatusDialog");
    },
    onDeleteClicked() {
      // please_select_items_to_delete
      console.log("seleectedd item ", this.selectedItems);

      console.log("selected", this.selectedItems);
      this.$TalkhAlertNA(
        this.$tr("are_you_sure"), //text
        "delete", //icon
        async () => {
          this.delete();
        }, // callback function
        "Yes" // btn text
      );
    },
    async delete() {
      const ids = this.selectedItems.map((row) => row.id);
      const response = await this.$axios.delete(
        `library/content-library/${ids}`,
        {
          params: { filter_type: this.filter_type },
        }
      );
      if (response.status == 200) {
        ids.forEach((id) => {
          this.$emit("onDelete", id);
        });
        this.$toasterNA("green", this.$tr("successfully_deleted"));
      } else this.$toasterNA("red", this.$tr("something_want_wrong"));
    },
  },
  components: { FilterDateRange },
};
</script>

<style scoped>
.content_cta {
  width: 100%;
  background-color: #f8f8f8;
  margin: 0 !important;
  /* margin-bottom: 15px !important; */
}
</style>
