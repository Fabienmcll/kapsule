self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
    // On ne gère que les requêtes GET pour éviter d'interférer avec les uploads (POST)
    if (event.request.method !== 'GET') {
        return;
    }
});
