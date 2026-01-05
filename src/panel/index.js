// Views
import View from "./components/Views/View.vue";
import VisitsView from "./components/Views/VisitsView.vue";
import DevicesView from "./components/Views/DevicesView.vue";
import ReferrersView from "./components/Views/ReferrersView.vue";
import InfoView from "./components/Views/Information/InfoView.vue";
import InfoConfig from "./components/Views/Information/InfoConfig.vue";
import InfoDatabase from "./components/Views/Information/InfoDatabase.vue";
import InfoTracking from "./components/Views/Information/InfoTracking.vue";
import InfoVisitors from "./components/Views/Information/InfoVisitors.vue";

// Components
import Chart from "./components/View/Chart.vue";
import FilterTable from "./components/View/FilterTable.vue";
import InfoTable from "./components/View/InfoTable.vue";
import Timespan from "./components/View/Timespan.vue";

// Table Previews
import PercentageFieldPreview from "./components/Previews/PercentageFieldPreview.vue";

// Sections
// import PageSection from "./components/Sections/PageSection.vue";

panel.plugin("daandelange/simplestats", {
  components: {
    "k-percentage-field-preview": PercentageFieldPreview,

    "k-simplestats-chart": Chart,
    "k-simplestats-filter-table": FilterTable,
    "k-simplestats-info-table": InfoTable,
    "k-simplestats-timespan": Timespan,

    "k-simplestats-view": View,
    "k-simplestats-visits-view": VisitsView,
    "k-simplestats-devices-view": DevicesView,
    "k-simplestats-referrers-view": ReferrersView,
    "k-simplestats-info-view": InfoView,
    "k-simplestats-info-config": InfoConfig,
    "k-simplestats-info-database": InfoDatabase,
    "k-simplestats-info-tracking": InfoTracking,
    "k-simplestats-info-visitors": InfoVisitors,
  },

  sections: {
    // "pagestats": PageSection,
  },
});
