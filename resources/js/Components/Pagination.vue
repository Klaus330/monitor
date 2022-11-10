<script setup>
import { Link } from "@inertiajs/inertia-vue3";
const props = defineProps({
  links: Array,
});

console.log()
</script>

<template>
  <div class="flex items-center justify-center gap-3"  v-if="props.links.length > 3">
    <Component
      v-for="link in props.links"
      :is="link.url ? 'Link' : 'span'"
      :href="link.url"
      v-html="link.label"
      class="border-b-2  px-3"
      :disabled="link.active"
      prevent-scroll
      :class="{ 'text-gray-500' : !link.url, 'border-indigo-500 text-indigo-600': link.active }"
    />
  </div>
</template>
