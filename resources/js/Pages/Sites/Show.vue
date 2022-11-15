<script setup>
import { usePage, useForm } from "@inertiajs/inertia-vue3";
import RoutesTable from "@/Components/RoutesTable.vue";
import Pagination from "@/Components/Pagination.vue";
import { Link } from "@inertiajs/inertia-vue3";
import ActionsMenu from "@/Components/ActionsMenu.vue";


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
