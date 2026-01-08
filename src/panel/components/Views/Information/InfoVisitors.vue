<template>
  <div class="k-fieldset">
    <k-grid variant="columns">
      <k-column width="1/1">
        <k-headline-field :label="$t('simplestats.info.current.title')" />
      </k-column>

      <!-- Visitors Table -->
      <k-column width="1/1">
        <k-simplestats-filter-table
          :label="$t('simplestats.info.current.label')"
          :rows="rows"
          :columns="columns"
        />

        <br />
        <k-box theme="info" :text="$t('simplestats.info.current.info')" />
      </k-column>
    </k-grid>
  </div>
</template>

<script>
import apiFetch from '../../../mixins/apiFetch.js';

export default {
  mixins: [apiFetch],

  data() {
    return {
      rows: [],
      columns: {}
    };
  },

  methods: {
    loadData(response) {
      this.columns = response.columns || {};
      this.rows = response.rows || [];
    }
  }
};
</script>
