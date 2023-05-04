<template>
  <div>
    <v-col cols="12">
      <Head />
    </v-col>

    <v-col cols="12">
      <SearchCountry :download_data="country" @search="search"/>
    </v-col>

    <v-col cols="12">
      <DataTable
        :top="headers"
        :country="country"

        :load="loading"
        @getRecord="getData"
        @changeStatus="changeStatus"
      />
    </v-col>
  </div>
</template>

<script>
import DataTable from "../../components/Common/country/DataTable.vue";
import Head from "~/components/Common/country/Head.vue";
import SearchCountry from "~/components/Common/country/SearchCountry.vue";

export default {
  components: { DataTable, Head,  SearchCountry },
  data() {
    return {
      loading: false,
      country: [],

      headers: [
        {
          text: "ID",
          value: "id",
          width: "10%",
          align: "left",
          sortable: false,
        },
        {
          text: "FLAG",
          value: "iso2",
          width: "10%",
          align: "left",
          sortable: false,
        },
        {
          text: "NAME",
          value: "name",
          width: "10%",
          align: "left",
          sortable: false,
        },
        {
          text: "CAPITAL",
          value: "capital",
          width: "10%",
          align: "left",
          sortable: false,
        },
        {
          text: "NATIVE",
          value: "native",
          width: "10%",
          align: "left",
          sortable: false,
        },
        {
          text: "PHONE CODE",
          value: "phone_code",
          width: "10%",
          align: "left",
          sortable: false,
        },
        {
          text: "CURRENCY",
          value: "currency_name",
          width: "10%",
          align: "left",
          sortable: false,
        },
        {
          text: "CURRENCY SYMBOL",
          value: "currency_symbol",
          width: "100%",
          align: "left",
          sortable: false,
        },
        {
          text: "REGION",
          value: "region",
          width: "10%",
          align: "left",
          sortable: false,
        },
        {
          text: "SUB REGION",
          value: "subregion",
          width: "10%",
          align: "left",
          sortable: false,
        },
        {
          text: "STATUS",
          value: "status",
          width: "10%",
          align: "left",
          sortable: false,
        },
        {
          text: "TIME ZONE",
          value: "timezones",
          width: "10%",
          align: "left",
          sortable: false,
        },
      ],
    };
  },
  created() {
    this.getData();

  },
  methods: {
    async getData(item) {
      this.loading = true;
      const result = await this.$axios.get("country", {
        params: {
          tabKey: item,
        },
      });
      this.country = result.data;
      this.loading = false;
    },
    async changeStatus(item) {
      const status = await this.$axios.put("country/id", item);
      await this.getData(item.tabKey);
    },
    async search(filter){
      this.getData1(filter);
    },
    async getData1(pa) {
      const search = await this.$axios.get("country", { params: pa });
      this.country = search.data;
    },
  },
};
</script>

<style></style>
