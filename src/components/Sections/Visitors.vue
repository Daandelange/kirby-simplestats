<template>
  <div class="k-fieldset">
    <k-grid variant="columns">
      <k-column width="1/1">
        <k-headline-field :label="$t('simplestats.info.config.currentusers.title')" />
      </k-column>

      <k-column width="1/1">
        <SearchableTable
          :label="$t('simplestats.info.config.currentusers.title')"
          :rows="rows"
          :columns="columns"
        />

        <br />
        <k-box theme="info" :text="$t('simplestats.info.config.currentusers.info')" />
      </k-column>
    </k-grid>
  </div>
</template>

<script>
import SectionBase from '../Sections/SimpleStatsSectionBase.vue';
import SearchableTable from '../Ui/SearchableTable.vue';

export default {
  extends: SectionBase,

  data() {
    return {
      rows: [],
      columns: {},
    }
  },

  components: {
    SearchableTable,
  },

  methods: {
    loadData(response) {
      this.columns = response.columns;
      this.rows    = response.rows;
    }
  },
};
</script>
