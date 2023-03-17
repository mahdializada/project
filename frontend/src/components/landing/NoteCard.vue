<template>
	<div>
		<v-card
			v-if="$attrs.link"
			outlined
			elevation="0"
			class=""
			style="background-color: inherit">
			<v-card-title class="primary pt-1" style="height: 50px">
				<span class="white--text">{{ $tr($attrs.titleText) }}</span>
			</v-card-title>
			<v-card-text class="px-3 pb-0 overflow-auto Scroll">
				<div>
					<!-- <div cols="12" md="6" class="pa-1 ps-2">
            <div class="d-flex align-center justify-space-between">
              <div class="text-capitalize black--text me-md-15 me-sm-2">
                {{ $tr("design_link") }}
              </div>
              <div
                class="text-capitalize grey--text d-flex justify-space-center align-center"
                v-if="$attrs.item.design_request_order"
              >
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      height="30"
                      width="100"
                      rounded
                      target="_blank"
                      color="primary"
                      dark
                      v-bind="attrs"
                      v-on="on"
                      :href="
                        $attrs.item.design_request_order &&
                        $attrs.item.design_request_order.design_link
                      "
                    >
                      {{ $tr("link") }}
                      <v-icon class="hover" color="white">mdi-link</v-icon>
                    </v-btn>
                  </template>
                  <span>{{
                    $attrs.item.design_request_order &&
                    $attrs.item.design_request_order.design_link
                  }}</span>
                </v-tooltip>

                <v-btn
                  v-if="
                    (!isDesignInputOpen && $attrs.canEdit) ||
                    ($attrs.item.status === 'in design' && !isDesignInputOpen)
                  "
                  @click="toggleDesignLink"
                  style="height: 28px; width: 28px"
                  color="primary"
                  class="ms-1"
                  fab
                  x-small
                  dark
                >
                  <v-icon> mdi-pencil</v-icon>
                </v-btn>

                <div
                  v-if="isDesignInputOpen"
                  class="d-flex align-center justify-content-between"
                >
                  <v-btn
                    color="primary"
                    class="stepper-btn px-1"
                    style="min-width: 130px; height: 31px"
                    v-if="isLoading"
                    :loading="isLoading"
                  >
                    <template v-slot:loader>
                      <span>
                        <span>
                          {{ $tr("submitting") + "..." }}
                        </span>
                        <v-progress-circular
                          class="ma-0"
                          indeterminate
                          color="white"
                          size="20"
                          :width="2"
                        />
                      </span>
                    </template>
                  </v-btn>

                  <v-btn
                    @click="saveDesignLink"
                    v-else
                    class="ms-1"
                    style="height: 25px"
                    color="primary"
                  >
                    <v-icon left> mdi-check </v-icon>
                    {{ $tr("save") }}
                  </v-btn>

                  <CloseBtn
                    @click="toggleDesignLink"
                    style="height: 25px"
                    class="py-0 my-0 white--text"
                    small
                  />
                </div>
              </div>
            </div>
            <div class="mt-2">
              <CustomInput
                v-model="$v.designLink.$model"
                :rules="validate($v.designLink, $root, 'design_link')"
                v-show="isDesignInputOpen"
                :placeholder="$tr('design_link')"
                type="textfield"
              />
            </div>
          </div> -->
					<!-- <v-col
						v-if="$attrs.item.type === 'landing page design'"
						cols="12"
						md="6"
						class="pa-1 ps-2 d-none d-md-block"
					>
					</v-col> -->
					<div
						cols="12"
						md="6"
						class="pa-1 ps-2 pt-2 pb-0"
						v-if="$attrs.item && $attrs.item.type === 'landing page design'">
						<div class="d-flex align-center justify-space-between">
							<div class="text-capitalize black--text me-md-15 me-sm-2">
								{{ $tr("landing_page_link") }}
							</div>
							<div
								class="text-capitalize grey--text d-flex justify-space-center align-center"
								v-if="$attrs.item.design_request_order">
								<v-tooltip bottom>
									<template v-slot:activator="{ on, attrs }">
										<v-btn
											height="30"
											width="100"
											rounded
											target="_blank"
											color="primary"
											dark
											v-bind="attrs"
											v-on="on"
											:href="
												$attrs.item.design_request_order &&
												$attrs.item.design_request_order.landing_page_link
											">
											{{ $tr("link") }}
											<v-icon class="hover" color="white">mdi-link</v-icon>
										</v-btn>
									</template>
									<span>{{
										$attrs.item.design_request_order &&
										$attrs.item.design_request_order.landing_page_link
									}}</span>
								</v-tooltip>

								<v-btn
									v-if="
										(!isLandingInputOpen && $attrs.canEdit) ||
										($attrs.item.status === 'in design' && !isLandingInputOpen)
									"
									@click="toggleLandingPageLink"
									style="height: 28px; width: 28px"
									color="primary"
									class="ms-1"
									fab
									x-small
									dark>
									<v-icon> mdi-pencil</v-icon>
								</v-btn>

								<div
									v-if="isLandingInputOpen"
									class="d-flex align-center justify-content-between">
									<v-btn
										color="primary"
										class="stepper-btn px-1"
										style="min-width: 130px; height: 31px"
										v-if="isLoading"
										:loading="isLoading">
										<template v-slot:loader>
											<span>
												<span>
													{{ $tr("submitting") + "..." }}
												</span>
												<v-progress-circular
													class="ma-0"
													indeterminate
													color="white"
													size="20"
													:width="2" />
											</span>
										</template>
									</v-btn>

									<v-btn
										@click="saveLandingPageLink"
										v-else
										class="ms-1"
										style="height: 25px"
										color="primary">
										<v-icon left> mdi-check </v-icon>
										{{ $tr("save") }}
									</v-btn>

									<CloseBtn
										@click="toggleLandingPageLink"
										style="height: 25px"
										class="py-0 my-0 white--text"
										small />
								</div>
							</div>
						</div>
						<div class="mt-2">
							<CustomInput
								v-model="$v.landingPageLink.$model"
								:rules="validate($v.landingPageLink, $root, 'landing_link')"
								v-show="isLandingInputOpen"
								:placeholder="$tr('landing_link')"
								type="textfield" />
						</div>
					</div>
				</div>
				<div class="mb-2">
					<CardHeader
						:showEditBtn="isUploadComponentOpen"
						:loading="isUploadingFiles"
						@onEdit="toggleUploadComponents"
						@onClose="toggleUploadComponents"
						@onSave="uploadFiles"
						:uploadFile="true"
						:text="'design_link_files'"
						icon="fa-file"
						:isSubmitting="isSubmitting" />
					<v-expand-transition>
						<div v-if="isUploadComponentOpen">
							<v-expand-transition>
								<CustomFileDropzone
									ref="customFileDropzone"
									@filesSelectedFileObject="filesSelected"
									@fileRemoved="fileRemoved"
									allowedExtensions
									:fileSize="0" />
							</v-expand-transition>
						</div>
					</v-expand-transition>

					<CustomFileDropzone
						@onDelete="deleteFiles"
						:files="$attrs.item.files"
						:showFiles="true"
						:cantDownload="true"
						@downloadFile="onDownloadFile" />
				</div>
			</v-card-text>
		</v-card>

		<v-card
			v-else
			outlined
			elevation="0"
			class=""
			style="background-color: inherit">
			<v-card-title class="primary pt-1" style="height: 50px">
				<span class="white--text">{{ $tr($attrs.titleText) }}</span>
				<v-spacer />

				<v-btn
					v-if="!isEditOpened && $attrs.canEdit"
					@click="toggleLandingNotePage"
					style="height: 28px; width: 28px"
					color="primary"
					class="mx-1"
					fab
					x-small
					dark>
					<v-icon
						color="primary"
						class="white pa-2"
						style="border-radius: 50px">
						mdi-eye</v-icon
					>
				</v-btn>

				<v-btn
					v-if="!isEditOpened && $attrs.canEdit"
					@click="toggleEditInput"
					style="height: 28px; width: 28px"
					color="primary"
					fab
					x-small
					class="me-1"
					dark>
					<v-icon
						color="primary"
						class="white pa-2"
						style="border-radius: 50px">
						mdi-pencil</v-icon
					>
				</v-btn>

				<v-dialog v-model="landingNotePage" width="1000">
					<LandingNoteView
						:title="$attrs.titleText"
						:note="$attrs.descriptions"
						@closeDialog="landingNotePage = false" />
				</v-dialog>

				<div
					v-if="isEditOpened"
					class="d-flex align-center justify-content-between">
					<v-btn
						color="success"
						class="stepper-btn px-1"
						style="min-width: 130px; height: 31px"
						v-if="isLoading"
						:loading="isLoading">
						<template v-slot:loader>
							<span>
								<span>
									{{ $tr("submitting") + "..." }}
								</span>
								<v-progress-circular
									class="ma-0"
									indeterminate
									color="white"
									size="20"
									:width="2" />
							</span>
						</template>
					</v-btn>

					<v-btn @click="onSave" v-else style="height: 31px" color="primary">
						<v-icon left> mdi-check </v-icon>
						{{ $tr("save") }}
					</v-btn>

					<CloseBtn
						logoColor="white"
						style="height: 31px"
						@click="toggleEditInput"
						class="py-0 my-0 white--text"
						small />
				</div>
			</v-card-title>
			<v-card-text class="px-3 pb-0 overflow-auto Scroll" style="height: 300px">
				<div class="mt-1" v-if="isEditOpened">
					<Editor
						v-model="$v.text.$model"
						:rules="validate($v.text, $root, $attrs.titleText)" />
				</div>
				<div v-else class="mx-2 mt-1">
					<div class="mt-1 mb-1" v-html="$attrs.descriptions"></div>
				</div>
			</v-card-text>
		</v-card>

		<div
			v-if="$attrs.column != 'sales_note' && userCanApprovOrReJect()"
			class="d-flex align-center justify-start mt-2">
			<TextButton
				type="primary"
				:text="$tr('approve')"
				@click="$emit('onApproved')">
			</TextButton>

			<TextButton
				type="danger"
				class="ms-1"
				:text="$tr('reject')"
				@click="$emit('onReject')">
			</TextButton>
		</div>
	</div>
</template>

<script>
import CustomFileDropzone from "../../components/landing/CustomFileDropzone.vue";
import CloseBtn from "~/components/design/Dialog/CloseBtn.vue";
import Editor from "../design/Editor.vue";
import HandleException from "~/helpers/handle-exception";
import CustomButton from "~/components/design/components/CustomButton.vue";
import Alert from "~/helpers/alert";
import Helpers from "~/helpers/helpers";
import CustomInput from "~/components/design/components/CustomInput.vue";
import TextButton from "~/components/common/buttons/TextButton.vue";
import { required } from "vuelidate/lib/validators";
import Validations, { validPdf } from "~/validations/validations";
import GlobalRules from "~/helpers/vuelidate";
import LandingNoteView from "./Orders/LandingNoteView.vue";
import CardHeader from "../common/CardHeader.vue";
import Personal from "~/utils/file_management/personal";
import { mapMutations } from "vuex";

export default {
  components: {
    CustomFileDropzone,
    CloseBtn,
    CustomInput,
    TextButton,
    Editor,
    CustomButton,
    CardHeader,
    LandingNoteView,
  },

  data() {
    return {
      validate: GlobalRules.validate,
      isEditOpened: false,
      text: this.$attrs.descriptions,
      isLoading: false,

      // => Submittion Link Section <=
      isLandingInputOpen: false,
      landingPageLink: "",
      isDesignInputOpen: false,
      designLink: "",
      landingNotePage: false,
      isFileInputOpen: false,
      file_url: null,

      isUploadComponentOpen: false,
      isUploadingFiles: false,
      selectedFiles: [],
      uploadedFiles: [],
      isSubmitting: false,
      isLandingLoading: false,
    };
  },

  validations() {
    return {
      text: this.$attrs.titleText.includes("design")
        ? Validations.htmlNoteValidation
        : Validations.htmlStoryNoteValidation,
      reason_id: { required },
      designLink: Validations.urlValidation,
      landingPageLink: Validations.urlValidation,
      file_url: { required, validPdf },
    };
  },
  async created() {},
  methods: {
    ...mapMutations({
      updateHasFile: "design_requests/UPDATE_HAS_FILE"
    }),
    onDownloadFile(record) {
      const personal = new Personal(this);
      const url = "files/library/files/download";
      const { id, type, name } = record;
      personal.downloadFiles(url, [{ id, type, name }]);
    },
    // For Uploading landing files
    filesSelected(files) {
      this.selectedFiles = files;
    },

    fileRemoved(object) {
      this.selectedFiles.splice(object.index, 1);
    },

    toggleUploadComponents() {
      if (this.isUploadComponentOpen) {
        this.$refs.customFileDropzone.clearItems();
        this.selectedFiles = [];
      }
      this.isUploadComponentOpen = !this.isUploadComponentOpen;
    },

    async uploadFiles() {
      if (this.selectedFiles.length) {
        this.isSubmitting = true;
        try {
          const formData = new FormData();
          this.selectedFiles.forEach((file) => {
            formData.append("files[]", file);
          });
          formData.append("_method", "put");
          const url = `order_requests/${this.$attrs.item.design_request_order?.id}`;
          const response = await this.$axios.post(url, formData);
          if (response.data.result) {
            this.$attrs.item.files = response.data.images;
            this.$attrs.item.has_files = true;
            this.toggleUploadComponents();
            // this.$toastr.s(this.$tr("files_uploaded_successfully"));
				this.$toasterNA("green", this.$tr('files_uploaded_successfully'));

            this.updateHasFile(true);
          } else {
            // this.$toastr.e(this.$tr("can_not_update"));
			      this.$toasterNA("red", this.$tr("can_not_update"));

          }
        } catch (error) {
          HandleException.handleApiException(this, error);
        }
        this.isSubmitting = false;
      }
    },
    async deleteFiles(file) {
      try {
        const url = `order_requests/${this.$attrs.item.design_request_order?.id}`;
        const response = await this.$axios.put(url, {
          fileId: file.id,
        });
        if (response.data.result) {
          this.$attrs.item.files = this.$attrs.item.files.filter(
            ({ id }) => id != file.id
          );
          // this.$toastr.s(this.$tr("file_deleted_successfully"));
				this.$toasterNA("green", this.$tr('file_deleted_successfully'));

          if(this.$attrs.item.files.length == 0){
            this.updateHasFile(false);
          }
        } else {
          // this.$toastr.e(this.$tr("could_not_delete"));
			      this.$toasterNA("red", this.$tr("could_not_delete"));

        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    // For Uploading landing files

    // *HM* Upload PDF file *HM*

    toggleFileInput() {
      this.file_url = null;
      this.$v.file_url.$reset();
      this.isFileInputOpen = !this.isFileInputOpen;
      this.isLoading = false;
    },

    // Submit Design Link
    async saveDesignLink() {
      if (!this.$v.designLink.$invalid) {
        this.$attrs.column = "design_link";
        this.text = this.designLink;
        this.$attrs.itemId = this.$attrs.item.design_request_order?.id;
        const isSaved = await this.onSave("isLoading");
        if (isSaved) {
          this.$attrs.item.design_request_order.design_link = this.designLink;
          this.toggleDesignLink();
        }
      }
    },

    // *HM* Landing Page Link *HM*
    toggleLandingPageLink() {
      if (!this.isLandingInputOpen) {
        let _pageLink = this.$attrs.item.design_request_order.landing_page_link;
        this.landingPageLink = _pageLink;
      }
      this.isLandingInputOpen = !this.isLandingInputOpen;
      this.isLoading = false;
    },

    toggleDesignLink() {
      if (!this.isDesignInputOpen) {
        let _pageLink = this.$attrs.item.design_request_order.design_link;
        this.landingPageLink = _pageLink;
      }
      this.isDesignInputOpen = !this.isDesignInputOpen;
      this.isLoading = false;
    },
    // Submit landing page link
    async saveLandingPageLink() {
      if (!this.$v.landingPageLink.$invalid) {
        this.$attrs.column = "landing_page_link";
        this.text = this.landingPageLink;
        this.$attrs.itemId = this.$attrs.item.design_request_order?.id;
        const isSaved = await this.onSave();
        if (isSaved) {
          this.$attrs.item.design_request_order.landing_page_link =
            this.landingPageLink;
          this.toggleLandingPageLink();
        }
      }
    },

    // *HM* Save design request order note *HM*
    async onSave(loader = "isLoading") {
      if (!this.$v.text.$invalid) {
        this[loader] = true;
        try {
          const url = `design-request-column?update-column=${this.$attrs.column}`;
          await this.$axios.put(url, {
            columnValue: this.text,
            id: this.$attrs.itemId,
          });
          this.$emit("onSave", this.text);
          this[loader] = false;
          this.isEditOpened = false;
          return true;
        } catch (error) {
          HandleException.handleApiException(this, error);
        }
        this[loader] = false;
        this.isEditOpened = false;
      }
    },

    userCanApprovOrReJect() {
      let canApprove = false;
      if (this.$attrs.item.design_request_order) {
        let userOrder =
          this.$attrs.item?.design_request_order?.design_request_order_user;
        if (userOrder && userOrder.length) {
          canApprove = userOrder[0].user_can_edit;
        }
      }

      if (
        this.$attrs.item.status == "storyboard review" &&
        this.$attrs.cardItem == "in storyboard"
      ) {
        return canApprove || this.$isInScope("drva");
      } else if (
        this.$attrs.item.status == "design review" &&
        this.$attrs.cardItem == "in design"
      ) {
        return canApprove || this.$isInScope("drva");
      }
      return false;
    },

    toggleEditInput() {
      this.isEditOpened = !this.isEditOpened;
      this.text = this.$attrs.descriptions;
    },
    toggleLandingNotePage() {
      this.landingNotePage = !this.landingNotePage;
    },
  },
};
</script>
