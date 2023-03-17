<template>
  <div :class="`single-remark d-flex py-2 ${deleting ? 'deleting' : ''}`">
    <div :class="`image ${smallRemark ? 'me-1' : 'me-2'}`">
      <v-avatar size="40">
        <img :src="remark.user.image" alt="John" />
      </v-avatar>
    </div>
    <div class="content w-full">
      <div class="d-flex justify-space-between align-center mb-1">
        <div class="name">
          {{ remark.user.firstname }} {{ remark.user.lastname }}
        </div>
        <div class="d-flex align-center">
          <div class="time">{{ getTime(remark.created_at) }}</div>
          <div class="action ms-2" v-if="smallRemark">
            <v-menu offset-y :disabled="remark.user_id != $auth.user.id">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  :disabled="remark.user_id != $auth.user.id"
                  fab
                  elevation="0"
                  small
                  style="margin-top: ; opacity: 0.8"
                  v-bind="attrs"
                  v-on="on"
                  icon
                >
                  <v-icon dark size="26"> mdi-dots-horizontal </v-icon>
                </v-btn>
              </template>
              <v-list dense min-width="130px">
                <v-list-item
                  link
                  dense
                  v-for="(item, index) in actionItems"
                  :key="index"
                  @click="item.handler"
                >
                  <v-list-item-title>
                    {{ item.title }}
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
        </div>
      </div>
      <p
        v-if="!isEdit"
        :style="
          smallRemark
            ? 'max-width: 280px; word-wrap: break-word'
            : 'max-width: 600px; word-wrap: break-word'
        "
      >
        {{ remark.remark }}
      </p>
      <div
        class="reactions d-flex align-center position-relative"
        v-if="!isEdit"
      >
        <ReactionsList
          v-if="reactions"
          @setReact="setReact"
          :react="reaction"
        />
        <v-btn
          @click="toggleReactions"
          fab
          small
          elevation="0"
          :color="reaction != 'emoji-add' ? '#ffd35044' : ''"
        >
          <ReactionIcons width="22" :emoji="reaction" />
        </v-btn>
        <ReactionsCount :remark.sync="remark" />
      </div>
      <RemarkEdit
        v-if="isEdit"
        :remark.sync="remark"
        @saveEdit="isEdit = false"
        @reverseEdit="$emit('changeReactionCount', $event)"
      />
    </div>
    <div class="action ms-2" v-if="!smallRemark">
      <v-menu offset-y :disabled="remark.user_id != $auth.user.id">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            :disabled="remark.user_id != $auth.user.id"
            fab
            elevation="0"
            small
            style="margin-top: -10px; opacity: 0.8"
            v-bind="attrs"
            v-on="on"
            icon
          >
            <v-icon dark size="26"> mdi-dots-horizontal </v-icon>
          </v-btn>
        </template>
        <v-list dense min-width="130px">
          <v-list-item
            link
            dense
            v-for="(item, index) in actionItems"
            :key="index"
            @click="item.handler"
          >
            <v-list-item-title>
              {{ item.title }}
            </v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </div>
  </div>
</template>
<script>
import ReactionIcons from "./ReactionIcons.vue";
import ReactionsCount from "./ReactionsCount.vue";
import ReactionsList from "./ReactionsList.vue";
import moment from "moment-timezone";
import RemarkEdit from "./RemarkEdit.vue";

export default {
  props: {
    remark: Object,
    smallRemark: Boolean,
  },
  components: { ReactionIcons, ReactionsCount, ReactionsList, RemarkEdit },
  data() {
    return {
      reactions: false,
      reaction: "emoji-add",
      isEdit: false,
      actionItems: [
        {
          title: this.$tr("edit"),
          handler: () => {
            this.isEdit = true;
          },
        },
        {
          title: this.$tr("delete"),
          handler: this.deleteHandler,
        },
      ],
      deleting: false,
    };
  },
  methods: {
    toggleReactions() {
      this.reactions = !this.reactions;
    },
    setReact(reaction) {
      if (this.reaction == reaction) {
        this.reaction = "emoji-add";
        this.removeReaction();
      } else {
        this.reaction = reaction;
        this.saveReaction();
      }
      this.reactions = false;
    },
    getTime(time) {
      return moment(time).locale(this.$vuetify.lang.current).fromNow(true);
    },
    async deleteHandler() {
      try {
        this.deleting = true;
        let { data } = await this.$axios.delete(`/remarks/${this.remark.id}`);
        if (data.result) {
          this.$emit("deleteHandler", this.remark.id);
        }
      } catch (error) {
        // this.$toasterNA("red", this.$tr('something_went_wrong'));
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
      this.deleting = false;
    },

    async saveReaction() {
      const reqData = {
        reaction_type: this.reaction,
        remark_id: this.remark.id,
      };
      try {
        let response = await this.$axios.post("remark-reactions", reqData);
        if (response.status == 200) {
          this.$emit("changeReactionCount", response.data);
        }
      } catch (error) {
        // this.$toasterNA("red", this.$tr('something_went_wrong'));
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
    async removeReaction() {
      try {
        let response = await this.$axios.delete(
          "remark-reactions/" + this.remark.id
        );
        if (response.data.result) {
          this.$emit("changeReactionCount");
          // this.remark = { ...this.remark };
        }
      } catch (error) {
        // this.$toasterNA("red", this.$tr('something_went_wrong'));
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
    getMyReact() {
      this.remark.reactions.forEach((row) => {
        if (row.user_id == this.$auth.user.id) {
          this.reaction = row.reaction_type;
        }
      });
    },
  },
  created() {
    this.getMyReact();
  },
};
</script>
<style scoped>
.single-remark {
  border-bottom: 1px solid #eee;
}

.single-remark .image {
  max-width: 40px;
}

.single-remark .name {
  font-size: 16px;
  font-weight: 500;
}

.single-remark .time {
  opacity: 0.6;
}

.single-remark.deleting {
  opacity: 0.6;
}
</style>
