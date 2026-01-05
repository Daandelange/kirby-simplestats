<template>
  <k-section :label="label">
    <div class="k-table" style="overflow: auto">
      <table style="table-layout: auto">
        <tbody>
          <tr v-for="(row, i) in previewData" :key="i">
            <th data-mobile="true">{{ $t(row.label) }}</th>
            <td data-mobile="true" class="k-table-cell">
              <preview :is="row.preview" :value="row.value" v-bind="row.props" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </k-section>
</template>

<script>
export default {
  props: {
    label: { type: String },
    rows: { type: Array },
  },

  computed: {
    previewData() {
      const rules = {
        'k-text-field-preview': row => ({ column: row.column }),
        'k-toggle-field-preview': () => ({ field: { disabled: true } }),
        'k-tags-field-preview': row => ({
          value: Array.isArray(row.value) ? row.value : [],
        }),
        'k-files-field-preview': row => ({
          value: Array.isArray(row.value)
            ? row.value
            : [{ image: { icon: 'file', back: 'black', color: 'white' }, filename: row.value }],
        }),
      };

      return this.rows.map(row => {
        const preview = row.preview || 'k-text-field-preview';
        const format  = row.format ? row.format(row.value) : row.value;
        const props   = rules[preview]?.({ ...row, value: format }) ?? {};
        const value   = props.value ?? format;

        return {
          ...row,
          preview,
          value,
          props,
        };
      });
    },
  },
};
</script>
