<script setup>
import Index from "@/Pages/Sites/Index.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
</script>

<script>
export default {
  layout: DashboardLayout,
};
</script>

<template>
  <Head :title="'Dashboard'" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <Index />
    </div>
  </div>
</template>
