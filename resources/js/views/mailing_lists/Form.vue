<template>
  <div>
    <el-form label-position="top">
      <el-card v-loading="loading">
        <h1>Рассылка</h1>
        <el-row :gutter="15">
          <el-col :span="12">
            <el-form-item label="Название">
              <el-input v-model="form.name" type="text" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="Включить каналы">
              <el-switch v-model="form.sms" active-text="SMS" />
              <el-switch v-model="form.email" active-text="Email" />
              <el-switch v-model="form.telegram" active-text="Telegram" />
              <el-switch v-model="form.whatsapp" active-text="Whatsapp" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="На дату и время">
              <el-switch v-model="start" active-text="Сразу" inactive-text="Запланировать" />
            </el-form-item>
            <el-form-item>
              <el-date-picker
                v-if="!start"
                v-model="form.start"
                type="datetime"
                placeholder="Выбрать дату и время"
                :picker-options="pickerOptions"
                format="yyyy.MM.dd HH:mm"
              />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-button type="success" @click="save">Сохранить</el-button>
          </el-col>
        </el-row>
      </el-card>
    </el-form>
  </div>
</template>

<script>
import MailingListResource from '@/api/mailing_list.js'

const mailingList = new MailingListResource();

export default {
  data() {
    return {
      loading: false,
      form: {
        id: null,
        name: null,
        sms: false,
        email: false,
        telegram: false,
        whatsapp: false,
        start: null,
      },
      pickerOptions: {
        firstDayOfWeek: 1,
        shortcuts: [
          {
            text: 'Завтра',
            onClick(picker) {
              const date = new Date();
              date.setTime(date.getTime() + 3600 * 1000 * 24);
              picker.$emit('pick', date);
            }
          },
          {
            text: 'Через неделю',
            onClick(picker) {
              const date = new Date();
              date.setTime(date.getTime() + 3600 * 1000 * 24 * 7);
              picker.$emit('pick', date);
            }
          }
        ]
      },
    };
  },
  computed: {
    start: {
      get: function () {
        return this.form.start === null
      },
      set: function (val) {
        if (val) {
          this.form.start = null
        } else {
          this.form.start = new Date()
        }
      },
    }
  },
  mounted() {
    const id = this.$route.params && this.$route.params.id;
    if (id !== 'create') {
      this.form.id = id;
      this.loadItem();
    }
  },
  methods: {
    async loadItem() {
      this.loading = true
      const { data } = await mailingList.get(this.form.id)
      this.form = data
      this.loading = false
    },
    async save() {
      this.loading = true
      if (this.form.id === null) {
        const { data } = await mailingList.store(this.form)
        this.form = data
      } else {
        const { data } = await mailingList.update(this.form.id, this.form)
        this.form = data
      }
      this.loading = false
      this.$router.push({name: 'mailing-lists-list'})
    }
  },
}
</script>

<style scoped>

</style>
