<template>
  <k-grid variant="columns">
    <!-- Configuration Info Section -->
    <k-column width="1/2">
      <k-simplestats-info-config path="configinfo" />
    </k-column>

    <!-- Database Info Section-->
    <k-column width="1/2">
      <k-simplestats-info-database />
    </k-column>

    <!-- Tracking Tester Section -->
    <k-column width="1/1">
      <k-line-field />
      <k-simplestats-info-tracking />
    </k-column>

    <!-- Visitors Info Section -->
    <k-column width="1/1">
      <k-line-field />
      <k-simplestats-info-visitors path="listvisitors" />
    </k-column>
  </k-grid>
</template>
