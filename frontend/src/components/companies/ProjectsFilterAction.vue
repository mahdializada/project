<template>
  <CustomCard class="py-0 pt-1">
    <!--  Column Filtration Dialog  -->
    <v-dialog v-model="columnDialog" persistent max-width="1100">
      <customize-column :headers="headers" @closeDialog="toggleColumnDialog" />
    </v-dialog>

    <!--  project Creation Dialog Section -->
    <v-dialog v-model="projectRegisterDialog" persistent max-width="1300">
      <RegisterProject @close="toggleProjectRegisterDialog" />
    </v-dialog>

    <h3 class="mx-2">Search & Filter</h3>
    <v-row class="mx-3 mt-2"> Select Option </v-row>

    <v-row class="mx-3" align="center">
      <v-radio-group row class="my-1 mb-0" v-model="selectedOption">
        <v-radio label="ID" :value="1" />
        <v-radio label="Name" :value="0" />
      </v-radio-group>

      <v-text-field
        placeholder="Search"
        outlined
        dense
        rounded
        append-icon="mdi-magnify"
        clear-icon="mdi-close"
        hide-details
        clearable
        style="max-width: 400px"
        class="mx-1"
      />

      <!-- <IconButton
        v-for="filterButton in filterButtons"
        :key="filterButton.icon"
        outlined
        :color="filterButton.color"
        :icon="filterButton.icon"
        :text="filterButton.text"
        @click="filterButton.onClick"
      /> -->
      <!--  project Update Dialog Section -->
      <v-dialog v-model="projectUpdateDialog" persistent max-width="1300">
        <UpdateProject
          @close="toggleProjectUpdateDialog"
          :editData="this.selected"
          :key="registerKey"
        />
      </v-dialog>
      <v-row
        class="mx-1"
      >
        <IconButton
          v-for="action in actions"
          :key="action.icon"
          :color="action.color"
          @click.stop="action.onClick"
          :icon="action.icon"
          :text="action.text"
          :disabled="selected.length == 0"
        />
      </v-row>
      <progress-bar v-if="showProgressBar" />

      <v-spacer />

      <IconButton
        v-for="actionButton in actionButtons"
        :key="actionButton.icon"
        :color="actionButton.color"
        :icon="actionButton.icon"
        :text="actionButton.text"
        @click="actionButton.onClick"
      />
    </v-row>
  </CustomCard>
</template>

<script>
import IconButton from "../common/buttons/IconButton";
import TextField from "../common/TextField";
import CustomizeColumn from "../customizeColumn/CustomizeColumn";
import RegisterProject from "./RegisterProject";
import CustomCard from "../common/CustomCard";
import UpdateProject from "./UpdateProject";
import { mapGetters, mapActions } from "vuex";
import alert from "../../helpers/alert";
import ProgressBar from "../common/ProgressBar.vue";

export default {
  name: "ProjectAction",
  components: {
    CustomCard,
    RegisterProject,
    CustomizeColumn,
    TextField,
    IconButton,
    UpdateProject,
    ProgressBar,
  },
  props: {
    headers: {
      type: Array,
      require: true,
    },
    selected: {
      type: Array,
    }
  },
  data() {
    return {
      actions: [
        {
          icon: "mdi-account-edit",
          text: "edit",
          color: "info",
          onClick: this.toggleProjectUpdateDialog,
        },
        {
          icon: "mdi-delete",
          text: "delete",
          color: "error",
          onClick: this.onDelete,
        },
      ],
      projectUpdateDialog: false,
      editData: [],
      accessibility: "",
      showProgressBar: false,
      selectedOption: 1,
      registerKey: 1,
      columnDialog: false,
      projectRegisterDialog: false,
      // filterButtons: [
      //   {icon: 'mdi-filter', text: 'Filter', color: 'primary', onClick: this.onFilter},
      // ],
      actionButtons: [
        {
          icon: "mdi-account-plus",
          text: "Create Company",
          color: "primary",
          onClick: this.toggleProjectRegisterDialog,
        },
      ],
    };
  },

  methods: {
    ...mapActions({
      updateProjectsStore: "projects/updateProjects",
      deleteProjectsStore: "projects/deleteProjects",
    }),
    onFilter() {
      alert("Filter button clicked");
    },

    toggleColumnDialog() {
      this.columnDialog = !this.columnDialog;
    },

    onDownload() {
      alert("download button clicked");
    },
    async onDelete() {
      if (this.selected.length == 1) {
        await alert.removeDialogAlert(
          this,
          async () => {
            this.showProgressBar = true;
            try {
              var table_ids = this.selected.map((table) => table.id);
              var response = await this.$axios.delete("/projects/" + table_ids);
              if (response.status === 204) {
                alert.removedSuccessAlert(this, this.$tr("removed_title"));
                this.deleteProjectsStore(table_ids);
                table_ids = [];
                this.$emit("update:selected", []);
              }
            } catch (error) {
              // this.$toasterNA("red", this.$tr('something_went_wrong'));
        this.$toasterNA("red", this.$tr("something_went_wrong"));

            }
            this.showProgressBar = false;
          },
          "swal_remove_text",
          "error"
        );
      } else {
        this.$toasterNA('red', this.$tr('cant_delete_more_item'))
      }
    },
    toggleProjectUpdateDialog() {
      if (this.selected.length == 1) {
        this.registerKey++
        this.projectUpdateDialog = !this.projectUpdateDialog;
      } else {
        alert.updateLimitAlert(this);
      }
    },

    toggleProjectRegisterDialog() {
      this.projectRegisterDialog = !this.projectRegisterDialog;
    },
  },
};
</script>

<style scoped>
</style>
