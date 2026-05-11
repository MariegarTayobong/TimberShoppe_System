<template>
  <div class="modal-backdrop" @click="close">
    <div class="modal-box" @click.stop>
      <button class="close-btn" @click="close">×</button>
      <p class="modal-message">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  message: {
    type: String,
    default: "Notification message"
  }
})

const emit = defineEmits(["close"])

function close() {
  emit("close")
}
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal-box {
  background: white;
  padding: 20px 30px;
  border-radius: 8px;
  min-width: 300px;
  position: relative;
  box-shadow: 0px 3px 10px rgba(0,0,0,0.2);
}

.close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}

.modal-message {
  margin-top: 15px;
  font-size: 16px;
  color: red;
}
</style>
