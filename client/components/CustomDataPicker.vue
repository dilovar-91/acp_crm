<template>
  <date-picker v-bind="$attrs" v-on="$listeners" :disabled-date="disabledBeforeNow" >
    <template #input="{ props, events }">

      <masked-input
        type="text"
        name="callback"
        :placeholder="datetype()"
        class="mx-input"
        :mask="dateTimeMask"
        v-bind="props"
        v-on="events"
      >
      </masked-input>

    </template>
    <template #footer="{ emit }">
      <div style="text-align: left">
        <button
          class="mx-btn mx-btn-text"
          @click="setNow('last_call', true, false)">
          Сейчас
        </button>
        <button
          class="mx-btn mx-btn-text"
          @click="setAfter('last_call', true, true)">
          Завтра
        </button>
      </div>
    </template>
  </date-picker>
</template>

<script>
import DatePicker from "vue2-datepicker";

export default {
  name: "FormDatePicker",
  props:{
    limit: String,
    type: String,
  },
  components: {
    DatePicker,
  },
  data() {
    return {
      test: null,
      value: null,
      rules: [
        v => (v <= 59 || v >= 0) || 'В пределах 0-59',
      ],
    }
  },

  methods: {
    setNow() {
      this.$emit('setNow');
    },
    setAfter() {
      this.$emit('setAfter');
    },
    datetype() {
      return (this.type === 'datetime') ? '__.__.____ __:__' : '__.__.____';
    },
    dateTimeMask() {
      return (this.type === 'datetime') ? [/\d/, /\d/, ".", /\d/, /\d/, ".", /\d/, /\d/, /\d/, /\d/, " ", /\d/, /\d/, ":", /\d/, /\d/] : [/\d/, /\d/, ".", /\d/, /\d/, ".", /\d/, /\d/, /\d/, /\d/];
    },
    disabledBeforeNow(date) {
      const now = new Date();
      let yesterday = new Date(now.getFullYear(), now.getMonth(), now.getDate()); // yesterday's datetime
      if (this.limit === 'before') {
        return date < yesterday;
      } else if (this.limit === 'after') {
        return date > yesterday;
      } else return

    },
  }
};
</script>
