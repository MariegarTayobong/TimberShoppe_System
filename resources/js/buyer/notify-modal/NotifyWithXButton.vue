<template>
  <div class="notify-modal">
    <div style="display: flex; flex-direction: row; align-items: center; justify-content: end;">
      <img src="../../../images/cancel (1).png" style="width: 15px; height: 15px; cursor: pointer;" @click="$emit('close')">
    </div>
    <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 10px;">
        <div class="icon">✔</div>
        {{ message }}
    </div>
  </div>
</template>

<script>
export default {
    props: ['message'],
  name: 'FollowSuccessModal'
}
</script>

<style scoped>
.notify-modal {
  position: fixed;
  top: 100px;
  right: 20px;
  background: #4caf50;
  color: white;
  padding: 10px 20px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  font-weight: 100;
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 10000;
}

.icon {
  background: white;
  color: #4caf50;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: bold;
}

/* Slide in and fade out animations */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeOut {
  to {
    opacity: 0;
    transform: translateY(-10px);
  }
}
</style>
