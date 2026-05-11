<template>
  <div class="verify-overlay" @click.self="$emit('close')">

    <teleport to="body">
        <div class="loading-overlay" v-if="is_loading">
            <img src="../../../images/kOnzy.gif" style="width: 100px; height: 100px;">
        </div>
    </teleport>
    <div class="action-container">

      <h3 class="title">Verify Seller Account</h3>

      <p class="desc">
        Are you sure you want to <strong>verify</strong> this account?
      </p>

      <div class="seller-info">
        <strong>{{ account.seller.firstname }} {{ account.seller.lastname }}</strong>
        <span>{{ account.seller.email }}</span>
      </div>

      <label class="optional-label">Optional Message</label>

      <textarea 
        v-model="message"
        class="message-box"
        placeholder="Enter a message for the seller (optional)..."
      ></textarea>

      <div class="btn-row">
        <button class="reject-btn" @click="goVerify('reject')">Reject</button>
        <button class="approve-btn" @click="goVerify('verified')">Verify</button>
      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: ['account'],
  data() {
    return {
      message: '',
      is_loading: false,
    };
  },
  methods: {

    async goVerify(action){

        try{

            this.is_loading = true;

            const data = new FormData();
            data.append('id', this.account.id);
            data.append('message', this.message);
            data.append('email', this.account.seller.email);
            data.append('action', action);

            const res = await axios.post('/admin/verify-account', data);

            console.log(res.data.message);

            if(res.data.message === 'successful-verify'){

                this.$emit('success', 'verify');
            }
            else{

              this.$emit('success', 'rejected');
            }
        }
        catch(e) {

            console.log(e);
        }
        finally {

            this.is_loading = false;
        }
    }
  }
}
</script>
<style scoped>

  .loading-overlay{

    position: fixed;
    width: 100%;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.637);
    left: 0;
    top: 0;
    display: flex;
    align-items: center; justify-content: center;
    z-index: 99999;
  }

    .verify-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.65);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

/* Modal Content */
.action-container {
  background: white;
  width: 480px;
  padding: 25px 30px;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.2);
  display: flex;
  flex-direction: column;
  gap: 18px;
  animation: fadeIn 0.2s ease-out;
}

/* Title */
.title {
  font-size: 20px;
  font-weight: 700;
  margin: 0;
  color: #2f3542;
  text-align: center;
}

/* Description */
.desc {
  text-align: center;
  color: #555;
  margin-bottom: 10px;
}

/* Seller Info Card */
.seller-info {
  background: #f5f6fa;
  padding: 12px 15px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  display: flex;
  flex-direction: column;
  gap: 2px;
  text-align: center;
  font-size: 15px;
}

/* Optional label */
.optional-label {
  font-size: 14px;
  color: #666;
}

/* Textarea */
.message-box {
  width: 94%;
  height: 120px;
  resize: none;
  border-radius: 8px;
  border: 1px solid #ccc;
  padding: 12px;
  font-size: 14px;
  color: #333;
  outline: none;
  transition: 0.2s;
}

.message-box:focus {
  border-color: #4a90ff;
  box-shadow: 0 0 3px rgba(74,144,255,0.4);
}

/* Button Row */
.btn-row {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

/* Reject Button */
.reject-btn {
  width: 48%;
  padding: 10px;
  background-color: #e74c3c;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 15px;
  transition: 0.2s;
}

.reject-btn:hover {
  background-color: #c0392b;
}

/* Approve Button */
.approve-btn {
  width: 48%;
  padding: 10px;
  background-color: #2ecc71;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 15px;
  transition: 0.2s;
}

.approve-btn:hover {
  background-color: #27ae60;
}

/* Fade animation */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

</style>