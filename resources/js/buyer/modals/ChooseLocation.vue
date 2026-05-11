<template>
  <div class="loc-overlay" @click.self="$emit('close')">
    <div class="modal">
      <header class="modal-header">
        <h3>Choose Delivery Location</h3>
        <button class="close-btn" @click="$emit('close')">×</button>
      </header>

      <section class="modal-body">
        <div class="options">
          <button
            class="option-card"
            :class="{ active: loc_type === 'current-loc' }"
            @click="selectCurrent"
          >
            <div class="icon">📍</div>
            <div class="content">
              <label>Use My Current Location</label>
              <p class="muted">{{ store.currentUser_info.current_address || 'No address on file' }}</p>
            </div>
          </button>

          <button
            class="option-card"
            :class="{ active: loc_type === 'input-loc' }"
            @click="selectInput"
          >
            <div class="icon">✏️</div>
            <div class="content">
              <label>Enter a Different Location</label>
              <p class="muted">Provide detailed delivery instructions</p>
            </div>
          </button>
        </div>

        <transition name="fade">
          <div v-if="input_loc" class="input-area">
            <label class="field-label">Delivery Address</label>
            <textarea
              v-model="custom_address"
              placeholder="Enter the address for the delivery"
            ></textarea>
          </div>
        </transition>
      </section>

      <footer class="modal-footer">
        <button class="btn secondary" @click="$emit('close')">Cancel</button>
        <button class="btn primary" :disabled="!canConfirm" @click="confirm">
          Confirm Location
        </button>
      </footer>
    </div>
  </div>
</template>

<script>
import { useDataStore } from '../../stores/dataStore'

export default {
  emits: ['close', 'confirm'],
  data() {
    return {
      store: useDataStore(),
      loc_type: 'current-loc',
      input_loc: false,
      custom_address: ''
    }
  },
  computed: {
    canConfirm() {
      if (this.loc_type === 'current-loc') return true
      return this.custom_address.trim().length > 1
    }
  },
  methods: {
    selectCurrent() {
      this.loc_type = 'current-loc'
      this.input_loc = false
    },
    selectInput() {
      this.loc_type = 'input-loc'
      this.input_loc = true
    },
    confirm() {
      const address = this.loc_type === 'current-loc'
        ? this.store.currentUser_info.current_address 
        : this.custom_address 

      this.$emit('confirm', address)
    }
  }
}
</script>

<style scoped>
/* Overlay */
.loc-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.65);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999999;
}

/* Modal */
.modal {
  width: 520px;
  max-width: 92%;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
  overflow: hidden;
  animation: scaleIn 0.2s ease;
}

@keyframes scaleIn {
  from { transform: scale(0.96); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

/* Header */
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
}

.close-btn {
  background: transparent;
  border: none;
  font-size: 22px;
  cursor: pointer;
}

/* Body */
.modal-body {
  padding: 20px;
}

.options {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.option-card {
  display: flex;
  gap: 12px;
  padding: 14px;
  border-radius: 10px;
  border: 1px solid #ddd;
  background: #fafafa;
  cursor: pointer;
  text-align: left;
  transition: all 0.2s ease;
}

.option-card:hover {
  border-color: #999;
  background: #f5f5f5;
}

.option-card.active {
  border-color: #2f80ed;
  background: #eef4ff;
}

.icon {
  font-size: 22px;
}

.content label {
  font-weight: 600;
  display: block;
}

.muted {
  margin-top: 4px;
  font-size: 13px;
  color: #666;
}

.input-area {
  margin-top: 16px;
}

.field-label {
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 6px;
  display: block;
}

textarea {
  width: 100%;
  min-height: 90px;
  resize: vertical;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-family: inherit;
}

textarea:focus {
  outline: none;
  border-color: #2f80ed;
}

/* Footer */
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 14px 20px;
  border-top: 1px solid #eee;
}

.btn {
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 600;
}

.btn.primary {
  background: #2f80ed;
  color: #fff;
}

.btn.primary:disabled {
  background: #aac6f3;
  cursor: not-allowed;
}

.btn.secondary {
  background: #e0e0e0;
}

/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
