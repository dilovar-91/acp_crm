<template>
  <todo-list :tasks="labelTasks" @edit-task="$emit('edit-task', $event)" />
</template>

<script>
import { mapState } from 'vuex'
import TodoList from '../components/TodoList'

export default {
  components: {
    TodoList
  },
  computed: {
    ...mapState('todo-app', ['tasks']),
    labelTasks() {
      return this.tasks
        .filter((t) => t.labels.includes(this.$route.params.id))
        .sort((a, b) => a === false ? 1 : -1)
    }
  }
}
</script>
