<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import BrokenLinks from "@/Components/BrokenLinksTable.vue";


console.log(usePage().props.value.site)
</script>

<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white rounded p-3 mt-5">
        <div>
            <h2>{{ $page.props.site.name }}</h2>
        </div>


            <div>
                <h1 class="font-bold text-2xl py-2">Broken Links</h1>

                <div>
                    <BrokenLinks :routes="$page.props.site.routes"/>
                </div>
            </div>
      
      </div>
    </div>
  </AppLayout>
</template>
