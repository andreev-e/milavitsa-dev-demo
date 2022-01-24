<template>
  <div>
    <el-form :model="form" :rules="rules" ref="form" label-position="top">
      <el-card v-loading="loading">
        <div slot="header" class="clearfix">
          <h1>Рассылка</h1>
        </div>
        <h2>Общие настроки</h2>
        <el-row :gutter="15">
          <el-col :span="12">
            <el-form-item label="Название списка" prop="name">
              <el-input v-model="form.name" type="text" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="Включить каналы" prop="channels">
              <el-switch v-model="form.sms" active-text="SMS" />
              <el-switch v-model="form.email" active-text="Email" />
              <el-switch v-model="form.telegram" active-text="Telegram" />
              <el-switch v-model="form.whatsapp" active-text="Whatsapp" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="Дата и время начала рассылки">
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
            <el-form-item label="Диапазон времени доставки сообщений" prop="allow_send">
              <el-time-select
                v-model="form.allow_send_from"
                placeholder="C"
                :picker-options="timeSelectOptions"
              >
              </el-time-select>
              <el-time-select
                v-model="form.allow_send_to"
                placeholder="По"
                :picker-options="timeSelectOptions"
              >
              </el-time-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="15">
          <el-col :span="24">
            <h2>Сегменты</h2>
          </el-col>
        </el-row>
        <el-row :gutter="15">
          <el-col :span="24">
            <el-button type="success" @click="validate">Сохранить</el-button>
          </el-col>
        </el-row>
      </el-card>
    </el-form>
  </div>
</template>

<script>
import MailingListResource from '@/api/mailing_list.js'

const mailingList = new MailingListResource();


const checkAllowSend = function (rule, value, callback) {
  if ((this.form.allow_send_from && this.form.allow_send_to) ||
    (!this.form.allow_send_from && !this.form.allow_send_to)) {
    callback();
  } else {
    callback(new Error('Укажите диапазон или оставьте пустым'));
  }
};

const checkChannels = function (rule, value, callback) {
  if (this.form.sms || this.form.email || this.form.telegram || this.form.whatsapp) {
    callback();
  } else {
    callback(new Error('Хотя бы 1 канал'));
  }
};

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
        allow_send_from: '9:00',
        allow_send_to: '21:00',
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
      timeSelectOptions: {
        start: '00:00',
         step: '00:15',
         end: '23:45'
      },
      rules: {
        name: [
          { required: true, message: 'Поле Название - обязательное', trigger: 'blur' },
          { min: 3, message: 'Не менее 3 букв', trigger: 'blur' }
        ],
        channels: [
          { validator: checkChannels.bind(this), trigger: 'blur' }
        ],
        allow_send: [
          { validator: checkAllowSend.bind(this), trigger: 'blur' }
        ]
      }
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
    validate() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.save()
        } else {
          this.$message.error('Не все поля правильно заполнены')
          return false;
        }
      });
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
