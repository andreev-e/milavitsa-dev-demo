<template>
  <div>
    <el-button @click="loadStat">Обновить</el-button>
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
        v-if="data === null"
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
      <el-table-column
        label="Действия"
        sortable
      >
        <template slot-scope="scope">
          <el-button icon="el-icon-document" @click="newSegment(scope.row.channel)">Новый сегмент (TODO)</el-button>
        </template>

      </el-table-column>
    </el-table>
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
    },
    data: {
      type: String,
      required: false,
      default: null,
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
      const { data } = await getStatistic(this.id, { data: this.data })
      this.stat = data
      this.loading = false
    },
    newSegment(channel) {
      this.$prompt('Введите название сегмента', 'Новый сегмент', {
          confirmButtonText: 'Создать',
          cancelButtonText: 'Отменить',
        }).then(({ value }) => {
          this.$message({
            type: 'success',
            message: 'Будем создавать (todo) сегмент ' + value + ' канала ' + channel + ' цель ' + this.data,
          });
        }).catch(() => {
          // this.$message({
          //   type: 'error',
          //   message: 'Ошибка',
          // });
        });
    }
  }
}
</script>

<style scoped>

</style>
