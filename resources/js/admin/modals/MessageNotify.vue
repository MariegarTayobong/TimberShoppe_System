<template>
  <transition name="fade">
    <div v-if="isVisible" class="message-modal">
      <div class="modal-header">
        <span class="modal-title">{{ title }}</span>
        <span class="material-icons close-icon" @click="hide">close</span>
      </div>

      <div class="modal-body">
        <p>{{ message }}</p>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: "MessageModal",
  props: {
    title: {
      type: String,
      default: "Notification",
    },
    message: {
      type: String,
      required: true,
    },
  },
  watch: {

    message(value){

        if(this.message !== ''){

            this.isVisible = true;
        }
        else{
            this.isVisible = false;
        }
    }
  },
  data() {
    return {
      isVisible: false, // starts visible
    };
  },
  methods: {
    hide() {
      this.isVisible = false;
      this.$emit("closed"); // optional event if parent wants to react
    },
  },
};
</script>

<style scoped>
.message-modal {
  position: fixed;
  top: 25px;
  right: 25px;
  width: 280px;
  background-color: #ffffff;
  border: 1px solid #ddd;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  padding: 15px 18px;
  z-index: 9999;
  animation: slideIn 0.3s ease;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.modal-title {
  font-weight: 600;
  color: #11480b;
  font-size: 15px;
}

.close-icon {
  cursor: pointer;
  font-size: 20px;
  color: #777;
  transition: 0.2s;
}

.close-icon:hover {
  color: #000;
}

.modal-body p {
  margin: 0;
  font-size: 14px;
  color: #333;
}

/* Smooth fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease, transform 0.4s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Slide in animation */
@keyframes slideIn {
  from {
    transform: translateY(-15px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>
