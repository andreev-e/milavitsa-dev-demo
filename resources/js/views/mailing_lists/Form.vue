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
            <el-form-item :label="`Текст сообщения (${messageLength} знаков)`" prop="text">
              <el-input v-model="form.text" type="textarea" :autosize="{ minRows: 2, maxRows: 8}"/>
            </el-form-item>
          </el-col>
          <el-col :span="8">
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
          <el-col :span="8">
            <el-form-item label="Диапазон времени доставки сообщений" prop="allow_send">
              <el-row>
                <el-col :span="11">
                  <el-time-select
                    v-model="form.allow_send_from"
                    placeholder="C"
                    :picker-options="timeSelectOptions"
                    style="width:100%"
                  />
                </el-col>
                <el-col :span="2" style="text-align: center">
                  -
                </el-col>
                <el-col :span="11">
                  <el-time-select
                    v-model="form.allow_send_to"
                    placeholder="По"
                    :picker-options="timeSelectOptions"
                    style="width:100%"
                  />
                </el-col>
              </el-row>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="15">
          <el-col :span="12">
            <h3>Доступные каналы</h3>
            <draggable class="list-group" :list="channels" group="channels">
              <div
                class="list-group-item"
                v-for="(element) in channels"
                :key="element"
              >
                {{ element }}
              </div>
            </draggable>
          </el-col>
          <el-col :span="12">
            <h3>Выбранные каналы по порядку</h3>
            <draggable class="list-group" :list="form.selected_channels" group="channels">
              <div
                class="list-group-item"
                v-for="(element) in form.selected_channels"
                :key="element"
              >
                {{ element }}
              </div>
            </draggable>
          </el-col>
        </el-row>
        <el-row v-if="form.selected_channels" :gutter="15">
          <el-col :span="24">
            <h2>Настройки каналов</h2>
          </el-col>
          <el-col v-if="form.selected_channels.indexOf('telegram') !== -1" :span="24" id="telegram">
            <el-card class="equal_height">
              <div slot="header">
                <h3>Telegram</h3>
              </div>
            </el-card>
          </el-col>
          <el-col v-if="form.selected_channels.indexOf('whatsapp') !== -1" :span="24" id="whatsapp">
            <el-card class="equal_height">
              <div slot="header">
                <h3>Whatsapp</h3>
              </div>
              <el-form-item label="Стоимость 1 сообщения">
                <el-input v-model="oneWhatsapp" type="text" />
              </el-form-item>
              <p>Стоимость рассылки {{ whatsappPrice }} руб.</p>
            </el-card>
          </el-col>
          <el-col v-if="form.selected_channels.indexOf('sms') !== -1" :span="24" id="sms">
            <el-card class="equal_height">
              <div slot="header">
                <h3>SMS</h3>
              </div>
              <el-form-item label="Стоимость 1 сообщения">
                <el-input v-model="oneSms" type="text" />
              </el-form-item>
              <p>Стоимость рассылки {{ smsPrice }} руб.</p>
            </el-card>
          </el-col>
          <el-col v-if="form.selected_channels.indexOf('email') !== -1" :span="24" id="email">
            <el-card class="equal_height">
              <div slot="header">
                <h3>Email</h3>
              </div>
              <el-form-item label="Шаблон письма TODO">
                <el-select v-model="form.email_teplate" clearable>
                  <el-option
                    v-for="item in mailtemplates"
                    :key="item"
                    :label="`${item}`"
                    :value="item">
                  </el-option>
                </el-select>
              </el-form-item>
            </el-card>
          </el-col>
        </el-row>
        <el-row :gutter="15">
          <el-col :span="24">
            <h2>Сегменты</h2>
          </el-col>
          <el-col :span="12">
            <el-form-item label="Выберите сегменты" prop="segments">
              <el-select v-model="form.segments" v-loading="loadingSegments" filterable multiple>
                <el-option
                  v-for="item in segments"
                  :key="item.id"
                  :label="`${item.name} - ${item.volume} чел.`"
                  :value="item.id">
                </el-option>
              </el-select>
              <p>Всего {{ totalUsers }} чел.</p>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="15">
          <el-col :span="24">
            <el-button :type="form.start === null ? 'danger' : 'success'" @click="validate">
              <span v-if="form.start === null">Запустить сразу</span>
              <span v-else>Запланировать</span>
            </el-button>
            <el-button v-if="form.name" type="default" @click="blueprint">Сохранить в черновик</el-button>
          </el-col>
        </el-row>
      </el-card>
    </el-form>
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import MailingListResource from '@/api/mailing_list.js'
import MailingSegmentResource from '@/api/mailing_segments.js'

const mailingList = new MailingListResource();
const mailingSegment = new MailingSegmentResource();

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
  components: { draggable },
  data() {
    return {
      loading: false,
      loadingSegments: false,
      segments: [],
      oneSms: 5,
      oneWhatsapp: 0.5,
      form: {
        id: null,
        name: null,
        text: null,
        sms: false,
        email: false,
        telegram: false,
        whatsapp: false,
        start: null,
        allow_send_from: '9:00',
        allow_send_to: '21:00',
        status: null,
        channel_order: [],
        segments: [],
        email_teplate: null,
        selected_channels: [],
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
        segments: [
          { type: 'array', required: true, message: 'Хотя бы 1 сегмент', trigger: 'blur' },
        ],
        channels: [
          { validator: checkChannels.bind(this), trigger: 'blur' }
        ],
        allow_send: [
          { validator: checkAllowSend.bind(this), trigger: 'blur' }
        ],
        text: [
          { required: true, message: 'Поле Текст сообщения - обязательное', trigger: 'blur' },
          { min: 3, message: 'Не менее 10 знаков', trigger: 'blur' }
        ],
      },
      channels: [
        'email',
        'telegram',
        'whatsapp',
        'sms',
      ],
      mailtemplates: [
        'Темплейт 1',
        'Темплейт 2',
        'Темплейт 3',
      ],
    };
  },
  watch: {
  },
  computed: {
    messageLength: {
      get: function () {
        return this.form.text ? this.form.text.length : 0;
      },
    },
    totalUsers: {
      get: function () {
        if (this.segments.length) {
          return this.segments.reduce((acc, item) =>
            acc + (this.form.segments.indexOf(item.id) !== -1 ? item.volume : 0), 0)
        }
        return 0;
      },
    },
    smsPrice: {
      get: function () {
        return Math.ceil(this.messageLength / 70 ) * this.oneSms * this.totalUsers;
      },
    },
    whatsappPrice: {
      get: function () {
        return Math.ceil(this.messageLength / 70 ) * this.oneWhatsapp * this.totalUsers;
      },
    },
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
    this.loadSegments();
  },
  methods: {
    async loadItem() {
      this.loading = true
      const { data } = await mailingList.get(this.form.id)
      this.form = data
      this.channels = this.channels.filter(item => this.form.selected_channels.indexOf(item) === -1)
      this.loading = false
    },
    async loadSegments() {
      this.loadingSegments = true
      const { data } = await mailingSegment.list()
      this.segments = data
      this.loadingSegments = false
    },
    validate() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          if (this.form.start === null) {
            this.form.start = new Date();
          }
          this.form.status = 'submitted';
          this.save()
        } else {
          this.$message.error('Не все поля правильно заполнены')
          return false;
        }
      });
    },
    blueprint() {
      this.form.status = 'blueprint';
      this.save(true)
    },
    async save(stay = false) {
      this.loading = true
      if (this.form.id === null) {
        const { data } = await mailingList.store(this.form)
      } else {
        const { data } = await mailingList.update(this.form.id, this.form)
      }
      this.loading = false
      if (!stay) {
        this.$router.push({name: 'mailing-lists-list'})
      }
    },
  },
}
</script>

<style scoped>
  .el-card {
    margin-bottom: 15px;
  }
  .list-group-item {
    position: relative;
    display: block;
    padding: 0.75rem 1.25rem;
    margin-bottom: -1px;
    background-color: #fff;
    border: 1px solid rgba(0,0,0,.125);
    cursor: move;
  }
  .list-group-item:first-child {
      border-top-left-radius: 0.25rem;
      border-top-right-radius: 0.25rem;
  }
  .list-group {
    display: -ms-flexbox;
    display: -webkit-box;
    display: flex;
    -ms-flex-direction: column;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    flex-direction: column;
    padding: 10px;
    margin-bottom: 0;
    min-height:20px;
    background-color: #F0F0F0;
  }
</style>
