<template>
  <span v-if="statusId === 1" class="rotated">Не подходит</span>
  <span v-else-if="statusId === 2" class="rotated">Рассмотрение</span>
  <span v-else-if="statusId === 3" class="rotated">Отказан</span>
  <span v-else-if="statusId === 4" class="rotated">Одобрен</span>
  <span v-else-if="statusId === 5" class="rotated">Лимит</span>
  <span v-else-if="statusId === 6" class="rotated">Не работает</span>
  <span v-else-if="statusId === 7" class="rotated">Отложено</span>
</template>
<script>
export default {
  props: {
    statusId: Number
  }
}
</script>
