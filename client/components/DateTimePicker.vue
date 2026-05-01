<template>
  <v-menu
    v-model="menu"
    :close-on-content-click="false"
    :nudge-right="40"
    transition="scale-transition"
    offset-y
    max-width="290px"
    min-width="290px"
  >
    <template v-slot:activator="{ on }">
      <v-text-field
        v-model="dateFormatted"
        label="Date time picker"
        append-icon="mdi-calendar"
        readonly
        v-on="on"
      ></v-text-field>
    </template>
    <v-date-picker
      v-model="date"
      no-title
      scrollable
    ></v-date-picker>
    <v-time-picker
      v-model="time"
      format="24hr"
      scrollable
    ></v-time-picker>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="primary" text @click="menu = false">Cancel</v-btn>
      <v-btn color="primary" text @click="save">OK</v-btn>
    </v-card-actions>
  </v-menu>
</template>

<script>
export default {
  name: 'DateTimePicker',
  data: () => ({
    menu: false,
    date: null,
    time: null
  }),
  computed: {
    dateFormatted () {
      console.log(this.date)
      if (!this.date) return ''
      const options = { year: 'numeric', month: 'short', day: 'numeric' }
      return this.date.toLocaleDateString('ru-RU', options) + ', ' + this.time
    }
  },
  methods: {
    save () {
      const dateTime = new Date(this.date)
      if (time) {
        const time = this.time.split(':')
        dateTime.setHours(time[0], time[1], 0)
      } else {
        dateTime.setHours(0)
      }

      this.$emit('datetime-selected', dateTime)
      this.menu = false
    }
  }
}
</script>
