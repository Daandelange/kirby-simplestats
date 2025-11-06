<template>
  <k-panel-inside>

  <k-view class="k-simplestats-view">
    <!-- DISCLAIMER -->
    <k-box v-if="!isLoading && !dismissDisclaimer" theme="warning" icon="flag">
      <template #default>
        <div>
          <k-headline class="h4">{{ $t('simplestats.disclaimer.title') }}</k-headline>
          <k-text size="small">
            <span v-html="$t('simplestats.disclaimer.text')"></span>
            <span class="hover-to-help">
              <k-icon type="question" />
              <div class="help"><k-text theme="help" size="small">{{ $t('simplestats.disclaimer.dismiss') }}</k-text></div>
            </span>
            <br>
          </k-text>
        </div>
      </template>
    </k-box>

    <k-header :tabs="tabs" :tab="tab" @tabChange="onTab">
      <template slot="default">
        {{ label }}
      </template>
      <!-- <template slot="left">
        Left
      </template> -->
      <!-- <template slot="right">
          <time-frame-input :dateChoices="timeframes" ref="timeFrame" />
      </template> -->
    </k-header>
    
    <time-frame-input :dateChoices="timeframes" ref="timeFrame" :time-period="timePeriod" :initial-view-periods="initialViewPeriods" />
    <k-tabs :tabs="tabsWithLinks" :tab="tab"></k-tabs>
    

    <div v-if="tab == tabs[0].name">
      <page-stats :dateFrom="$refs.timeFrame.dateFrom" :dateTo="$refs.timeFrame.dateTo" section-name="pagestats" />
    </div>

    <div v-else-if="tab == tabs[1].name">
      <devices :dateFrom="$refs.timeFrame.dateFrom" :dateTo="$refs.timeFrame.dateTo" section-name="devicestats" />
    </div>

    <div v-else-if="tab == tabs[2].name">
      <referers :dateFrom="$refs.timeFrame.dateFrom" :dateTo="$refs.timeFrame.dateTo" section-name="refererstats" />
    </div>

    <div v-else-if="tab == tabs[3].name">
      <k-grid variant="columns" style="gap: var(--spacing-12);">
        <!-- CONFIGURATION -->
        <k-column width="1/2">
          <configuration section-name="configinfo" />
        </k-column>

        <!-- DB INFORMATION -->
        <k-column width="1/2">
          <DbInformation section-name="listdbinfo" />
        </k-column>
          
        <!-- TRACKING TESTER -->
        <k-column width="1/1">
          <k-line-field />
          <tracking-tester />
        </k-column>

        <!-- VISITORS TABLE -->
        <k-column width="1/1">
          <k-line-field />
          <visitors section-name="listvisitors" />
        </k-column>
      </k-grid>
    </div>

    <div v-else>
      <k-empty>{{ $t('simplestats.taberror') }}</k-empty>
    </div>

    <!-- Invisible color getter util for CSS to JS style data transfer -->
    <div id="chart-default-color-getter"></div>
  </k-view>
  </k-panel-inside>
</template>



<script>

import Visitors from "./Sections/Visitors.vue";
import PageStats from "./Sections/PageStats.vue";
import Devices from "./Sections/Devices.vue";
import Referers from "./Sections/Referers.vue";
import DbInformation from "./Sections/DbInformation.vue";
import Configuration from "./Sections/Configuration.vue";
import TrackingTester from "./Sections/TrackingTester.vue";
import TimeFrameInput from "./Ui/TimeFrameInput.vue";

import useI18n, { usePanel } from "kirbyuse";

export default {
  components: {
    Visitors,
    PageStats,
    Devices,
    Referers,
    DbInformation,
    Configuration,
    TrackingTester,
    TimeFrameInput,
  },
  data() {
    return {
      // Set initial tab and load it
      tab: '',
      dismissDisclaimer : false,
      isLoading : true,
    };
  },
  props: {
    label: {
      type: String,
      default: "Simple Stats",
    },
    initialtab: {
      type: String,
      default: "pagevisits",
    },
    tabs: {
      type: Array,
      default: [],
    },
    globaltimespan: {
      type: Array,
      default: [],
    },
    timePeriod: {
      type: String,
      default: "Monthly",
    },
    timeframes: {
      type: Array,
      default: [],
    },
    initialViewPeriods: {
      type: Number,
      default: -1,
    },
  },
  watch: {
    initialtab(newValue){
      //console.log('initialTab.watch', newValue);
      if(newValue) this.tab = newValue;
    }
  },
  computed: {
    tabsWithLinks() {
			let self = this;
      return this.tabs.map(function(btn){btn.click=function(e){self.onTab(btn.name);}; return btn;});
		},
  },
  // created() {

  // },
  mounted(){
    this.onTab();
  },
  methods: {
    getTabFromLocalStorage(){
      try {
        return window.localStorage.getItem('ss-tabs-menu');
      } catch (error) {
        // ignore
      }
      return null;
    },
    writeTabToLocalStorage(){
      // Don't store crap
      if(!this.tab || this.length<1) return false;
      try {
        window.localStorage.setItem('ss-tabs-menu', this.tab);
        return true;
      } catch (error) {
        // ignore
      }
      return false;
    },

    // Bind tab changing
    onTab(tab=null) {
      let mTab = tab;
      // Set initial tab position
      if( !tab || tab.length<1 ){
        // set default
        mTab = this.getTabFromLocalStorage();
      }
      if(!mTab || mTab.length<1 && this.initialtab ) mTab = this.initialtab;

      // Sanitize
      if(!this.tabs.some(aTab=>aTab.name===mTab)){
        mTab = this.tabs[0].name;
      }

      // Grab tab key and set tab acordingly
      // This is hacky and subject to break !
      this.tab = mTab;
      
      if(this?.$root?.$view?.breadcrumb){ // Kirby 3.6+
        this.$root.$view.breadcrumb[0].label = (this.tabs.find((t)=>t.name===tab)?.label) ?? mTab;
        this.$root.$view.breadcrumb[0].link = null;
      }
      else { // K5
        const panel = usePanel();
        panel.view.breadcrumb[0].label = (this.tabs.find((t)=>t.name===tab)?.label) ?? mTab;
        panel.view.breadcrumb[0].link = null;
      }
      this.writeTabToLocalStorage();
    },
    
  },
};
</script>

<style lang="less">

/** Remove the bottom margin from the header if it is followed by time-frame-input */
.k-header:has(+ .ss-timeframe-input) {
	margin-bottom: .5rem;
}

.k-simplestats-view {

  .k-tabs {
    border-bottom: 1px solid var(--color-border);
  }

// Todo: Are these styles still used ?
.row-percent p, .row-percent span.visualiser {
  float: left;
  background-color: rgba(46, 64, 87,.4);// .8);
  color: white;
  display: inline-block;
  height: 1em;
  width: 0%; /* Default for unvalid values */
}

.row-percent span.number {
  display: inline-block;
  margin-left: -100%;
}

.hover-to-help {
  position: relative;

  .k-icon {
    display: inline;
  }

  .help {
    display: inline-block;
    visibility: hidden;
    z-index: 1;
    position: relative;
    left: 10px;
    top: 0;
    overflow: visible;
    width: 0;
    height: 0;

    .k-text {
      width: 350px;
      background-color: #efefef;
      border: 1px solid black;
      padding: 8px 10px;
      color: black;
    }
  }
  &:hover .help {
    //display: inline-block;
    visibility: visible;
  }
}
}

// To grab color from JS
#chart-default-color-getter {
  color: light-dark(var(--color-dark), var(--color-light));
  display: none;
}
</style>
