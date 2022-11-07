<script setup>
import { reactive } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

let props = defineProps({
  sites: Array,
});

let columns = reactive([
  "Site",
  "Uptime",
  "SSl",
  "Schedulers",
  "Broken Links",
  "Actions",
]);

let hasSitesRegistered = () => {
  return Array.isArray(props.sites) && props.sites.length > 0;
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
    <tbody v-if="hasSitesRegistered()">
      <tr class="bg-white" v-for="site of sites" :key="`sites-${site.id}`">
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-400">
          <Link :href="route('site.show', { site: site.id })">{{ site.name }}</Link>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <div class="flex items-center">
            {{ site.status }}
            <!-- <a
              href="{{ route('sites.show', ['site' => $site->id]) }}"
              data-tippy-content="See uptime page"
            >
            
            </a> -->
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <div class="flex items-center">
            {{ site.ssl ?? "null" }}
            <!-- <a
              href="{{ route('site.ssl-certificate-health', ['site' => $site->id]) }}"
              data-tippy-content="See certificate health"
            >
              
            </a> -->
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <div class="flex items-center">
            {{ site.schedulers ?? "null" }}
            <!-- <a
              href="{{ route('schedulers.index', ['site' => $site->id]) }}"
              data-tippy-content="See schedulers page"
            >

            </a> -->
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 flex items-center">
          {{ site.brokenLinks ?? "null" }}
          <!-- <a
            href="{{ route('sites.broken-links', ['site' => $site->id]) }}"
            data-tippy-content="See broken links page"
          >

          </a> -->
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          null
          <!-- <button
            class="text-green-500 hover:text-green-600"
            data-tippy-content="Run Uptime Job Now"
          >
            <i class="fa-solid fa-play"></i>
          </button> -->
        </td>
      </tr>
    </tbody>

    <tbody class="flex flex-col items-center justify-center p-3" v-else>
      <tr span="5">
        <i class="fa-solid fa-globe text-5xl"></i>
        <p class="mt-2">No monitors registered yet.</p>
      </tr>
    </tbody>
  </table>
</template>
