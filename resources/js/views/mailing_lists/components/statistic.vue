<template>
  <div>
    <el-table
      v-loading="loading"
      :data="stat"
      stripe
    >
      <el-table-column
        prop="channel"
        label="Канал"
        sortable
      />
      <el-table-column
        prop="status"
        label="Статус"
        sortable
      >
        <template slot-scope="scope">
          {{ statuses[scope.row.status] }}
        </template>
      </el-table-column>
      <el-table-column
        prop="total"
        label="Всего"
        sortable
      />
    </el-table>
    <el-button @click="loadStat">Обновить</el-button>
  </div>
</template>

<script>
import { getStatistic } from '@/api/mailing_list.js'
import { message_statuses } from '@/const/lists'

export default {
  props: {
    id: {
      type: Number,
      required: true,
    }
  },
  data() {
    return {
      stat: [],
      statuses: [],
      loading: false,
    };
  },
  mounted() {
    this.loadStat();
    this.statuses = message_statuses;
  },
  methods: {
    async loadStat() {
      this.loading = true
      const { data } = await getStatistic(this.id)
      this.stat = data
      this.loading = false
    },
  }
}
</script>

<style scoped>

</style>
