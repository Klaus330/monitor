<script setup>
import RoutesTable from "@/Components/RoutesTable.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { ref } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import ActionsMenu from "@/Components/ActionsMenu.vue";

let routeModal = ref(false);
let routeHistory = ref([]);

let fetchRouteHistory = (e) => {
  console.log(e);

  axios
    .get(
      route("site.route.show", { site: usePage().props.value.site.id, route: e.route.id })
    )
    .then((response) => {
      routeHistory.value = response.data;
      routeModal.value = true;
    });
};

let actionLinks = [
  {
    url: "#",
    displayName: "Request new broken links run",
  },
  {
    url: route("site.configuration", { site: usePage().props.value.site.id }),
    displayName: "Settings",
  },
];
</script>

<template>
  <div class="sm:px-6 lg:px-8 py-5 w-full">
    <div class="bg-white p-3">
      <div class="flex items-start justify-between px-2">
        <h2 class="text-2xl font-bold mb-3">Broken routes</h2>

        <ActionsMenu :links="actionLinks" />
      </div>
      <RoutesTable
        :siteRoutes="$page.props.brokenRoutes.data"
        :site="$page.props.site"
        :hasActions="true"
        @fetchroute="fetchRouteHistory"
      />
      <Pagination :links="$page.props.brokenRoutes.links" class="mt-10" />

      <DialogModal :show="routeModal" @close="routeModal = false">
        <template #title>Broken route history</template>

        <template #content>
          <div class="mt-4">
            <RoutesTable
              :siteRoutes="routeHistory"
              :site="$page.props.site"
              :hasActions="false"
            />
          </div>
        </template>

        <template #footer>
          <SecondaryButton
            @click="
              routeModal = false;
              routeHistory = [];
            "
          >
            Close
          </SecondaryButton>
        </template>
      </DialogModal>
    </div>
  </div>
</template>
