<template>
  <k-grid variant="columns">
    <k-column width="1/2">
      <k-simplestats-info-config section-name="configinfo" />
    </k-column>

    <k-column width="1/2">
      <k-simplestats-info-database />
    </k-column>

    <k-column width="1/1">
      <k-line-field />
      <k-simplestats-info-tracking />
    </k-column>

    <k-column width="1/1">
      <k-line-field />
      <k-simplestats-info-visitors section-name="listvisitors" />
    </k-column>
  </k-grid>
</template>

<script>
export default {

};
</script>
