<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="700">
      <v-card>
        <v-card-title class="text-h5">
          <div class="d-flex">{{ $tr('change_status') }} <small style="font-size:15px">&nbsp; + {{ $tr('add_item',$tr('permission'))}}</small></div>
          <div class="ml-auto">
            <close-btn @click="closeDialog" />
          </div>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text
          class="mt-2"
          style="padding-left: 20px; padding-right: 20px"
        >
          <div class="box-style d-flex">
            <div class="d-flex">
              <v-avatar color="primary" class="mr-2" size="60">
                <v-icon color="white" style="font-size: xx-large"
                  >mdi-account</v-icon
                >
              </v-avatar>
            </div>
            <div class="mt-1 ms-4" style="font-size: 15px;color: black">
              {{ $tr('choose_user_give_permissions_to_company') }}
            </div>
          </div>
          <div class="mt-2">
            <div class="w-full d-flex mt-2">
              <InputCard
                :title="$tr('select_user_optional')"
                :hasSearch="true"
                :hasPagination="false"
                @search="(v) => (searchUser = v)"
              >
                <HorizontalItemContainer
                  v-model="selected_user"
                  :logoName="'image'"
                  :item_text="'fullname'"
                  :items="filterUser"
                  :loading="isFetchingUsers"
                  :no_data="filterUser.length < 1 && !isFetchingUsers"
                  :multi="true"
                  height="150px"
                  
                ></HorizontalItemContainer>
              </InputCard>
            </div>
          </div>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions class="py-2">
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="closeDialog">{{ $tr('cancel') }}</v-btn>
          <v-btn color="primary" :loading="isSubmitUser" @click="submit">{{ $tr('change') }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import CloseBtn from "../../design/Dialog/CloseBtn.vue";
import HorizontalItemContainer from "../cat_product_selection/HorizontalItemContainer.vue";
import CSelectMulti from "../Inputs/CSelectMulti.vue";
import InputCard from "./InputCard.vue";
export default {
  components: { CloseBtn, CSelectMulti, InputCard, HorizontalItemContainer },
  data() {
    return {
      dialog: false,
      users: [],
      selected_user:[],
      isFetchingUsers:false,
      selected_item:[],
      searchUser:false,
      isSubmitUser:false,
    };
  },
  computed:{
    filterUser() {
      if (this.searchUser) {
        const filter = (item) =>
          item?.fullname
            ?.toLowerCase()
            ?.includes(this.searchUser?.toLowerCase());
        return this.users.filter(filter);
      }
      return this.users;
    },
  },
  methods: {
    reset() {
      this.users = [];
      this.dialog = false;
      this.isSubmitUser = false
      this.selected_item = [];
      this.selected_user = [];
    },
    toggleDialog(item) {
      this.selected_item = item;
      this.dialog = !this.dialog;
      this.fetchUsers();
    },
    async submit(){
      this.isSubmitUser = true;
        const data = {
          company_id:this.selected_item.id,
        }
        if(this.selected_user.length>0)
          data['users'] = this.selected_user;
        const res = await this.$axios.post('companies/change-status/add-users',data);
        if (res.status == 200) {
          this.reset();
          this.$toasterNA('green', this.$tr("successfully", this.$tr('change_status')));
        }else{
          this.isSubmitUser = false
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
        
    },
    closeDialog() {
      this.dialog = false;
    },
    async fetchUsers() {
			try {
				this.isFetchingUsers = true;
				const { data } = await this.$axios.get(
					"all/users"
				);
				this.users = data
        this.users = this.users.map((item)=>{
          item.fullname = item.firstname+' '+item.lastname
          return item
        })
			} catch (error) {}
			this.isFetchingUsers = false;
		},
  },
};
</script>

<style scoped>
.box-style {
  height: 100px;
  padding: 16px;
  background-color: #f5f5f5;
  border-radius: 15px;
}
.style {
  padding: 0px !important;
}
</style>
