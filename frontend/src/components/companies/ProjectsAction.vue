<template>
  <CustomCard class="pa-1">
    <h3 class="mx-2">Actions</h3>
    <!--  project Update Dialog Section -->
    <v-dialog v-model="projectUpdateDialog" persistent max-width="1300">
      <UpdateProject
        @close="toggleProjectUpdateDialog"
        :editData="this.selected"
      />
    </v-dialog>
    <v-row align="center" justify="center" align-content="center" class="mx-1">
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
  </CustomCard>
</template>

<script>
import IconButton from "../common/buttons/IconButton";
import CustomCard from "../common/CustomCard";
import ProgressBar from "../common/ProgressBar.vue";
import alert from "../../helpers/alert";
import { mapGetters, mapActions } from "vuex";
import UpdateProject from "./UpdateProject";
export default {
  name: "ProjectAction",
  components: { CustomCard, IconButton, ProgressBar, UpdateProject },
  props: ["selected"],
  data() {
    return {
      actions: [
        // {
        //   icon: "mdi-eye",
        //   text: "View",
        //   color: "primary",
        //   onClick: this.onView,
        // },
        {
          icon: "mdi-account-edit",
          text: "Edit",
          color: "info",
          onClick: this.toggleProjectUpdateDialog,
        },
        // {
        //   icon: "mdi-auto-fix",
        //   text: "Auto Edit",
        //   color: "info",
        //   onClick: this.onEdit,
        // },
        {
          icon: "mdi-delete",
          text: "Delete",
          color: "error",
          onClick: this.onDelete,
        },
      ],
      projectUpdateDialog: false,
      editData: [],
      accessibility: "",
      showProgressBar: false,
      accessibilityItems: [
        { id: "active", name: "Active" },
        { id: "inactive", name: "Deactive" },
      ],
    };
  },
  methods: {
    ...mapActions({
      updateProjectsStore: "projects/updateProjects",
      deleteProjectsStore: "projects/deleteProjects",
    }),
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
              this.$toasterNA("red", this.$tr("something_went_wrong"));
            }
            this.showProgressBar = false;
          },
          "swal_remove_text",
          "error"
        );
      } else {
        this.$toasterNA("red", this.$tr("cant_delete_more_item"));
      }
    },
    toggleProjectUpdateDialog() {
      if (this.selected.length == 1) {
        this.projectUpdateDialog = !this.projectUpdateDialog;
      }
      else {
        alert.updateLimitAlert(this);
      }
    },
  },
};
</script>

<style scoped>
.v-menu__content {
  top: 445px !important;
}
</style>
