<template>
  <k-section :label="label" class="ss-table">
    <k-button
      v-if="isSearchable"
      icon="filter"
      size="xs"
      slot="options"
      text="Filter"
      variant="filled"
      @click="showSearch = !showSearch"
    />

    <k-input
      v-if="isSearchable && showSearch"
      v-model="searchTerm"
      ref="searchInput"
      icon="search"
      type="text"
      class="k-models-section-search"
      :placeholder="$t('simplestats.table.filter')"
    />

    <k-table
      :index="false"
      :columns="columns"
      :rows="sortedFilteredRows"
      :empty="searchTerm ? 'No search results...' : $t('simplestats.nodatayet')"
      @header="onHeaderClick"
    />
  </k-section>
</template>

<script>
export default {
  name: 'SearchableTable',

  props: {
    label: { type: String },
    columns: { type: Object },
    rows: { type: Array },
  },

  data() {
    return {
      searchTerm: '',
      showSearch: false,
      sortBy: null,
      sortAsc: true,
    };
  },

  computed: {
    isSearchable(){
      return Object.values(this.columns).some(col => col.search);
    },

    sortedFilteredRows() {
      // Filter
      const filtered = this.rows.filter(row => {
        if (!this.searchTerm) return true;
        return Object.entries(this.columns).some(([key, col]) => {
          if (!col.search || typeof row[key] === 'object') return false;
          return String(row[key])?.includes(this.searchTerm);
        }) || row.slug?.includes(this.searchTerm);
      })

      // Sort
      if (!this.sortBy) return filtered;

      const { type } = this.columns[this.sortBy];
      return filtered.sort((a, b) => {
        const valueA = type === 'number' || type === 'percentage' ? Number(a[this.sortBy]) : String(a[this.sortBy]);
        const valueB = type === 'number' || type === 'percentage' ? Number(b[this.sortBy]) : String(b[this.sortBy]);
        if (valueA === valueB) return 0;
        return (valueA > valueB ? 1 : -1) * (this.sortAsc ? 1 : -1);
      });
    },
  },

  watch: {
    showSearch(val) {
      if (val) {
        this.$nextTick(() => {
          this.$refs.searchInput?.focus();
        });
      };
    },
  },

  methods: {
    onHeaderClick({ column, columnIndex }) {
      if (!column.sortable) return;

      if (this.sortBy !== columnIndex) {
        this.sortBy = columnIndex;
        this.sortAsc = true;
      } else {
        this.sortAsc = !this.sortAsc;
      };
    },
  },
}
</script>

<style lang="less">
.ss-table {
  .k-table thead tr th {
    cursor: pointer;
  }
}
</style>
