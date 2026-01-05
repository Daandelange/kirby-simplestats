// Views
import View from "./components/SimpleStatsArea.vue";
import DevicesView from "./components/Sections/Devices.vue";
import PageStatsView from "./components/Sections/PageStats.vue";
import ReferersView from "./components/Sections/Referers.vue";

// Components
import Chart from "./components/Ui/AreaChart.vue";
import InfoTable from "./components/Ui/InfoTable.vue";
import SearchableTable from "./components/Ui/SearchableTable.vue";
import Timespan from "./components/Ui/TimeFrameInput.vue";

// Table previews
import PercentageFieldPreview from "./components/Ui/PercentagePreview.vue";

// Sections
import Configuration from "./components/Sections/Configuration.vue";
import DbInformation from "./components/Sections/DbInformation.vue";
// import OnePageStats from "./components/Sections/OnePageStats.vue";
import TrackingTester from "./components/Sections/TrackingTester.vue";
import Visitors from "./components/Sections/Visitors.vue";

panel.plugin("daandelange/simplestats", {
  components: {
    "k-percentage-field-preview": PercentageFieldPreview,

    "k-simplestats-chart": Chart,
    "k-simplestats-infotable": InfoTable,
    "k-simplestats-searchabletable": SearchableTable,
    "k-simplestats-timespan": Timespan,

    "k-simplestats-view": View,
    "k-simplestats-devices-view": DevicesView,
    "k-simplestats-pagestats-view": PageStatsView,
    "k-simplestats-referers-view": ReferersView,

    "k-simplestats-configuration": Configuration,
    "k-simplestats-dbinformation": DbInformation,
    "k-simplestats-trackingtester": TrackingTester,
    "k-simplestats-visitors": Visitors,
  },

  sections: {
    // "pagestats": OnePageStats,
  },
});
