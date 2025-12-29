<script>
import { usePanel, useApi } from "kirbyuse";

export default {
  extends: "k-pages-section",

  props: {
    sectionName: {
      type: String,
      required: true,
    },
    dateFrom: {
      type: String,
      default: null,
    },
    dateTo: {
      type: String,
      default: null,
    },
  },

  data() {
    return {
      isLoading: true,
      isProcessing: false,
      error: "",
    };
  },

  mounted() {
    this.load();
  },

  watch: {
    dateFrom: "reload",
    dateTo: "reload",
  },

  methods: {
    reload() {
      this.load(true);
    },

    dateQueryString(startWith = "?") {
      if (!this.dateFrom || !this.dateTo) return "";
      return `${startWith}dateFrom=${this.dateFrom}&dateTo=${this.dateTo}`;
    },

    async load(reload = false) {
      if (!reload) this.isLoading = true;
      this.isProcessing = true;
      this.error = "";

      try {
        const api = useApi();
        const response = await api.get(
          `simplestats/${this.sectionName}${this.dateQueryString()}`
        );

        this.loadData(response);
      } catch (error) {
        this.handleError(error);
      } finally {
        this.isLoading = false;
        this.isProcessing = false;
      }
    },

    handleError(error) {
      const message = error?.message || "Unknown error";
      this.error = message;

      if (this.$store?.dispatch) {
        this.$store.dispatch("notification/error", message);
        return;
      }

      const panel = usePanel();
      panel.error(message, true);
    },

    // Intended to be overridden by extending sections
    loadData(apiResponse) {},
  },
};
</script>
