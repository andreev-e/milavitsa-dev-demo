<template>
  <div>
    <el-row>
      <el-col :span="12">
        <h1>Рассылки</h1>
      </el-col>
      <el-col :span="12" style="text-align: right">
        <el-button type="success" icon="el-icon-plus" @click="create">Создать</el-button>
      </el-col>
    </el-row>

    <el-table
      :data="list"
      v-loading="loading"
      :default-sort="listQuery.sort"
      @sort-change="sortChanged"
    >
      <el-table-column prop="id" label="ID" sortable="custom" width="50px">
        <template slot-scope="scope">
          <router-link :to="`mailing_lists/${scope.row.id}`">{{ scope.row.id }}</router-link>
        </template>
      </el-table-column>
      <el-table-column prop="name" label="Название" sortable="custom">
        <template slot-scope="scope">
          <router-link :to="`mailing_lists/${scope.row.id}`">{{ scope.row.name }}</router-link>
        </template>
      </el-table-column>
      <el-table-column prop="start" label="Запланирована на" sortable="custom" width="250px;">
        <template slot-scope="scope">
          {{ scope.row.start }}
        </template>
      </el-table-column>
      <el-table-column prop="sms" label="SMS" sortable="custom">
        <template slot-scope="scope">
          <i v-if="scope.row.sms" class="el-icon-check" />
        </template>
      </el-table-column>
      <el-table-column prop="email" label="Email" sortable="custom">
        <template slot-scope="scope">
          <i v-if="scope.row.email" class="el-icon-check" />
        </template>
      </el-table-column>
      <el-table-column prop="telegram" label="Telegram" sortable="custom">
        <template slot-scope="scope">
          <i v-if="scope.row.telegram" class="el-icon-check" />
        </template>
      </el-table-column>
      <el-table-column prop="whatsapp" label="WhatsApp" sortable="custom">
        <template slot-scope="scope">
          <i v-if="scope.row.whatsapp" class="el-icon-check" />
        </template>
      </el-table-column>
      <el-table-column prop="name" label="Действия">
        <template slot-scope="scope">
          <el-button type="danger" size="mini" icon="el-icon-delete" round @click="drop(scope.row.id)" />
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

<style scoped>

</style>
