<script>

import KPagestatsSection from "../Sections/PagestatsSection.vue"

const PagestatsSectionRender = KPagestatsSection.render;

export default {

  extends: KPagestatsSection,
  mixins: [
    'k-pages-section', // Needed to manually inject (yet unresolved) KPagestatsSection mixins (for `load()`)
    'k-drawer', // Needed to inherit drawer properties
  ], 

  props: {
    uid: '',
  },

  // Custom render function that wraps a `k-drawer` around our original section template
  render(createElement) {
    const closeFn = () => { this.$emit('close') };
    const drawerProps = { attrs: { ...this.$attrs}, on: { submit: closeFn }};

    // Simply return a drawer to which we bind 
    // We put the original KPagestatsSection renderFn as childs
    return createElement(
      'k-drawer', // Element to create
      drawerProps, // Element Props
      [ // Childs (they go in the default template)
        KPagestatsSection.render.call(this, createElement),
      ],
    )
  },

  methods: {
    // Like native K5 section load but with modified URL and no setting member vars, use return instead
    async load(reload) {
      this.isProcessing = true;

      if (!reload) {
        this.isLoading = true;
      }

      try {
        const response = await this.$api.get(
          'simplestats/onepagestats/'+this.uid+(this.$attrs.dateFrom?('?dateFrom='+this.$attrs.dateFrom+'&dateTo='+this.$attrs.dateTo):''),
        );
        return response;
      } catch (error) {
        this.error = error.message;
      } finally {
        // Note: This gets called correctly even when return in try is called.
        this.isProcessing = false;
        this.isLoading = false;
      }
      return null;
    },
  }
};
</script>
