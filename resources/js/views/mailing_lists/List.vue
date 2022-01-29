<template>
  <div>
    <el-card>
      <div slot="header" class="clearfix">
        <h1>Рассылки</h1>
        <el-button type="success" icon="el-icon-plus" style="float: right;" @click="create">Создать</el-button>
      </div>
      <el-table
        :data="list"
        v-loading="loading"
        :default-sort="listQuery.sort"
        @sort-change="sortChanged"
      >
        <el-table-column prop="id" label="ID" sortable="custom" width="75px">
          <template slot-scope="scope">
            <router-link :to="`mailing_lists/${scope.row.id}`">{{ scope.row.id }}</router-link>
          </template>
        </el-table-column>
        <el-table-column prop="name" label="Название" sortable="custom">
          <template slot-scope="scope">
            <router-link :to="`mailing_lists/${scope.row.id}`">{{ scope.row.name }}</router-link>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="Статус" sortable="custom">
          <template slot-scope="scope">
            {{ statuses[scope.row.status] }}
          </template>
        </el-table-column>
        <el-table-column prop="start" label="Запланирована на" sortable="custom" width="200px;">
          <template slot-scope="scope">
            {{ scope.row.start }}
          </template>
        </el-table-column>
        <el-table-column prop="start" label="Разрешено рассылать" width="150px;">
          <template slot-scope="scope">
            <span v-if="scope.row.allow_send_from && scope.row.allow_send_to">
                {{ scope.row.allow_send_from }}&nbsp;-&nbsp;{{ scope.row.allow_send_to }}
            </span>
            <span v-else>Круглосуточно</span>
          </template>
        </el-table-column>
        <el-table-column prop="sms" label="Сегменты">
          <template slot-scope="scope">
            <el-tag v-for="(segment, index) in scope.row.segments" size="mini" :key="`seg_${index}`">
              {{ segment }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="sms" label="Каналы" sortable="custom">
          <template slot-scope="scope">
            <el-tag v-for="(channel, index) in scope.row.selected_channels" size="mini" :key="`chan_${index}`">
              {{ channel }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="name" label="Действия">
          <template slot-scope="scope">
            <el-button v-if="scope.row.status === 'blueprint' || scope.row.status === 'submitted'" type="danger" size="mini" icon="el-icon-delete" round @click="drop(scope.row.id)" />
          </template>
        </el-table-column>
      </el-table>
      <pagination
        v-show="total > 0"
        :limit.sync="listQuery.limit"
        :page.sync="listQuery.page"
        :total="total"
        @pagination="loadList"
      />
    </el-card>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination';
import MailingListResource from '@/api/mailing_list.js'

const mailingList = new MailingListResource();

export default {
  components: { Pagination },
  data() {
    return {
      list: [],
      total: 0,
      listQuery: {
        limit: 10,
        page: 1,
        sort: { prop: 'id', order: 'descending' }
      },
      loading: false,
      statuses: {
        blueprint: 'Черновик',
        submitted: 'Запланирована',
        sending: 'Идет рассылка',
      }
    }
  },
  mounted() {
    this.loadList();
  },
  methods: {
    async loadList() {
      this.loading = true
      const { data, meta } = await mailingList.list(this.listQuery)
      this.list = data
      this.total = meta.total
      this.loading = false
    },
    create() {
      this.$router.push('/admin/mailing_lists/create')
    },
    async deleteItem(id) {
      this.loading = true
      await mailingList.destroy(id);
      this.$message({
        type: 'success',
        message: 'Удалено'
      });
      this.listQuery.page = 1
      this.loadList()
    },
    sortChanged(data) {
      if (data.order) {
        this.listQuery.sort = { prop: data.prop, order: data.order };
      } else {
        this.listQuery.sort = null;
      }
      this.loadList();
    },
    drop(id) {
      this.$confirm('Точно удалить?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Отмена',
        type: 'warning'
      }).then(() => {
        this.deleteItem(id)
      }).catch((e) => {
        console.log(e);
      });
    }
  }
}
</script>

<style>
.el-table .cell {
  word-break: normal;
}
</style>
