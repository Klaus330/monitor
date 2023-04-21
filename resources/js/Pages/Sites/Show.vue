<script setup>
import { usePage, useForm } from "@inertiajs/inertia-vue3";
import RoutesTable from "@/Components/RoutesTable.vue";
import Pagination from "@/Components/Pagination.vue";
import { Link } from "@inertiajs/inertia-vue3";
import ActionsMenu from "@/Components/ActionsMenu.vue";
import Metrics from "@/Components/Lighthouse/Metrics.vue";
import Timeline from "@/Components/Lighthouse/Timeline.vue";
// import { Chart } from "highcharts-vue";
// import HighChart from "highcharts";

// console.log(HighChart.chart('container', {
//   chart: {
//     type: 'timeline',
//   }
// }))


// console.log(Chart);

// let chartOptions = {
//   series: [
//     {
//       data: [1, 2, 3], // sample data
//     },
//   ],
// };

let actionLinks = [
  {
    url: "#",
    displayName: "Request new uptime run",
  },
  {
    url: route("site.configuration", { site: usePage().props.value.site.id }),
    displayName: "Settings",
  },
];
</script>

<template>
  <div class="w-full sm:px-6 lg:px-8 py-5">
    <div class="bg-white rounded p-3">
      <div>
        <div class="flex items-start justify-between px-2">
          <h1 class="font-bold text-2xl mb-4">Overview</h1>

          <ActionsMenu :links="actionLinks" />
        </div>
        <div>
          <Timeline />
          <Metrics />

          <!-- <Player/> -->
          <!-- <RoutesTable
            :siteRoutes="$page.props.siteRoutes.data"
            :site="$page.props.site"
          /> -->
          <!-- <Pagination :links="$page.props.siteRoutes.links" class="mt-10" /> -->
        </div>
      </div>
    </div>
  </div>
</template>
