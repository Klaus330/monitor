<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { reactive } from "vue";
import Pagination from "@/Components/Pagination.vue"


let props = defineProps({
  site: Object,
  siteRoutes: Object,
});

let columns = reactive(["Site", "Status", "Found On", "Last Checked At"]);

let hasRoutesRegistered = () => {
  return props.siteRoutes.data.length > 0;
};

</script>

<template>
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th
          scope="col"
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          v-for="column of columns"
          :key="column"
        >
          {{ column }}
        </th>
      </tr>
    </thead>
    <tbody v-if="hasRoutesRegistered()">
      <tr
        class="bg-white hover:bg-indigo-50 rounded"
        v-for="siteRoute of siteRoutes.data"
        :key="`sites-${siteRoute.siteRoute}`"
      >
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-400">
          <Link
            :href="route('site.route.show', { site: site.id, route: siteRoute.id })"
            >{{ siteRoute.route }}</Link
          >
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <div class="flex items-center">
            {{ siteRoute.http_code ?? "not visited yet" }}
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <div class="flex items-center">
            {{ siteRoute.found_on ?? "not visited yet" }}
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <div class="flex items-center">
            {{ siteRoute.last_check ?? "not visited yet" }}
          </div>
        </td>
      </tr>
    </tbody>

    <tbody class="flex flex-col items-center justify-center p-3 w-full" v-else>
      <tr span="5" class="w-full">
        <p class="mt-2">No links verified yet.</p>
      </tr>
    </tbody>
  </table>

  <Pagination :links="siteRoutes.links" class="mt-10"/>
</template>
