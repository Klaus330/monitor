<script setup>
import { ref } from "vue";
import { usePage, useForm } from "@inertiajs/inertia-vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ActionsMenu from "@/Components/ActionsMenu.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Toggle from "@/Components/Toggle.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";

let formValues = {};
Object.values(usePage().props.value.formValues).forEach((item, index, array) => {
  let value = Object.values(item)[0];

  if (value == "0" || value === "1") {
    value = value === "1";
  }

  formValues[Object.keys(item)[0]] = value;
});

let form = useForm(formValues);

let saveChanges = () => {
  form.patch(route("site.configuration.update", usePage().props.value.site.id), {
    preserveScroll: true,
  });
};

let currentSettingsPage =
  window.location.hash === "" ? "general" : window.location.hash.slice(1);

let currentSettingsPageIndex = Object.values(
  usePage().props.value.settingGroups
).findIndex((item, index) => {
  return item.name === currentSettingsPage;
});

const selectedTab = ref(currentSettingsPageIndex);

function changeTab(index) {
  window.location.hash = Object.values(usePage().props.value.settingGroups).find((item, id) => id === index).name
  selectedTab.value = index;
}
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
      <form @submit.prevent="saveChanges" >
        <div class="w-full pt-3 pb-10 sm:px-0">
          <TabGroup :selectedIndex="selectedTab" @change="changeTab">
            <TabList class="flex space-x-1 my-2 w-full gap-5">
              <Tab
                v-for="group in $page.props.settingGroups"
                :key="group"
                as="template"
                v-slot="{ selected }"
                :data-headlessui-state="{ selected: group.name == currentSettingsPage }"
              >
                <button
                  :class="[
                    'bg-white focus:outline-none border-b-2',
                    selected ? 'border-indigo-600' : 'border-gray-300',
                  ]"
                >
                  {{ group.display_name }}
                </button>
              </Tab>
            </TabList>

            <TabPanels class="mt-2 outline-none focus:outline-none">
              <TabPanel
                :class="[
                  'rounded-xl bg-white outline-none',
                  'ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 outline-none focus:outline-none focus:ring-2',
                ]"
                v-for="(config, id) in $page.props.configuration"
                :key="id"
              >
                <div class="my-4" v-for="(setting, id) in config" :key="id">
                  <div v-if="setting.value_type === 'checkbox'" class="flex flex-col">
                    <Toggle
                      :id="setting.name"
                      :value="`${form[`${setting.name}`]}`"
                      :label="setting.display_name"
                      v-model:checked="form[`${setting.name}`]"
                    />
                    <span class="pt-2 text-gray-500 text-sm">{{
                      setting.description
                    }}</span>
                    <InputError class="mt-2" :message="form.errors[`${setting.name}`]" />
                  </div>
                  <div v-else>
                    <InputLabel :for="setting.name" :value="setting.display_name" />

                    <TextInput
                      :id="setting.name"
                      :type="setting.value_type"
                      :value="form[`${setting.name}`]"
                      v-model="form[`${setting.name}`]"
                      class="mt-1 block w-full"
                    />
                    <span class="pt-3 text-gray-500 text-sm">{{
                      setting.description
                    }}</span>
                    <InputError class="mt-2" :message="form.errors[`${setting.name}`]" />
                  </div>
                </div>
              </TabPanel>
            </TabPanels>
          </TabGroup>
        </div>

        <div class="text-right">
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
