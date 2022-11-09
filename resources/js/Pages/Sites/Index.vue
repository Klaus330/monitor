<script setup>
import SitesTable from "@/Pages/Sites/SitesTable.vue";
import CreateForm from "@/Pages/Sites/CreateForm.vue";
import Pagination from "@/Components/Pagination.vue";

</script>

<template>
  <div class="flex gap-4">
    <div class="overflow-hidden sm:rounded-lg flex-1 h-fit">
      <div class="border-b border-gray-200 flex-1 bg-white shadow-xl ">
        <SitesTable :sites="$page.props.sites.data" />
      </div>
      <div class="mt-8">
        <Pagination :links="$page.props.sites.links"/>
      </div>
    </div>
    <div class="bg-white shadow-xl rounded-lg h-fit">
      <CreateForm />
    </div>
  </div>

</template>
