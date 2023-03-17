<template>
  <div class="current-reactions d-flex align-center ms-1 pe-1">
    <v-menu
      :close-on-click="true"
      bottom
      offset-y
      max-height="400"
      v-for="(emoji, index) in emojies"
      :key="index"
    >
      <template v-slot:activator="{ on, attrs }">
        <div
          class="reactions-count d-flex align-center me-1"
          v-bind="attrs"
          v-on="on"
          @click="changeReactionHover(emoji)"
        >
          <ReactionIcons width="18" :emoji="emoji" />
          <div class="reaction-count ms-1">{{ countReactions(emoji) }}</div>
        </div>
      </template>

      <v-list>
        <v-list-item
          v-for="(reaction, index) in getReactionInfo()"
          :key="index"
          dense
        >
          <v-avatar size="25" color="primary">
            <img :src="reaction.user.image" alt="" />
          </v-avatar>
          <v-list-item-title class="px-1 custom-title">{{
            reaction.user.firstname + " " + reaction.user.lastname
          }}</v-list-item-title>

          <ReactionIcons width="25" :emoji="reaction.reaction_type" />
        </v-list-item>
      </v-list>
    </v-menu>
  </div>
</template>
<script>
import ReactionIcons from "./ReactionIcons.vue";
export default {
  props: {
    remark: {
      type: Object,
      require: true,
    },
  },
  data() {
    return {
      selected_reaction: null,
      emojies: ["like", "love", "wow", "sad", "natural"],
    };
  },
  components: { ReactionIcons },
  methods: {
    countReactions(type) {
      let count = 0;
      this.remark.reactions.forEach((row) => {
        if (row.reaction_type == type) {
          count++;
        }
      });
      return count;
    },
    changeReactionHover(type) {
      this.selected_reaction = type;
    },
    getReactionInfo() {
      let info = [];
      this.remark.reactions.forEach((row) => {
        if (row.reaction_type == this.selected_reaction) {
          info.push(row);
        }
      });
      return info;
    },
  },
  created() {},
};
</script>

<style scoped>
.current-reactions {
  padding: 4px 12px;
  background: #f5f5f5;
  height: 30px;
  border-radius: 15px;
}
.theme--dark .current-reactions {
  background: #181818;
}
.reaction-count {
  font-size: 14px;
}
.reactions-count {
  cursor: pointer !important;
}
.custom-title {
  font-weight: 500 !important;
  font-size: 13px !important;
  opacity: 0.8 !important;
}
</style>
<style scoped>
.v-menu__content::-webkit-scrollbar {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3) !important;
  border-radius: 10px !important;
  background-color: #f5f5f5 !important;
}
.v-menu__content::-webkit-scrollbar {
  width: 10px;
  background-color: var(--v-surface-base);
}
.v-menu__content::-webkit-scrollbar:hover {
  cursor: alias !important;
}
.v-menu__content::-webkit-scrollbar-thumb:hover {
  cursor: alias !important;
}

.v-menu__content::-webkit-scrollbar-thumb {
  border-radius: 10px;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
  background-color: var(--v-background-darken2);
}
</style>