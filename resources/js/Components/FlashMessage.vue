<script>
import { usePage } from "@inertiajs/inertia-vue3";
export default {
  data() {
    return {
      show: true,
    };
  },

  watch: {
    "$page.props.flash": {
      handler(value) {
        setTimeout(() => {
          if (!this.show) {
            return;
          }

          this.show = false;
          usePage().props.value.flash.success = null;
          usePage().props.value.flash.error = null;
        }, 1000);

        this.show = value.error != null || value.success != null;
      },
      deep: true,
    },
  },
};
</script>
<template>
  <div>
    <div
      v-if="$page.props.flash.success && show"
      class="absolute bottom-10 right-10 bg-green-500 rounded shadow-xl p-3 flex items-center justify-between gap-3 text-white"
    >
      <div>
        <span class="text-md font-semibold">{{ $page.props.flash.success.trim() }}</span>
      </div>
      <button type="button" @click="show = false" class="hover:text-gray-100">
        &times;
      </button>
    </div>

    <div
      v-if="
        ($page.props.flash.error || Object.keys($page.props.errors).length > 0) && show
      "
      class="absolute bottom-10 right-10 bg-red-500 rounded shadown-xl p-3 text-white flex items-center justify-between gap-3"
    >
      <div class="">
        <span
          v-if="$page.props.flash.error"
          v-text="$page.props.flash.error"
          class="text-md font-semibold"
        ></span>
        <div v-else>
          <span
            v-if="Object.keys($page.props.errors).length === 1"
            class="text-md font-semibold"
            >There is one error</span
          >
          <span v-else class="text-md font-semibold"
            >There are {{ Object.keys($page.props.errors).length }} errors.</span
          >
        </div>
      </div>
      <button type="button" @click="show = false" class="hover:text-gray-100">
        &times;
      </button>
    </div>
  </div>
</template>
