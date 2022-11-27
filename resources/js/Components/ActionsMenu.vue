<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import { usePage, useForm } from "@inertiajs/inertia-vue3";
import { computed } from "vue";
defineProps(["links"]);

let deleteSite = () => {
  let form = useForm({
    site_id: usePage().props.value.site.id,
  });

  form.delete(route("site.delete"));
};

let actionLink = (linkInfo) => {
  let url = linkInfo.url;

  if (linkInfo.fragment !== undefined) {
    url = url + `#${linkInfo.fragment}`;
  }

  return url;
};
</script>

<template>
  <div class="relative">
    <Menu>
      <MenuButton
        class="text-xl font-bold hover:bg-gray-100 px-2 py-1 rounded text-center"
        >...</MenuButton
      >

      <MenuItems
        class="absolute top-10 right-0 flex flex-col w-60 bg-white rounded shadow-xl border border-gray-200"
      >
        <MenuItem v-slot="{ active }">
          <a
            :class="{ 'text-blue-500': active }"
            :href="$page.props.site.url"
            target="_blank"
            class="p-3 border-b border-gray-200"
          >
            Open site in browser
          </a>
        </MenuItem>

        <MenuItem v-slot="{ active }" v-for="link in links" :key="link.url" @click="link.onClick">
          <a
            :class="{ 'text-blue-500': active }"
            :href="actionLink(link)"
            class="p-3 border-b border-gray-200"
          >
            {{ link.displayName }}
          </a>
        </MenuItem>

        <!-- <MenuItem v-slot="{ active }">
          <a
            :class="{ 'text-blue-500': active }"
            href="#"
            class="p-3 border-b border-gray-200"
          >
            Settings
          </a>
        </MenuItem> -->
        <MenuItem v-slot="{ active }">
          <button
            :class="{ 'bg-red-500 text-white': active }"
            class="p-3 text-left"
            @click.prevent="deleteSite"
          >
            Delete
          </button>
        </MenuItem>
      </MenuItems>
    </Menu>
  </div>
</template>
