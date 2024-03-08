<template>
    <div class="loader-container">
      <div class="loader"></div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'RotatingLoader',
  }
  </script>
  
  <style scoped>
  .loader-container {
    display: flex;
  }
  .loader {
    margin: auto;
    border: 16px solid #fff;
    border-top: 16px solid var(--color-primary-bla-400);
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s cubic-bezier(0.63, 0.19, 0.48, 0.56) infinite;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  </style>