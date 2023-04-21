<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { audits, timing } from "@/report.json";
import { Chart } from "highcharts-vue";
import HighChart from "highcharts";

console.log(audits["screenshot-thumbnails"]);
let metrics = audits["metrics"].details.items[0];
let thumbnails = audits["screenshot-thumbnails"].details.items;
console.log(metrics);
let chartOptions = {
  chart: {
    type: "timeline",
    zoomType: "x",
    showAxes: false,
    height: 180,
    spacing: [-140,0,0,0],
  },
  title: {
    text: "",
    margin: 0
  },
  caption: {
    margin: 0
  },
  xAxis: {
    type: "datetime",
    visible: true,
    dateTimeLabelFormats: "seconds",
    tickInterval: 0.5,
    tickAmount: 10,
    min: 0,
    max: Math.ceil(timing['total'] / 1000),
    labels: {
      formatter: function () {
        return this.value + " s";
      },
    },
    offset: -150
  },
   colorAxis: [{}, {
        minColor: '#434348',
        maxColor: '#e6ebf5'
  }],
  yAxis: {
    visible: false,
    gridLineWidth: 0.1,
  },
  series: [
    {
      lineWidth: 1,
      dataLabels: {
        align: 'center',
        allowOverlap: false,
        alternate: false,
        verticalAlign: 'bottom',
        format:`
          <span style="font-weight: bold; color: {point.color};">{point.label}</span>
        `,
        borderColor: 'auto',
      },
      marker: {
        symbol: "circle",
      },
      colorKey: 'x',
      data: [
        {
          label: `FFTB (${Math.round(audits['server-response-time'].numericValue / 10) / 100}s)`,
          x: Math.round(audits['server-response-time'].numericValue / 10) / 100,
          description: "First FFTB",
          name: "First FFTB",
          colorValue: 11
        },
        {
          label: `First Contentful Paint (${metrics.firstContentfulPaint / 1000}s)`,
          x: metrics.firstContentfulPaint / 1000,
          description: "First Contentful Paint",
          name: "First Contentful Paint",
           dataLabels: {
            y: 200,
          },
          colorValue: 11
        },
        {
          label: `Largest Contentful Paint (${metrics.largestContentfulPaint / 1000}s)`,
          x: metrics.largestContentfulPaint / 1000,
          description: "Largest Contentful Paint",
          name: "Largest Contentful Paint",
          dataLabels: {
            y: 100,
          },
          colorValue: 20
        },
        {
          label: `Visually complete (${metrics.observedLastVisualChange / 1000}s)`,
          x: metrics.observedLastVisualChange / 1000,
          description: "Visually complete",
          name: "Visually complete",
          colorValue: 8,
          dataLabels: {
            y: 50,
          },
        },
        {
          label: `Time to interactive (${metrics.interactive / 1000}s)`,
          x: metrics.interactive / 1000,
          description: "Time to interactive",
          name: "Time to interactive",
          colorValue: 7
        },
        
      ],
    },
  ],
};
</script>

<template>
  <div class="w-full">
    <div class="flex items-start justify-start mt-10 gap-1 mt-4">
      <div class="h-40 relative" v-for="image in thumbnails">
        <span class="absolute top-0 left-0 text-white bg-gray-600 text-center rounded-t p-1 w-full text-sm">{{image.timing / 1000}}s</span>
        <img :src="image.data" alt="image" class="h-full w-full border border-gray-300 rounded" />
      </div>
    </div>

    <highcharts :options="chartOptions" class="mt-2"></highcharts>
  </div>
</template>
