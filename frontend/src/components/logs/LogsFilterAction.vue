<template>
 <v-card class="w-full mb-2 px-2 py-1" elevation="1" outlined>
    <h4 class="title ma-0 mb-2">{{ $tr("search_filter") }}</h4>
    <h5 class="title2 ms-2">{{ $tr("search_type") }}</h5>
    <!-- <v-row class="align-center">
      <v-col
        cols="12"
        lg="4"
        xl="4"
        class="py-0 d-flex flex-column flex-sm-row"
      >



      </v-col>

    </v-row> -->

      <v-row class="mx-3 py-0" align="center">
        <v-radio-group row class="my-1 mb-0" v-model="form.selectedOption" @change="changeType">
          <v-radio label="id" :value="1" />
          <v-radio label="content" :value="0" />
        </v-radio-group>
        <v-text-field
          :placeholder="$tr('search')"
          v-model="form.search"
          hide-details
          outlined
          dense
          rounded
          append-icon="mdi-magnify"
          clear-icon="mdi-close"
          clearable
          @click:clear="()=>{search=''; searchAction();}"
          @keyup="searchAction"
        />

        <v-menu
          v-model="menu"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="date"
              :label="$tr('search_date')"
              append-icon="mdi-calendar"
              class="ms-2"
              readonly
              outlined
              rounded
              dense
              hide-details=""
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker v-model="date" @input="getDate"></v-date-picker>
        </v-menu>

        <v-spacer />
        <slot name="ColumnButton" />

      </v-row>
  </v-card>
    <!-- <v-card class="py-2 pt-1 mb-2" elevation="24">
      <h4 class="mx-3">Search & Filter</h4>
      <v-row class="mx-3">
        <h5>Search Type</h5>
      </v-row>
      <v-row class="mx-3 py-0" align="center">
        <v-radio-group row class="my-1 mb-0" v-model="selectedOption">
          <v-radio label="ID" :value="1" />
          <v-radio label="Content" :value="0" />
        </v-radio-group>
        <v-text-field
          placeholder="Search"
          v-model="search"
          hide-details
          outlined
          dense
          rounded
          append-icon="mdi-magnify"
          clear-icon="mdi-close"
          clearable
          @click:clear="()=>{search=''; searchAction();}"
          @keyup="searchAction"
        />

        <v-menu
          v-model="menu"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="date"
              label="Search Date"
              append-icon="mdi-calendar"
              class="ms-2"
              readonly
              outlined
              rounded
              dense
              hide-details=""
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker v-model="date" @input="getDate"></v-date-picker>
        </v-menu>

        <v-spacer />
        <slot name="ColumnButton" />

      </v-row>
    </v-card> -->

</template>

<script>
import IconButton from "../common/buttons/IconButton.vue";
import CustomCard from "../common/CustomCard.vue";
import CustomizeColumn from "../customizeColumn/CustomizeColumn.vue";
import CustomButton from '../design/components/CustomButton.vue';
export default {
  name: "LogsFilterAction",
  components: { CustomCard, IconButton, CustomizeColumn, CustomButton },
  props: {
    filename: {
      type: String,
      require: true,
    },
    searchLogs: {
      type: Function,
      require: true,
    },
    searchLogByDate: {
      type: Function,
      require: true,
    },
  },
  data() {
    return {
      date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
      menu: false,
      form:{
        search: "",
        selectedOption: 1, },
      columnDialog: false,
    };
  },

  methods: {
    searchAction() {
      // if (this.selectedOption == 0) {
      //   this.$emit("searchLogs", this.search.);
      // }
      // if (this.selectedOption == 1) {
      //   this.$emit("searchById", this.search,);
      // }
      this.$emit("searchLogs", this.form);

    },
    filterBySystem(value){
      this.$emit("filterBySystem",value);
    },
    getDate() {
      this.menu = false;
      this.$emit("searchLogByDate", this.date);
    },
    toggleColumnDialog() {
      this.columnDialog = !this.columnDialog;
    },
    changeType(){
      this.form.search=""
      this.searchAction()
       this.$emit("onTypeChange");

    }
  },
};
</script>

<style scoped>
.title,
.title2 {
  font-size: 1rem !important;
  line-height: 1rem !important;
}

.radio-label {
  font-size: 1rem;
  font-weight: 500;
}
</style>
