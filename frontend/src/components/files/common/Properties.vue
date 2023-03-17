<template>
  <v-card v-if="items" height="100%">
    <v-card-title class="py-1">
      <v-btn icon @click="onCloseProperty">
        <v-icon>mdi-close</v-icon>
      </v-btn>
      <p class="ms-2 grey--text">
        {{ $tr("properties") }}
      </p>
    </v-card-title>
    <v-divider class="mx-3"></v-divider>
    <v-card-text style="height: 93%">
      <!-- header -->
      <PropertyHeader :items="items" />
      <div style="overflow-y: scroll; height: calc(100% - 180px)">
        <!-- descriptions  -->
        <ListItem
          iconalign="align-start"
          icon="mdi-information"
          header="Description"
          class="mt-3 mb-1"
        >
          <template v-slot:rear-icon>
            <span v-if="!items.deleted_at" class="d-flex align-center me-5">
              <v-btn
                @click="toggleDescription"
                icon
                color="primary"
                outlined
                small
              >
                <v-icon size="21">
                  {{ desriptionInput ? "mdi-close" : "mdi-pencil" }}
                </v-icon>
              </v-btn>
            </span>
          </template>

          <template v-slot:body>
            <div v-if="items && !desriptionInput">
              {{
                items.description
                  ? items.description
                  : $tr("there_is_no_description")
              }}
            </div>
          </template>
        </ListItem>
        <div v-if="!items.deleted_at" class="me-1">
          <ItemDescriptions
            ref="description"
            :description.sync="items.description"
            :fileId="items.id"
            @closeInput="toggleDescription"
          />
        </div>
        <v-divider class="mx-5"></v-divider>

        <!-- Password Section  -->
        <!-- <ListItem
					:size="`15`"
					icon="fas fa-user-lock"
					header="password"
					class="mt-1 mb-1"
				>
					<template v-slot:rear-icon>
						<span class="d-flex align-center me-5">
							<v-btn
								@click="togglePasswordModel"
								icon
								color="primary"
								outlined
								small
							>
								<v-icon size="21">
									{{ items.is_protected ? "mdi-lock-open" : "mdi-lock" }}
								</v-icon>
							</v-btn>
						</span>
					</template>
				</ListItem>
				<v-divider class="mx-5"></v-divider> -->

        <!-- private  -->
        <!-- <ListItem
					:size="`15`"
					icon="fas fa-share-square"
					header="Private"
					class="mt-1 mb-1"
				>
					<template v-slot:rear-icon>
						<span class="d-flex align-center me-5">
							<v-btn icon color="primary" outlined small>
								<v-icon size="21">mdi-pencil</v-icon>
							</v-btn>
						</span>
					</template>
				</ListItem>
				<v-divider class="mx-5"></v-divider> -->

        <!-- link or path  -->

        <!-- <ListItem icon="mdi-link-variant" class="mt-1 mb-1">
          <template v-slot:body>
            <a
              target="__blank"
              :href="items === null ? '' : items.path"
              style="text-decoration: none; color: var(--v-primary-base)"
              >{{ items === null ? "" : items.path }}</a
            >
          </template>
        </ListItem> -->
        <!-- <v-divider class="mx-5"></v-divider> -->

        <ListItem icon="mdi-link-variant" class="mt-1 mb-1">
          <template v-slot:body>
            <v-btn
              @click="copyLink"
              text
              color="primary"
              outlined
              small
              class="me-4"
            >
              {{ $tr("copy_public_link") }}
            </v-btn>
          </template>
        </ListItem>

        <ListItem icon="mdi-file" :header="$tr('type')" class="mt-0">
          <template v-slot:body>
            <p>{{ items === null ? "" : items.type }}</p>
          </template>
        </ListItem>

        <!-- created at  -->
        <ListItem
          icon="fas fa-calendar-alt"
          :size="`18`"
          :header="$tr('time_created')"
          class="mt-0"
        >
          <template v-slot:body>
            <p>
              {{
                items === null ? "" : localeHumanReadableTime(items.created_at)
              }}
            </p>
          </template>
        </ListItem>

        <!-- modified by  -->
        <ListItem
          icon="mdi-calendar-edit"
          :header="$tr('modified_by')"
          class="mt-0"
        >
          <template v-slot:body>
            <p v-if="items && items.created_by">
              {{
                items.created_by.firstname +
                " - " +
                localeHumanReadableTime(items.updated_at)
              }}
            </p>
          </template>
        </ListItem>
        <!-- size -->
        <ListItem
          :size="`18`"
          icon="fas fa-database"
          :header="$tr('size')"
          class="mt-0"
        >
          <template v-slot:body>
            <p>
              {{
                items === null
                  ? ""
                  : items.type == "folder"
                  ? bytesToSize(items.info.size)
                  : bytesToSize(items.size)
              }}
            </p>
          </template>
        </ListItem>

        <!-- Storage Used -->
        <ListItem
          :size="`18`"
          icon="fas fa-hdd"
          :header="$tr('storage_used')"
          class="mt-0"
        >
          <template v-slot:body>
            <p>{{ items === null ? "" : items.size }}</p>
          </template>
        </ListItem>

        <!-- Shared to-->
        <ListItem
          v-if="hasShare"
          :size="`16`"
          icon=" fa-users"
          iconalign="align-start"
          :header="$tr('shared_with')"
          class="mt-1"
        >
          <template v-slot:body>
            <div class="mb-2">
              <v-simple-table dense>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">{{ $tr("user") }}</th>
                      <th class="text-left">{{ $tr("permission") }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(shares, i) in items.file_shares"
                      :key="i"
                      class="not-hover-tr__force"
                    >
                      <td>
                        <UserChip
                          class="my-1"
                          :name="`${shares.shared_to.firstname} ${shares.shared_to.lastname}`"
                          :avatar="shares.shared_to.image"
                          :showCloseBtn="!isShare"
                          :selectedClose="!isShare"
                          hasImage
                          @close="removeFromShare(shares.id, items)"
                        />
                      </td>
                      <td>{{ $tr(shares.permission) }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </div>
          </template>
        </ListItem>
      </div>
    </v-card-text>
  </v-card>
</template>
<script>
import ListItem from "../common/filePreview/ListItem.vue";
import PropertyHeader from "./filePreview/PropertyHeader.vue";
import ItemDescriptions from "./filePreview/ItemDescriptions.vue";
import moment from "moment-timezone";

import UserChip from "../../design/UserChip.vue";
export default {
  components: {
    ListItem,
    PropertyHeader,
    UserChip,
    ItemDescriptions,
  },

  props: {
    isShare: false,
    items: {
      type: Object,
      default: null,
    },
  },

  computed: {
    hasShare: function () {
      if (this.items && this.items.file_shares) {
        return this.items.file_shares.length > 0 && !this.items.deleted_at;
      }
      return false;
    },
  },

  data() {
    return {
      desriptionInput: false,
    };
  },

  methods: {
    async copyLink() {
      try {
        const id = this.items.id;
        const path = location.origin + `/storage/files/preview/${id}`;
        await navigator.clipboard.writeText(path);
        const message = this.$tr("link_copied");
        // this.$toastr.i(message);
				this.$toasterNA("primary",this.$tr(message));
        
      } catch (_) {
        const message = this.$tr("cant_copy_link");
        // this.$toastr.e(message);
			this.$toasterNA("red", this.$tr(message));

        
      }
    },
    onCloseProperty() {
      this.$emit("close");
      this.desriptionInput = false;
      const input = this.$refs.description;
      if (input) {
        this.desriptionInput = false;
        input.toggleInput(null, false);
      }
    },

    toggleDescription() {
      const input = this.$refs.description;
      if (this.items && input) {
        if (this.desriptionInput) {
          this.desriptionInput = false;
          input.toggleInput(this.items.description, false);
        } else {
          this.desriptionInput = true;
          input.toggleInput(this.items.description, true);
        }
      }
    },

    // togglePasswordModel() {
    // 	this.$root.$emit("togglePasswordModel", this.items);
    // },

    bytesToSize(bytes) {
      let sizes = ["Bytes", "KB", "MB", "GB", "TB"];
      if (bytes == 0) return "0 Byte";
      let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
      return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
    },
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("MMMM DD ● h:mm: A");
    },
    removeFromShare(share_id, item) {
      this.$root.$emit("removeFromShare", share_id, item);
    },
  },
  created() {},
};
</script>
<style scoped>
p {
  margin-bottom: 0px !important;
}
</style>
