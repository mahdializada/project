<template>

  <div class="text-center">
  <v-dialog
		v-if="showDialog"
		v-model="showDialog"
		width="800"
		persistent
		style="height:400px !important;"
	>
    <v-card style="border-radius:0px !important">
      <v-card-title class="px-2 pt-2 pb-2" style="color: #777">
				<div class="dialog-title d-flex align-center">
					{{ $tr("bank_details") }}
					<div
						class="remarks-number ms-1"
						:style="`background: ${$vuetify.theme.defaults.light.primary}33`"
					></div>
				</div>
				<v-spacer></v-spacer>
				<div class="d-flex align-center">
					<v-btn
						class="me-1"
						fab
						dark
						x-small
						color="primary"
						@click="
							toggleInsertion()
						"
					>
						<v-icon dark size="20"> {{expand1 ?'mdi-minus':'mdi-plus'}} </v-icon>
					</v-btn>
					<svg
						@click="toggleDialog()"
						width="42px"
						height="42px"
						viewBox="0 0 700 560"
						fill="currentColor"
						style="transform: scaleY(-1); cursor: pointer"
					>
						<g>
							<path
								d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z"
							/>
							<path
								d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z"
							/>
						</g>
					</svg>
				</div>
			</v-card-title>
      
    </v-card>
    <v-card-text  style="background-color:#F6F8FC !important; height:580px !important; overflow-y:scroll; overflow-x:hidden">
      <v-expand-transition style="background-color: white !important;">
          <v-card class="pb-3 pt-1 px-3" style="position:relative !important; z-index:100;" v-if="expand1">
        
      <v-form ref="bankformref" lazy-validation @submit.prevent="submit()">
          
        
        <v-card-text 
            style="background-image: linear-gradient(to bottom, #2E7BE6, #00b3ff) !important;
           border-radius: 23px !important;">
        <v-row class="pb-0">
          <v-spacer></v-spacer>
          <v-avatar class="me-5 pa-4" style="background-color:#c3bfbf82 !important;" ><v-icon class="white--text " size="35px">{{'mdi-bank'}}</v-icon></v-avatar>
        </v-row>  
        <v-row class="mt-0">
          <v-col cols="12 py-0">
            <label for="" class="white--text label-size " >{{$tr('label_required',$tr('card_number'))}}</label>
            <v-text-field
            solo
            class="mt-1"
            v-model="$v.bank.card_number.$model"
            type="number" 
            :rules="
              validateRule(
                $v.bank.card_number,
                $root,
                $tr('card_number')
              )
            "
            :placeholder="$tr('card_number')"
            prepend-inner-icon="mdi-credit-card-settings"
            ></v-text-field>
          </v-col>
          <v-col cols="6 pt-0 pe-1">
            <label for="" class="white--text label-size " >{{$tr('label_required',$tr('owner'))}}</label>
            <v-text-field
            solo
            v-model="$v.bank.owner.$model"
            class="mt-1"
            :rules="
              validateRule(
                $v.bank.owner,
                $root,
                $tr('owner')
              )
            "
            :placeholder="$tr('owner')"
            prepend-inner-icon="mdi-account"
            ></v-text-field>
          </v-col>
          <v-col cols="6 pt-0 ps-1">
            <label for="" class="white--text label-size " >{{$tr('label_required',$tr('bank_name'))}}</label>
            <v-text-field
            solo
            class="mt-1"
            v-model="$v.bank.bank_name.$model"
            :rules="
              validateRule(
                $v.bank.bank_name,
                $root,
                $tr('bank_name')
              )
            "
            :placeholder="$tr('bank_name')"
            prepend-inner-icon="mdi-bank"
         
          ></v-text-field>
          </v-col>
        </v-row>
        </v-card-text>
        <v-divider></v-divider>

        <v-card-actions class="mt-2">
          <v-spacer></v-spacer>
         
          <v-btn
         depressed
         color="primary"
         type="submit"
         :loading="load"
         > Done</v-btn>
        </v-card-actions>
      </v-form>
      </v-card>
      </v-expand-transition>
      <v-card-text class="overflow-auto my-2 mr-3" >
					<div >
					<div class="px-5" v-if="loading">
						<v-skeleton-loader
              
							v-for="i in 2"
							:key="i"
							type=" list-item-avatar, list-item-three-line"
						></v-skeleton-loader>
					</div>
						<v-timeline
							dense
							
							:class="`custom-timeLine`"
							color="red"
              v-else-if="items.length>0 && !loading"
						>
							<v-timeline-item
								v-for="(item,i) in items" :key="i"
								color="primary"
								icon="mdi-book"
							>
								<template v-slot:icon>
									<v-badge avatar bordered overlap bottom>
										<template v-slot:badge>
											<v-avatar>
											<v-icon color="white" size="15">mdi-book</v-icon>

											</v-avatar>
										</template>

										<v-avatar size="30">
											<v-icon color="white" size="15">mdi-book</v-icon>
										</v-avatar>
									</v-badge>
								</template>
								<div class="d-flex py-2 ps-1" >
									<div class=" ">
											<h4>
											{{item.bank_name}}
										</h4>

										<span class="text-caption">
                        {{item.user.username}}
										</span>
                    
									</div>
									<v-chip
										class="mx-2 px-1 primary--text"
										color="blue lighten-5"
										small
										label
									>
										{{item.owner}}
									</v-chip>
									<v-spacer></v-spacer>
									<div class="px-1">
										<div class="text-body-1 font-weight-regular green--text">
											**** **** ****{{item.card_number.substr(item.card_number.length-4,4)}}
										</div>

										<div class="text-caption text-right">
										  {{item.created_at}}
										</div>
									</div>
									<div>
										<v-menu bottom left>
											<template v-slot:activator="{ on, attrs }">
												<v-icon color="primary" v-bind="attrs" v-on="on"
													>mdi-dots-horizontal</v-icon
												>
											</template>
											<v-list>
												<v-list-item @click="update(item)">
													<v-list-item-icon class="grey--text" >
														<v-icon v-text="'mdi-pencil'" size="20"></v-icon>
													</v-list-item-icon>
													<v-list-item-title class="text-caption" >{{
														$tr("update")
													}}</v-list-item-title>
												</v-list-item>
												<v-list-item @click="deleted(item.id)">
													<v-list-item-icon class="grey--text">
														<v-icon v-text="'mdi-delete'" size="20"></v-icon>
													</v-list-item-icon>
													<v-list-item-title class="text-caption">{{
														$tr("delete")
													}}</v-list-item-title>
												</v-list-item>
											</v-list>
										</v-menu>
									</div>
								</div>
								<v-divider></v-divider>
							</v-timeline-item>
						</v-timeline>

						<div  class="text-center" v-else>
							<NoRemark
								style="margin-top: 150px"
							/>
						</div>
					</div>

				</v-card-text>
    </v-card-text>
  </v-dialog>
  </div>
</template>
<script>  
import CTextField from '../../new_form_components/Inputs/CTextField.vue';
import GlobalRules from "~/helpers/vuelidate";
import Validations from "../../../validations/validations";
import NoRemark from "../remarks/NoRemark.vue";
export default {
  validations:{
    bank:{
      card_number:Validations.bankNumberValidation,
      owner:Validations.name100Validation,
      bank_name:Validations.name100Validation,
    }
  },
    data() {
        return {
            showDialog: false,
            validateRule: GlobalRules.validate,
            bank:{
              card_number: null,
              owner: "",
              bank_name:'',
            },
            add_account_id:'',
            load:false,
            loading:false,
            items:[],
            expand1:false,
            isEdit:'',
        };
    },
    methods: {
      toggleInsertion(){
        this.expand1=! this.expand1;
        this.clearForm();
      },
      toggleBankModal(items1) {
            this.add_account_id=items1.id;
            this.showDialog = true;
            this.fetchAllData();
        },
      clearForm(){
        this.bank={
             card_number: null,
              owner: "",
              bank_name:'',
        }
      }, 
      async fetchAllData() {
        this.loading=true;
        const data= await this.$axios.get(`advertisement/bank-account/${this.add_account_id}`);
        this.loading=false;
        this.items=data.data;
		},
    async deleted(id){
      this.$TalkhAlertNA(
				"Are you sure ?", //text
				"delete", //icon
				async () => {
          const data= await this.$axios.delete(`advertisement/bank-account/${id}`);
          if(data.status==200){
            this.items=this.items.filter((item)=>item.id != id);
          this.$toasterNA("green", this.$tr("successfully_deleted"));
          }else{
            this.$toasterNA("red", this.$tr("something_want_wrong"));
          }
				}, // callback function
				"Yes", // btn text
			);
    },
     update(editItem){
        this.expand1=true;
        this.bank={
          card_number:editItem.card_number,
          owner:editItem.owner,
          bank_name:editItem.bank_name,
        }
          this.isEdit=editItem.id;
          console.log(this.isEdit);
    },
      async submit(){
        try {
          this.$refs.bankformref.validate();
          this.$v.bank.$touch();
          if(
            this.$v.bank.card_number.$invalid||
            this.$v.bank.owner.$invalid||
            this.$v.bank.bank_name.$invalid
            ){
              return false;
            }
            let data1={
              ...this.bank,
              add_account_id:this.add_account_id,
              user_id:this.$auth.user.id,
            }
            this.load=true;
            console.log(this.isEdit);
            if(this.isEdit){
              const data= await this.$axios.put(`advertisement/bank-account/${this.isEdit}`,data1);
              this.isEdit='';
            }else{
              const data= await this.$axios.post('advertisement/bank-account',data1);
            }
          this.load=false;
          this.fetchAllData();
          this.toggleInsertion();
        } catch (error) {
          this.$toasterNA("red", this.$tr('something_went_wrong'));
          this.load=false;
        }
      },  
      async toggleDialog() {
			this.showDialog = !this.showDialog;
      this.expand1=false;
      this.clearForm();
		},
    },
    created(){
    },
    components: { CTextField , NoRemark}
};
</script>
<style scoped>
  .CTextField:active{
    background-color: red !important;
  }
  .label-size{
    font-size: 16px  !important;
    margin-bottom: 5px !important;
  }
 v-text-field{
  border:1px solid green !important;  
 }
</style>