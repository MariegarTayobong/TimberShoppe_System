<template>
  <div class="verify-overlay">
    
    <div class="big-box">
        <div style="width: 100%; display: flex; flex-direction: row; justify-content: end;">
            <img src="../../../images/cancel.png" style="width: 20px; height: 20px; margin: 10px; cursor: pointer;" @click="$emit('close')">
        </div>
        <div style="width: 100%; height: auto;">
            <img :src="'/' + path" style="width: 100%; height: 100vh;">
        </div>
    </div>
  </div>
</template>

<script>
export default {
    props: ['path']
}
</script>

<style scoped>
    .verify-overlay {
        width: 100%;
        height: 100vh;
        position: fixed;
        background-color: rgba(0, 0, 0, 0.658);
        top: 0;
        left: 0;
        display: flex; flex-direction: row; align-items: center; justify-content: center;
    }
    .big-box {
        width: 90%;
        height: 90vh;
        background-color: white;
        overflow-y: scroll;
        overflow-x: hidden;
    }
</style>