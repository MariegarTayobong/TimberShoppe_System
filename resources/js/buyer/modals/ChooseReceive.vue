<template>
  <div class="overlay" @click.self="$emit('close')">

    <teleport to="body">
      <ChooseLocation v-if="this.show_choose_location" @close="show_choose_location = false;" @confirm="confirm"/>
    </teleport>

    <div class="modal-box">
      <h2>How would you like to get your item?</h2>

      <div class="options">
        <div class="option-card" @click="select('pickup')"> 
          <h3>Pick Up</h3>
          <p>You will personally pick up the item at the seller’s shop.</p>
        </div>

        <div class="option-card" @click="select('delivery')">
          <h3>Delivery</h3>
          <p>The seller will deliver the item to your address.</p>
        </div>
      </div>

      <button class="close-btn" @click="$emit('close')">Cancel</button>
    </div>
  </div>
</template>

<script>
  import ChooseLocation from './ChooseLocation.vue';
  export default {
    emits: ['close', 'select_method'],
    components: {ChooseLocation},
    data() {
      return{
        data: {
          location: '',
          method: '',
        },
        show_choose_location: false,
      }
    },
    methods: {

      confirm(address) {

        this.data.location = address;

        this.$emit('select_method', this.data);
        this.show_choose_location = false;
      },
      select(method) {

        this.data.method = method;

        if(method === 'delivery') {

          this.show_choose_location = true;
          return;
        }

        this.$emit('select_method', this.data);
      }
    }
  }
</script>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999999;
}

.modal-box {
  background: #fff;
  width: 90%;
  max-width: 400px;
  padding: 25px;
  border-radius: 10px;
  animation: fadeIn 0.2s ease-in;
}

h2 {
  text-align: center;
  margin-bottom: 20px;
}

.options {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.option-card {
  border: 1px solid #ddd;
  padding: 15px;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.2s;
}

.option-card:hover {
  background: #f7f7f7;
  border-color: #999;
}

.close-btn {
  width: 100%;
  margin-top: 20px;
  padding: 10px;
  border: none;
  background: #aaa;
  color: white;
  border-radius: 6px;
  cursor: pointer;
}

.close-btn:hover {
  background: #888;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
