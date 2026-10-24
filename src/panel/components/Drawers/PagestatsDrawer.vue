<template>
  <k-drawer :options="options">
    <k-pagestats-field v-bind="stats" />
  </k-drawer>
</template>

<script>

import KPagestatsField from "../Fields/PagestatsField.vue"

export default {

  components: { KPagestatsField },

  emits: [
    "next",
    "prev",
  ],

  props: {
    uid: {
      type: String,
      required: true,
    },
    dateFrom: String,
    dateTo: String,
    next: {
      type: Object
    },
    prev: {
      type: Object
    },
  },

  data() {
    return {
      stats: {},
    };
  },

  computed: {
    options() {
      return [
        this.$props.prev ? {
          title: "Previous",
          icon: 'angle-left',
          click: () => { this.$emit('prev'); if(this.$props.prev instanceof Function) this.$props.prev(); },
        } : {},
        this.$props.next ? {
          title: 'Next',
          icon: 'angle-right',
          click: () => { this.$emit('next'); if(this.$props.next instanceof Function) this.$props.next(); },
        } : {},
      ];
    },
  },

  created() {
    this.load();
  },

  methods: {
    async load() {
      try {
        this.stats = await this.$api.get(
          'simplestats/onepagestats/'+this.uid+(this.dateFrom?('?dateFrom='+this.dateFrom+'&dateTo='+this.dateTo):''),
        );
      } catch (error) {
        this.$panel.error(error);
      }
    },
  }
};
</script>
