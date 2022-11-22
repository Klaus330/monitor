<script setup>
import RoutesTable from "@/Components/RoutesTable.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { ref } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import ActionsMenu from "@/Components/ActionsMenu.vue";

let routeModal = ref(false);
let routeHistory = ref([]);
let loadedMoreRoutes = ref(usePage().props.value.siteRoutes.data.length > 15);

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

let loadMoreRoutes = async () => {
  if (loadedMoreRoutes.value) {
    return;
  }

  loadedMoreRoutes.value = true;
  axios
    .get(route("site.routes.all", { site: usePage().props.value.site.id }))
    .then((res) => {
      usePage().props.value.siteRoutes.data = res.data;
      // console.log(res);
    });
};
</script>

<template>
  <div class="mt-5 bg-white w-full">
    <div
      class="flex items-start justify-between border-b border-gray-200 sm:px-6 lg:px-8 pt-5 pb-2"
    >
      <h2 class="text-2xl font-bold mb-3">Broken Routes</h2>

      <ActionsMenu />
    </div>
    <div class="sm:px-6 lg:px-8 py-5 w-full">
      <div v-show="$page.props.brokenRoutes.data.length > 0">
        <h3 class="text-md font-semibold mb-1">We found some internal broken routes</h3>
        <RoutesTable
          :siteRoutes="$page.props.brokenRoutes.data"
          :site="$page.props.site"
          :hasActions="true"
          @fetchroute="fetchRouteHistory"
        />
      </div>

      <span
        v-show="$page.props.brokenRoutes.data.length === 0"
        class="w-full border-l-2 border-green-400 bg-green-200 p-3 rounded"
        >We haven't found any broken links.</span
      >

      <div>
        <div class="mt-10">
          <h2 class="font-bold text-2xl">
            {{ $page.props.siteRoutesCount }} Crawled Routes
          </h2>
          <h3 class="font-normal text-sm mt-1 mb-3">
            This table will contain all urls that we crawled at X.
          </h3>
          <div>
            <RoutesTable
              :siteRoutes="$page.props.siteRoutes.data"
              :site="$page.props.site"
            />
          </div>

          <div class="flex w-full items-center justify-center my-3">
            <PrimaryButton
              type="button"
              @click="loadMoreRoutes"
              :disabled="loadedMoreRoutes"
              v-show="!loadedMoreRoutes"
              >See More</PrimaryButton
            >
          </div>
        </div>

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
  </div>
</template>
