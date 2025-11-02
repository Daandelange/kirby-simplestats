

<script>

import { usePanel, useApi } from 'kirbyuse';

export default {
  extends: 'k-pages-section', // Re-use loading mechanisms
  data() {
    return {
      isLoading: true,
      error: "",
    }
  },
  props: {
    sectionName : {
      type: String,
      required: true,
    },
    dateFrom : {
      type: String,
      // required: true,
      default: null,
    },
    dateTo : {
      type: String,
      // required: true,
      default: null,
    },
  },
  created() {
	},
	destroyed() {
	},
  mounted() {
		this.load();
	},
  computed: {
    
  },
  watch: {
    // Fetch new data when date changes
    dateFrom(){
      this.load(true);
    },
    dateTo(){
      this.load(true);
    },
  },
  methods: {
    // Getter
    dateQueryString(startwith='?'){
      if(!this.dateFrom || !this.dateTo) return '';
      return ''+startwith+'dateFrom='+this.dateFrom+'&dateTo='+this.dateTo;
    },

    // Heavily based on ModelsSection.vue::load() (the k-pages-section parent)
    async load(reload) {
      if(!reload) this.isLoading = true;
      this.isProcessing = true;
      // Load configuration
      try {
        //const response = await this.$api.get("simplestats/"+this.sectionName+this.dateQueryString()); // K3
        const api = useApi();
        const response = await api.get("simplestats/"+this.sectionName+this.dateQueryString()); // K5

        this.loadData(response);
      } catch (error) {
        this.error = error.message;
        if(this.$store?.dispatch){ // K3
          this.$store.dispatch("notification/error", error.message??'Unknown error');
        }
        else { // K5+
          const panel = usePanel();
          //panel.notification.info(error.message??'Unknown error'); // Bottom notification
          panel.error(error.message??'Unknown error', true); // Center error msg
        }
        //console.log("Error=", error.message, { e: error });
      } finally {
        this.isLoading = false;
        this.isProcessing = false;
      }
    },
    // To be implemented by each section
    loadData(apiResponse){
      // console.log('DummyLoadData!', apiResponse);
    },
  },
};
</script>

<style lang="scss">

</style>
