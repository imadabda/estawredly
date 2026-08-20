const CACHE_NAME = 'estawredly-cache-v25-clean';
const urlsToCache = [
  './',
  './index.html',
  './shop.html',
  './product.html',
  './checkout.html',
  './style.css',
  './main.js',
  './store.js',
];

self.addEventListener('install', event => {
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  // Bypass cache for API requests, POST requests, and the dynamic products db
  if (event.request.method !== 'GET' || 
      event.request.url.includes('/api/') || 
      event.request.url.includes('products_db.js') ||
      event.request.url.includes('admin.php')) {
    return;
  }
  
  // Network first: always fetch newest version from network, fallback to cache if offline
  event.respondWith(
    fetch(event.request)
      .then(response => {
        if (response && response.status === 200) {
          const resClone = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(event.request, resClone));
        }
        return response;
      })
      .catch(() => {
        return caches.match(event.request);
      })
  );
});
