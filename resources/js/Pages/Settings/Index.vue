<script setup>
import { usePage, useForm } from "@inertiajs/inertia-vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ActionsMenu from "@/Components/ActionsMenu.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Toggle from "@/Components/Toggle.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";

let form = useForm({
  crawler_delay: usePage().props.value.configuration.crawler_delay,
  respect_robots: usePage().props.value.configuration.respect_robots,
  has_uptime: usePage().props.value.configuration.has_uptime,
  has_crawlers: usePage().props.value.configuration.has_crawlers,
  has_lighthouse: usePage().props.value.configuration.has_lighthouse,
});

let saveChanges = () => {
  form.patch(route("site.configuration.update", usePage().props.value.user.id), {
    preserveScroll: true,
  });
};
</script>

<template>
  <div class="mt-5 bg-white w-full">
    <div
      class="flex items-start justify-between border-b border-gray-200 sm:px-6 lg:px-8 pt-5 pb-2"
    >
      <h2 class="text-2xl font-bold mb-3">Settings</h2>

      <ActionsMenu />
    </div>
    <div class="sm:px-6 lg:px-8 pt-2 pb-3 w-full">
      <form @submit.prevent="saveChanges">
        <div class="w-full pt-3 pb-10 sm:px-0">
          <TabGroup>
            <TabList class="flex space-x-1 my-2 w-full gap-5">
              <Tab
                as="template"
                v-slot="{ selected }"
              >
                <button
                  :class="[
                    'bg-white focus:outline-none border-b-2',
                    selected ? 'border-indigo-600' : 'border-gray-300',
                  ]"
                >
                  General
                </button>
              </Tab>
              <Tab as="template" class="" v-slot="{ selected }">
                <button
                  :class="[
                    'bg-white focus:outline-none border-b-2',
                    selected ? 'border-indigo-600' : 'border-gray-300',
                  ]"
                >
                  Uptime
                </button>
              </Tab>
              <Tab
                as="template"
                v-slot="{ selected }"
              >
                <button
                  :class="[
                    'bg-white focus:outline-none border-b-2',
                    selected ? 'border-indigo-600' : 'border-gray-300',
                  ]"
                >
                  Broken Routes
                </button>
              </Tab>
            </TabList>

            <TabPanels class="mt-2 outline-none focus:outline-none">
              <TabPanel
                :class="[
                  'rounded-xl bg-white outline-none',
                  'ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 outline-none focus:outline-none focus:ring-2',
                ]"
              >
                <div class="mb-3 border-b border-gray-300 pb-2 font-semibold mt-5">
                  <h2 class="text-xl">Checks</h2>
                </div>

                <div class="w-full flex gap-20">
                  <Toggle
                    id="crawlers"
                    :value="form.has_crawlers"
                    label="Crawlers"
                    v-model:checked="form.has_crawlers"
                  />
                  <Toggle
                    id="uptime"
                    :value="form.has_uptime"
                    label="Uptime"
                    v-model:checked="form.has_uptime"
                  />
                  <Toggle
                    id="lighthouse"
                    :value="form.has_lighthouse"
                    label="Lighthouse"
                    v-model:checked="form.has_lighthouse"
                  />
                </div>
              </TabPanel>
              <TabPanel
                :class="[
                  'rounded-xl bg-white outline-none p-3',
                  'ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2 outline-none',
                ]"
              >
              </TabPanel>
              <TabPanel
                :class="[
                  'rounded-xl bg-white outline-none',
                  'ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2 outline-none',
                ]"
              >
                <div class="my-4 w-1/4">
                  <InputLabel for="crawler_delay" value="Crawler Delay (ms):" />
                  <TextInput
                    id="crawler_delay"
                    v-model="form.crawler_delay"
                    type="number"
                    min="0"
                    max="10000"
                    class="mt-1 block w-full"
                    autofocus
                  />
                  <InputError class="mt-2" :message="form.errors.crawler_delay" />
                </div>

                <div class="my-4">
                  <div class="flex items-center justify-start gap-2">
                    <InputLabel for="respect_robots" value="Respect Robots:" />
                    <Checkbox
                      id="respect_robots"
                      :value="form.respect_robots"
                      v-model:checked="form.respect_robots"
                      type="checkbox"
                      class="block"
                    />
                  </div>
                  <InputError class="mt-2" :message="form.errors.respect_robots" />
                </div>
              </TabPanel>
            </TabPanels>
          </TabGroup>
        </div>

        <div class="mt-3 text-right">
          <PrimaryButton
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Save
          </PrimaryButton>
        </div>
      </form>
    </div>
  </div>
</template>
