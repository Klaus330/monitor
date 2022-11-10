<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import RoutesTable from "@/Components/RoutesTable.vue";
import { Link } from "@inertiajs/inertia-vue3";
</script>

<template>
    <div class="w-full sm:px-6 lg:px-8 py-5">
      <div class="bg-white rounded p-3">
        <div>
          <h1 class="font-bold text-2xl mb-4">Crawled Routes</h1>
          <div>
            <RoutesTable
              :siteRoutes="$page.props.siteRoutes"
              :site="$page.props.site"
            />
          </div>
        </div>
      </div>
    </div>
</template>
