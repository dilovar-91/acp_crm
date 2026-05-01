<template>
  <date-picker v-bind="$attrs" v-on="$listeners">
    <template #input="{ props, events }">
      <masked-input
        type="text"
        name="callback"
        placeholder="__.__.____"
        class="mx-input"
        :mask="dateTimeMask"
        v-bind="props"
        v-on="events"
      >
      </masked-input>
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
      dateTimeMask: [/\d/, /\d/, ".", /\d/, /\d/, ".", /\d/, /\d/, /\d/, /\d/],
      test: null,
    }
  },
  methods: {
    setNow() {
      this.$emit('setNow');
    }
  }
};
</script>
