<template>
  <v-menu
    v-model="menu2"
    :close-on-content-click="true"
    transition="scale-transition"
    max-width="auto"
    min-width="auto"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-text-field
        v-model="getDate"
        filled
        dense
        v-bind="attrs"
        placeholder="Select date"
        v-on="on"
        @blur="parseDate(getDate)"
      ></v-text-field>
    </template>
    <div class="h-full d-flex align-center-justify-center mt-5">
      <div class="w-full">
        <v-date-picker
          class="pt-1"
          v-model="date1"
          prev-icon="mdi-skip-previous"
          next-icon
          no-title
          @input="menu2 = false"
        ></v-date-picker>
      </div>
      <div class="w-full">
        <v-date-picker
          class="pt-1"
          v-model="date2"
          prev-icon
          no-title
          @input="menu2 = false"
        ></v-date-picker>
      </div>
      <div class="w-full">
        <template>
          <v-card style="box-shadow: none" class="align-right pt-1">
            <v-list flat>
              <v-list-item-group v-model="model" color="indigo">
                <v-list-item v-for="(item, i) in items" :key="i">
                  <v-list-item-content @click="changeDate(item)">
                    <v-list-item-title v-text="item"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </v-card>
        </template>
      </div>
    </div>
  </v-menu>
</template>

<script>
export default {
    
  data() {
    return {
      date1: new Date(new Date().valueOf() - 1000 * 60 * 60 * 24)
        .toISOString()
        .substr(0, 10),
      date2: new Date().toISOString().substr(0, 10),
      getDate:"",
      menu2: false,
      listedDate: false,
      model: 1,
       items: [
        "Lifetime",
        "Today",
        "One day ago",
        "Three days ago",
        "One week ago",
        "One month ago",
      ],
    };
  },
  created() {
    this.getDate = [this.date1, this.date2];
  },
  watch: {
      getDate: function () {
        
        this.$emit('getDate',this.getDate);
      },
    date1: function () {
      this.getDate = [this.date1, this.date2];
    },
    date2: function () {
      this.getDate = [this.date1, this.date2];
    },
  },
  methods: {
    changeDate(item) {
      this.listedDate = true;
      switch (item) {
        case "liftime":
          this.getDate = ["liftime"];
          break;
        case "1 day ago":
          // this.date1 = new Date(new Date().valueOf() - 1000 * 60 * 60 * 24 * 2)
          //   .toISOString()
          //   .substr(0, 10);
          // this.date2 = new Date(new Date().valueOf() - 1000 * 60 * 60 * 24)
          //   .toISOString()
          //   .substr(0, 10);
          this.getDate = ["1 day ago"];
          break;
        case "3 days ago":
          this.getDate = ["3 days ago"];
          break;
        case "1 week ago":
          this.getDate = ["1 week ago"];
          break;
        case "1 nounth ago":
          this.getDate = ["1 mount ago"];
          break;
        case "today":
          this.getDate = ["today"];
          break;
      }
    },
    parseDate(date) {
        if (!this.listedDate) {
            if (!Array.isArray(date)) {
                date = date.split(",");
        }
        this.date1 = date[0];
        this.date2 = date[1];
        this.listedDate = false;
      }
    },
  },
};
</script>

<style scoped>
.v-btn--icon.v-size--default{
    display: none!important;;
}
</style>>
</style>