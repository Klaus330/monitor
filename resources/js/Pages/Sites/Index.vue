<script setup>
import SitesTable from "@/Pages/Sites/SitesTable.vue";
import CreateForm from "@/Pages/Sites/CreateForm.vue";
</script>

<template>
  <div class="flex gap-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex-1 h-fit">
      <div class="border-b border-gray-200 flex-1">
        <SitesTable :sites="$page.props.sites" />
      </div>
    </div>
    <div class="bg-white shadow-xl rounded-lg h-fit">
      <CreateForm />
    </div>
  </div>
  <div class="mt-8">
    // TODO: Add the pagination
  </div>
</template>
