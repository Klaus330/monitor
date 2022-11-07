<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import { reactive } from "vue";

let props = defineProps({
    routes: Array
})

let columns = reactive([
  "Site",
  "Status",
  "Found ON",
  "Last Checked At",
]);

let hasRoutesRegistered = () => {
    return true;
}

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
      <tr class="bg-white" v-for="route of routes" :key="`sites-${route.id}`">
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-400">
          <Link>{{ route.route }}</Link>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <div class="flex items-center">
            {{ route.http_code }}
            <!-- <a
              href="{{ route('sites.show', ['site' => $site->id]) }}"
              data-tippy-content="See uptime page"
            >
            
            </a> -->
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <div class="flex items-center">
            {{ route.found_on ?? "null" }}
            <!-- <a
              href="{{ route('site.ssl-certificate-health', ['site' => $site->id]) }}"
              data-tippy-content="See certificate health"
            >
              
            </a> -->
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <div class="flex items-center">
            {{ route.updated_at ?? "null" }}
            <!-- <a
              href="{{ route('schedulers.index', ['site' => $site->id]) }}"
              data-tippy-content="See schedulers page"
            >

            </a> -->
          </div>
        </td>
      </tr>
    </tbody>

    <tbody class="flex flex-col items-center justify-center p-3" v-else>
      <tr span="5">
        <i class="fa-solid fa-globe text-5xl"></i>
        <p class="mt-2">No links verified yet.</p>
      </tr>
    </tbody>
  </table>
</template>