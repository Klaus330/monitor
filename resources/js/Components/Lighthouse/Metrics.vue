<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { audits } from "@/report.json";

let metrics = audits["metrics"].details.items[0];

let displayMetric = (metric) => {
    if(metric < 1000) 
    {
        return metric + 'ms';
    }

    return (metric / 1000) + 's';
}

</script>

<template>
  <div class="w-full">
    <div class="grid grid-cols-4 gap-2 gap-y-5">
      <div class="flex flex-col items-start justify-start">
        <span class="text-indigo-600 text-xl font-bold">Time to interactive</span>
        <span class="text-2xl text-green-500  font-bold">{{ displayMetric(metrics.interactive) }}</span>
      </div>
      <div class="flex flex-col items-start justify-start">
        <span class="text-indigo-600 text-xl font-bold">Firt Contenful Paint</span>
        <span class="text-2xl text-green-500  font-bold">{{ displayMetric(metrics.firstContentfulPaint) }}</span>
      </div>
      <div class="flex flex-col items-start justify-start">
        <span class="text-indigo-600 text-xl font-bold">Largest Contentful Paint</span>
        <span class="text-2xl text-green-500  font-bold">{{ displayMetric(metrics.largestContentfulPaint) }}</span>
      </div>
      <div class="flex flex-col items-start justify-start">
        <span class="text-indigo-600 text-xl font-bold">Speed Index</span>
        <span class="text-2xl text-green-500  font-bold">{{ displayMetric(metrics.speedIndex) }}</span>
      </div>

       <div class="flex flex-col items-start justify-start">
        <span class="text-indigo-600 text-xl font-bold">Total Blocking Time</span>
        <span class="text-2xl text-green-500  font-bold">{{ displayMetric(metrics.totalBlockingTime) }} </span>
      </div>
      <div class="flex flex-col items-start justify-start">
        <span class="text-indigo-600 text-xl font-bold">Cumulative Layout Shift</span>
        <span class="text-2xl text-green-500  font-bold">{{ Math.round(metrics.cumulativeLayoutShift * 100) / 100 }}</span>
      </div>
      <div class="flex flex-col items-start justify-start">
        <span class="text-indigo-600 text-xl font-bold">Page Weight</span>
        <span class="text-2xl text-green-500  font-bold">{{ displayMetric(metrics.speedIndex) }}</span>
      </div>
    </div>
  </div>
</template>
