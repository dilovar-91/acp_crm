<template>
  <date-picker v-bind="$attrs" v-on="$listeners">
    <template #input="{ props, events }">

      <masked-input
        type="text"
        name="callback"
        placeholder="__.__.____ __:__"
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
          Через 1,5 часа
        </button>
      </div>
    </template>
  </date-picker>
</template>

<script>
import DatePicker from "vue2-datepicker";

export default {
  name: "FormDatePicker",
  components: {
    DatePicker,
  },
  data() {
    return {
      dateTimeMask: [/\d/, /\d/, ".", /\d/, /\d/, ".", /\d/, /\d/, /\d/, /\d/, " ", /\d/, /\d/, ":", /\d/, /\d/],
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
    }
  }
};
</script>
