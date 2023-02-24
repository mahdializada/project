<template>
  <v-dialog
  v-model="showAlert"
  :width="`${hasStatus?'580':'780'}`"
  persistent
>
  <v-card>
    <v-card-text
      class="blue lighten-5 pt-2"
      :style="`
        background-color: rgba(17, 85, 152, 5%) !important; 
        height:${hasStatus?'350px':'420px'};
        overflow-y: hidden;
      `"
    >
        <lottie-player
          v-if="loading"
          src="/assets/man-on-rocket.json"
          background="transparent"
          speed="1"
          loop
          autoplay
          class="py-2 rounded"
          style="background-color: #f7f8fc;"
        >
        </lottie-player>
        <div v-else class="">
        	<InputCard :title="$tr('are_you_sure_to', $tr(textDialog))" >
            <CheckBoxHorizontalItem
              v-model="newStatus"
              :items="itemStatus.filter((item)=>!item.notAllowed)"
              :height="hasStatus?'210px':'auto'"
              hasCustomoColor
            ></CheckBoxHorizontalItem>
          </InputCard>
        </div>
      </v-card-text>
      <v-card-actions class="justify-end my-1">
        <v-btn class="px-2" depressed @click="close">{{ $tr("cancel") }}</v-btn>
        <v-btn
          depressed
          class="friqiBase white--text ms-2 px-2"
          @click="chaneStatus"
          :loading="loading"
          >{{ $tr(acceptbBtnTxt) }}</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<script>
import CheckBoxHorizontalItem from '../new_form_components/cat_product_selection/CheckBoxHorizontalItem.vue';
import InputCard from '../new_form_components/components/InputCard.vue';

export default {
    props: {
        acceptbBtnTxt: {
            type: String,
            default: "change",
        },
        textDialog: {
            type: String,
            default: "change_status",
        },
    },
    data() {
        return {
          newStatus:"",
          hasStatus:false,
            itemStatus: [
                // {
                //     id: "new_item",
                //     name: "new_item",
                //     color: "#007f35",
                //     notAllowed:true,
                //   },
                //   {
                //     id: "in_study",
                //     name: "in_study",
                //     color: "#37383a",
                //     notAllowed:true,
                //   },
                //   {
                //     id: "create_content",
                //     name: "create_content",
                //     color: "#fa8059",
                //     notAllowed:true,
                //   },
                //   {
                //     id: "content_review",
                //     name: "content_review",
                //     color: "#8a53fd",
                //     notAllowed:true,
                //   },
                  {
                    id: "ready_to_sale",
                    name: "ready_to_sale",
                    color: "#0049b0",
                    notAllowed:true,
                  },
                  {
                    id: "in_creation",
                    name: "in_creation",
                    color: "#767171",
                    notAllowed:true,
                },
                {
                    id: "in_testing",
                    name: "in_testing",
                    color: "#5267b6",
                    notAllowed:true,
                },
                {
                    id: "in_sales",
                    name: "in_sales",
                    color: "#09d5a2",
                    notAllowed:true,
                  },
                  {
                    id: "waiting_for_md",
                    name: "waiting_for_md",
                    color: "#e08ea5",
                    notAllowed:true,
                  },
                  {
                    id: "short_stop",
                    name: "short_stop",
                    color: "#fc9b24",
                    notAllowed:true,
                  },
                  {
                    id: "final_stop",
                    name: "final_stop",
                    color: "#fe1619",
                    notAllowed:true,
                  },
                  {
                    id: "retesting",
                    name: "retesting",
                    color: "#450e50",
                    notAllowed:true,
                },
                  {
                    id: "cancel",
                    name: "cancel",
                    color: "#fe58bc",
                    notAllowed:true,
                },
                  {
                    id: "remove",
                    name: "remove",
                    color: "#bb732b",
                    notAllowed:true,
                },
            ],
            items: [],
            showAlert: false,
            loading: false,
            change_status_icon: `
              <svg width="46" height="57" viewBox="0 0 46 57" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_73_61)">
              <path d="M41.6747 0.601524C41.3652 6.82006 36.3141 11.6963 30.1891 11.6907H15.8136C9.68868 11.6963 4.63479 6.82006 4.32799 0.601524C1.69833 1.76002 0 4.39725 0 7.30741V49.6926C0.00821771 53.725 3.22134 56.9889 7.18776 57H38.815C42.7814 56.9916 45.9918 53.725 46.0027 49.6926V7.30741C46.0027 4.39447 44.3044 1.76002 41.6747 0.59874V0.601524ZM35.4649 28.5278L21.8208 42.4019C21.1469 43.0897 20.232 43.4741 19.2787 43.4713C18.3255 43.4713 17.4133 43.0842 16.7367 42.4019L10.5351 36.097C9.13261 34.6684 9.13261 32.357 10.5351 30.9284C11.209 30.2405 12.1239 29.8562 13.0771 29.859C14.0304 29.859 14.9453 30.2433 15.6191 30.9284L19.2787 34.6489L30.3836 23.3592C31.0575 22.6713 31.9724 22.287 32.9256 22.2898C33.8789 22.2898 34.7938 22.6741 35.4649 23.3592C36.1415 24.0415 36.5195 24.9744 36.5168 25.9435C36.5168 26.9126 36.1415 27.8428 35.4649 28.5278Z" fill="#105698"/>
              <path d="M30.1892 7.30462H15.8137C11.8473 7.29627 8.63419 4.03244 8.62598 0H37.377C37.3688 4.03244 34.1557 7.29905 30.1892 7.30741V7.30462Z" fill="#B6C6DA"/>
              </g>
              <defs>
              <clipPath id="clip0_73_61">
              <rect width="46" height="57" fill="white"/>
              </clipPath>
              </defs>
              </svg>`,
        };
    },
    methods: {
        async toggleStatusDialog(item) {
          if(item.item_status?.item_status)
          this.hasStatus = true;
          else
          this.hasStatus = false;
          
          await this.changeAbleStatus(item.item_status?.item_status);
          this.newStatus = item.item_status?.item_status??'';
            this.items = {
                id: item.id,
                item_code: item.pcode?? item.product_code,
                country_id: item.country_id,
                prev_status: item.item_status?.item_status
            };
            this.showAlert = true;
        },
        chaneStatus() {
          if(this.items.prev_status == this.newStatus){
              this.$toasterNA("orange", this.$tr("Record is already in "+this.newStatus));
              return;
            }
            this.loading = true;
            const status1 = this.itemStatus.find((i)=>i.id == this.newStatus);
            this.items['item_status'] = {
              status:status1.id,
              color:status1.color,
            };
            this.$emit("changeStatus", this.items);
        },
        close() {
            this.loading = false;
            this.showAlert = false;
        },
        reset() {
            this.loading = false;
        },  
        changeAbleStatus(status){
            switch (status) {
              // case 'new_item':
              // this.itemStatus = this.itemStatus.map((item)=>{
              //   if(item.id == status || item.id == 'in_study')
              //       item.notAllowed = false;
              //   else 
              //       item.notAllowed = true;
              //       return item;
              //     });
              //   break;
              // case 'in_study':
              // this.itemStatus = this.itemStatus.map((item)=>{
              //   if(item.id == status || item.id == 'create_content')
              //       item.notAllowed = false;
              //   else 
              //       item.notAllowed = true;
              //       return item;
              //     });
              //   break;
              // case 'create_content':
              // this.itemStatus = this.itemStatus.map((item)=>{
              //   if(item.id == status || item.id == 'content_review')
              //       item.notAllowed = false;
              //   else 
              //       item.notAllowed = true;

              //       return item;
              //     });
              //   break;
              // case 'content_review':
              // this.itemStatus = this.itemStatus.map((item)=>{
              //   if(item.id == status || item.id == 'ready_to_sale')
              //       item.notAllowed = false;
              //   else 
              //       item.notAllowed = true;
              //       return item;
              //     });
              //   break;
              case 'ready_to_sale':
              this.itemStatus = this.itemStatus.map((item)=>{
                if(item.id == status || item.id == 'in_creation')
                    item.notAllowed = false;
                else 
                    item.notAllowed = true;
                    return item;
                  });
                break;
              case 'in_creation':
              this.itemStatus = this.itemStatus.map((item)=>{
                if(item.id == status || item.id == 'in_testing')
                    item.notAllowed = false;
                else 
                    item.notAllowed = true;
                    return item;
                  });
                break;
              case 'in_testing':
              this.itemStatus = this.itemStatus.map((item)=>{
                if(item.id == status || item.id == 'in_sales' || item.id == 'waiting_for_md')
                    item.notAllowed = false;
                else 
                    item.notAllowed = true;
                    return item;
                  });
                break;
              case 'in_sales':
              this.itemStatus = this.itemStatus.map((item)=>{
                if(item.id == status || item.id == 'waiting_for_md')
                    item.notAllowed = false;
                else 
                    item.notAllowed = true;
                    return item;
                  });
                break;
              case 'waiting_for_md':
              this.itemStatus = this.itemStatus.map((item)=>{
                if(item.id == status || item.id == 'short_stop' || item.id == 'final_stop' || item.id == 'retesting' || item.id == 'cancel' || item.id == 'remove')
                    item.notAllowed = false;
                else 
                    item.notAllowed = true;
                    return item;
                  });
                break;
              case 'short_stop':
              this.itemStatus = this.itemStatus.map((item)=>{
                if(item.id == status || item.id == 'retesting' || item.id == 'waiting_for_md')
                    item.notAllowed = false;
                else 
                    item.notAllowed = true;
                    return item;
                  });
                break;
              case 'final_stop':
              this.itemStatus = this.itemStatus.map((item)=>{
                if(item.id == status  || item.id == 'retesting' || item.id == 'waiting_for_md')
                    item.notAllowed = false;
                else 
                    item.notAllowed = true;
                    return item;
                  });
                break;
              case 'retesting':
              this.itemStatus = this.itemStatus.map((item)=>{
                if(item.id == status || item.id == 'in_sales' || item.id == 'waiting_for_md')
                    item.notAllowed = false;
                else 
                    item.notAllowed = true;
                    return item;
                  });
                break;
              case 'cancel':
              this.itemStatus = this.itemStatus.map((item)=>{
                if(item.id == status || item.id == 'waiting_for_md')
                    item.notAllowed = false;
                else 
                    item.notAllowed = true;
                    return item;
                  });
                break;
              case 'remove':
              this.itemStatus = this.itemStatus.map((item)=>{
                if(item.id == status || item.id == 'waiting_for_md')
                    item.notAllowed = false;
                else 
                    item.notAllowed = true;
                    return item;
                  });
                break;
            
              default:
                this.itemStatus = this.itemStatus.map((item)=>{
                  item.notAllowed = false;
                  return item;
                })
                break;
            }

        }
    },
    components: { InputCard, CheckBoxHorizontalItem }
};
</script>
<style>
.AvatarCustom {
  background: linear-gradient(
    360deg,
    rgba(17, 85, 152, 15%) 0%,
    rgba(17, 85, 152, 1%) 100%
  ) !important;
  width: 88px;
  height: 85px;
  display: flex;
  justify-content: center;
  border-radius: 50%;
}
</style>
