<template>
	<v-card>
		<!--  Registration note Header  -->
		<v-card-title primary-title>
			<span>{{ $tr("note") }}</span>
			<v-spacer />
			<v-btn icon color="blue-grey lighten-2" @click="closeDialog" outlined>
				<v-icon color="error">mdi-close</v-icon>
			</v-btn>
		</v-card-title>

		<!--  Registration note Body  -->
		<v-card-text>
			<v-form ref="noteForm" lazy-validation>
				<div v-for="(note, index) in $v.notes.$each.$iter" :key="index">
					<v-row class="pt-3">
						<v-col cols="12" sm="12" class="py-0">
							<v-textarea
								outlined
								dense
								counter
								no-resize
								clearable
								name="input-7-4"
								:label="$tr('note')"
								value=""
								v-model.trim="note.note.$model"
								:rules="noteRules(note)"></v-textarea>
						</v-col>
						<v-col cols="12 py-0" sm="12" class="py-0">
							<v-autocomplete
								v-model.trim="note.users.$model"
								:items="allUsers"
								item-text="firstname"
								item-value="id"
								outlined
								dense
								chips
								small-chips
								:label="$tr('users')"
								multiple
								:rules="usersRules(note)">
								<template v-slot:selection="data">
									<v-chip
										v-bind="data.attrs"
										:input-value="data.selected"
										close
										small
										@click="data.select"
										@click:close="remove(data.item)">
										<v-avatar left>
											<v-img :src="data.item.image"></v-img>
										</v-avatar>
										{{ `${data.item.firstname} ${data.item.lastname}` }}
									</v-chip>
								</template>

								<template v-slot:item="data">
									<template>
										<v-list-item-avatar>
											<img :src="data.item.image" />
										</v-list-item-avatar>
										<v-list-item-content>
											<v-list-item-title
												v-html="
													`${data.item.firstname} ${data.item.lastname} (${data.item.code_id})`
												">
											</v-list-item-title>
										</v-list-item-content>
									</template>
								</template>
							</v-autocomplete>
						</v-col>
					</v-row>
					<v-row>
						<v-col>
							<v-btn color="primary" @click="onSubmit"
								>{{ $tr("submit") }}
							</v-btn>
						</v-col>
					</v-row>
				</div>
			</v-form>
		</v-card-text>
	</v-card>
</template>

<script>
import TextField from "../common/TextField";
import { mapGetters, mapActions } from "vuex";
import { required } from "vuelidate/lib/validators";
export default {
  components: {
    TextField,
  },
  props: {
    selected: {
      type: Array,
    },
    open: {
      type: Boolean,
    },
  },
  async created() {
    await this.fetchAllUsers("/all/users");
    this.notes[0].users = [];
    this.selected.forEach((item) => {
      this.notes[0].users.push(item.id);
    });
  },
  data() {
    return {
      notes: [
        {
          note: "",
          users: [],
        },
      ],
    };
  },
  validations: {
    notes: {
      $each: {
        note: {
          required,
        },
        users: {
          required,
        },
      },
    },
  },
  computed: {
    ...mapGetters({
      allUsers: "users/getAllItems",
    }),
  },
  methods: {
    ...mapActions({
      fetchAllUsers: "users/fetchAllUsers",
    }),
    async DialogChange(open) {
      if (open && this.selected.length != 0) {
        this.notes[0].users = [];
        this.selected.forEach((item) => {
          this.notes[0].users.push(item.id);
        });
      }
    },
    closeDialog() {
      this.$emit("close");
    },
    async onSubmit() {
      this.$refs.noteForm.validate();
      this.$v.notes.$touch();
      if (!this.$v.notes.$invalid) {
        await this.insertNote(this.notes);
      } else {
        this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
      }
    },
    async insertNote(notes) {
      /* ==========> insert new Note into DB  <========*/
      try {
        this.showProgressBar = true;
        const response = await this.$axios.post("notes", {
          note: this.notes[0].note,
          users: this.notes[0].users,
        });
        if (response.status == 201) {
          this.$toasterNA("green", this.$tr('successfully_updated'));;
          this.resetForm();
          this.closeDialog();
        }
        this.showProgressBar = false;
      } catch (error) {
        this.showProgressBar = false;
        if (error.response.status == 422) {
          // return server errors
          const errors = error.response.data.errors;
          for (let error in errors) {
            for (let er in errors[error]) {
              // this.$toastr.e(errors[error][er]);
				this.$toasterNA("red", this.$tr(errors[error][er]));

            }
          }
        } else {
          // this.$toastr.e(error.response.statusText);
				this.$toasterNA("red", this.$tr(error.response.statusText));

        }
      }
    },

    resetForm() {
      this.$refs.noteForm.reset();
      this.$v.notes.$reset();
      this.notes = [
        {
          note: "",
          users: [],
        },
      ];
    },

    noteRules(notes) {
      return [
        (_) => notes.note.required ||
          this.$tr("required", this.$tr("item_name",this.$tr('note'))),
      ];
    },
    usersRules(notes) {
      return [
        (_) =>
          notes.users.required ||
          this.$tr("required", this.$tr("users")),
      ];
    },
    remove(item) {
      const index = this.notes[0].users.indexOf(item.id);
      if (index >= 0) this.notes[0].users.splice(index, 1);
    },
  },
};
</script>

<style scoped>
.show-cursor {
	cursor: pointer;
}
</style>
