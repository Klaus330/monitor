<script setup>
import RoutesTable from "@/Components/RoutesTable.vue";
</script>

<template>
  <div class="sm:px-6 lg:px-8 py-5 w-full">
    <div class="bg-white p-3">
      <h2 class="text-2xl font-bold mb-3">
        Broken routes
      </h2>
      <RoutesTable :siteRoutes="$page.props.brokenRoutes" :site="$page.props.site" />
    </div>
  </div>
</template>
