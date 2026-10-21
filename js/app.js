document.addEventListener('DOMContentLoaded', () => {
  const viewer = document.querySelector('#product-viewer');
  const status = document.querySelector('#viewer-status');
  if (!viewer || !status) return;
  viewer.addEventListener('error', () => {
    status.textContent = '3D model failed to load. Please check the model file.';
    status.classList.add('is-error');
  });
  viewer.addEventListener('ar-status', (event) => {
    if (event.detail.status === 'failed') {
      status.textContent = 'AR is unavailable on this device or browser. You can still inspect the 3D model here.';
      status.classList.add('is-error');
    }
  });
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden && viewer.isConnected && viewer.loaded === false) viewer.dismissPoster();
  });
  window.addEventListener('pageshow', () => {
    if (viewer.isConnected) viewer.requestUpdate?.();
  });
});
