<script setup>
import ReportFilter from './Partials/ReportFilter.vue'
import ReportSummary from './Partials/ReportSummary.vue'
import ReportTable from './Partials/ReportTable.vue'

defineProps({
  documents: Array,
  summary: Object,
  filters: Object,
  departments: Array,
})
</script>

<template>
  <div class="p-6 space-y-6">
    <h1 class="text-xl font-bold">📊 รายงานเอกสาร</h1>

    <ReportFilter
      :filters="filters"
      :departments="departments"
    />

    <ReportSummary
      :summary="summary"
    />

    <ReportTable
      :documents="documents"
    />
  </div>
</template>
