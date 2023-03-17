<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="900">
      <v-card>
        <v-form @submit.prevent="submit">
            <dialog @closeDialog="closeDialog"/>
          <custom-stepper
            :CustomHeight="'60'"
            @submit="submit"
            @onNext="onNext"
            :canNext="true"
            :headers="headers"
          >
            <template v-slot:step1>
              <div class="w-full d-flex mt-2">
                <InputCard
                  :title="$tr('select_user_optional')"
                  :hasSearch="true"
                  :hasPagination="false"
                  @search="(v) => (searchUser = v)"
                  :px="'0'"
                  :py="'0'"
                >
                  <HorizontalItemContainer
                    v-model="selected_user"
                    :logoName="'image'"
                    :item_text="'firstname'"
                    :items="filterUser"
                    :loading="isFetchingUsers"
                    :no_data="filterUser.length < 1 && !isFetchingUsers"
                    :multi="true"
                    height="250px"
                  ></HorizontalItemContainer>
                </InputCard>
              </div>
            </template>
            <template v-slot:step2>
              <div class="mb-3">
                <InputCard
                  :title="$tr('project')"
                  :hasSearch="true"
                  :hasPagination="false"
                  @search="(v) => (searchProject = v)"
                  :px="'0'"
                  :py="'0'"
                >
                  <NoCheckboxItemContainer
                    v-model="selected_project"
                    :items="filterProject"
                    :loading="isFetchingprojects"
                    :no_data="filterProject.length < 1 && !isFetchingprojects"
                    height="250px"
                    :nameLogo="'name'"
                    :multi="true"
                  ></NoCheckboxItemContainer>
                </InputCard>
              </div>
            </template>
          </custom-stepper>
        </v-form>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import CustomStepper from "../design/FormStepper/CustomStepper.vue";
import HorizontalItemContainer from "../new_form_components/cat_product_selection/HorizontalItemContainer.vue";
import NoCheckboxItemContainer from "../new_form_components/cat_product_selection/NoCheckboxItemContainer.vue";
import InputCard from "../new_form_components/components/InputCard.vue";
import Dialog from '../design/Dialog/Dialog.vue';

export default {
  components: {
    CustomStepper,
    InputCard,
    HorizontalItemContainer,
    NoCheckboxItemContainer,
    CloseBtn,
    Dialog
},
  data() {
    return {
      isFetchingUsers: false,
      isFetchingprojects: false,
      users: [],
      projects: [],
      selected_user: [],
      dialog: false,
      selected_project: [],
      selected_item: [],
      headers: [
        {
          icon: "fa-user",
          title: "User",
          slotName: "step1",
        },
        {
          icon: "fa-thumbs-up",
          title: "projects",
          slotName: "step2",
        },
        {
          icon: "fa-thumbs-up",
          title: "done",
          slotName: "step2",
        },
      ],
    };
  },
  computed: {
    filterUser() {
      if (this.searchUser) {
        const filter = (item) =>
          item?.firstname
            ?.toLowerCase()
            ?.includes(this.searchUser?.toLowerCase());
        return this.users.filter(filter);
      }
      return this.users;
    },
    filterProject() {
      if (this.searchProject) {
        const filter = (item) =>
          item?.name
            ?.toLowerCase()
            ?.includes(this.searchProject?.toLowerCase());
        return this.projects.filter(filter);
      }
      return this.projects;
    },
  },
  methods: {
    toggleDialog(item) {
        this.selected_item = item;
      this.dialog = !this.dialog;
      this.fetchUsers();
    },
    closeDialog(){
        this.dialog = false;
    },
    onNext() {
      this.fetchProject();
    },
    async fetchUsers() {
      try {
        this.isFetchingUsers = true;
        const { data } = await this.$axios.get("all/users");
        this.users = data;
        this.users = this.users.map((item) => {
          item.fullname = item.firstname + " " + item.lastname;
          return item;
        });
      } catch (error) {}
      this.isFetchingUsers = false;
    },
    async fetchProject() {
      try {
        this.isFetchingprojects = true;
        const { data } = await this.$axios.get("advertisement/get-projects");
        this.projects = data;
      } catch (error) {}
      this.isFetchingprojects = false;
    },
    async submit(){
        this.isSubmitUser = true;
        const data = {
          company_id:this.selected_item.id,
        }
        if(this.selected_user.length>0)
          data['users'] = this.selected_user;
        if(this.selected_project.length>0)
          data['projects'] = this.selected_project;
        const res = await this.$axios.post('companies/change-status/add-users',data);
        if (res.status == 200) {
          this.reset();
          this.$toasterNA('green', this.$tr("successfully", this.$tr('change_status')));
        }else{
          this.isSubmitUser = false
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
    },
    reset() {
      this.users = [];
      this.dialog = false;
      this.isSubmitUser = false
      this.selected_item = [];
      this.selected_user = [];
    },
  },
};
</script>

<style></style>
