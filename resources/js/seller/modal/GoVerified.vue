<template>
  <div class="overlay">

    <NotifyWithXButton :message="message" v-if="message != ''" @close="message = ''"/>

    <teleport to="body">
      <div class="loading-overlay" v-if="is_loading">
        
        <img src="../../../images/kOnzy.gif" style="width: 100px; height: 100px;">
      </div>
    </teleport>
    <div class="verified-container">

      <div class="header">
        <h2>Seller Verification</h2>
        <button class="close-btn" @click="$emit('close')">✕</button>
      </div>

      <p class="sub">Please upload all required documents to verify your account.</p>

      <form @submit.prevent="submitVerification">

        <!-- VALID ID -->
        <label class="field-label">Valid ID</label>
        <select v-model="form.validID" class="select-input" required>
          <option disabled value="">Select ID</option>
          <option>Driver’s License</option>
          <option>Passport</option>
          <option>National ID</option>
          <option>SSS / GSIS</option>
          <option>UMID</option>
        </select>

        <!-- SELFIE + ID -->
        <label class="field-label">Selfie with ID</label>
        <input type="file" @change="handleFile($event, 'selfie')" accept="image/*" required />

        <!-- PROOF OF ADDRESS -->
        <!-- <label class="field-label">BIR</label>
        <input type="file" @change="handleFile($event, 'proofAddress')" accept="image/*" required /> -->

        <!-- DTI -->
        <label class="field-label">DTI Registration</label>
        <input type="file" @change="handleFile($event, 'dti')" accept="image/*" required />

        <!-- BUSINESS PERMIT -->
        <label class="field-label">BIR Certificate of Registration (COR)</label>
        <input type="file" @change="handleFile($event, 'businessPermit')" accept="image/*" required />

        <button type="submit" class="submit-btn">
          Submit Verification
        </button>
      </form>

    </div>
  </div>
</template>

<script>
import axios from 'axios';
import NotifyWithXButton from '../../buyer/notify-modal/NotifyWithXButton.vue';

export default {
  components: {NotifyWithXButton},
  props: ["user"],
  data() {
    return {
      form: {
        validID: "",
        selfie: null,
        dti: null,
        businessPermit: null,
      },
      is_loading: false,
      message: '',
    };
  },
  methods: {
    handleFile(event, field) {
      this.form[field] = event.target.files[0];
    },

    async submitVerification() {

      try{

        this.is_loading = true;

        const data = new FormData();

        for(let key of Object.keys(this.form)){

          console.log('key: ', key);
          console.log('file: ', this.form[key]);
          data.append(key, this.form[key]);
        }

        data.append('id', this.user.id);

        const res = await axios.post('/seller/go-verified', data);

        console.log(res.data.message);
        if(res.data.message === 'successful') {

          this.user.shop.is_verified = 'pending';
          this.$emit('success');
          this.message = 'SUCCESSFUL. PLEASE WAIT FOR VERIFICATION THANK YOU!';
        }
      }
      catch(e) {

        console.log(e);
      }
      finally {

        this.is_loading = false;
      }
    },
  },
};
</script>

<style scoped>
.loading-overlay{
  position: fixed;
  background-color: rgba(0, 0, 0, 0.616);
  width: 100%;
  height: 100vh;
  top: 0;
  left: 0;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  z-index: 9999999;
}
/* Overlay */
.overlay {
  background-color: rgba(0, 0, 0, 0.6);
  width: 100%;
  height: 100vh;
  z-index: 999999;
  position: fixed;
  top: 0;
  left: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Modal container */
.verified-container {
  width: 500px;
  max-width: 90%;
  background: white;
  border-radius: 10px;
  padding: 25px;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.25);
  animation: fadeIn 0.25s ease-in-out;
}

/* Header */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.close-btn {
  background: transparent;
  border: none;
  font-size: 20px;
  cursor: pointer;
}

.sub {
  margin-top: 5px;
  color: #555;
  font-size: 14px;
}

/* Inputs */
.field-label {
  margin-top: 15px;
  display: block;
  font-weight: bold;
  font-size: 14px;
}

.select-input,
input[type="file"] {
  width: 100%;
  margin-top: 5px;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

/* Button */
.submit-btn {
  margin-top: 20px;
  width: 100%;
  padding: 12px;
  background: #007bff;
  border: none;
  border-radius: 6px;
  color: white;
  font-size: 16px;
  cursor: pointer;
}

.submit-btn:hover {
  background: #0067d3;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
