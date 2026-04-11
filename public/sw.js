self.addEventListener('install', (event) => {
    console.log('Service Worker installé !');
});

self.addEventListener('fetch', (event) => {
    // considère le site comme une application installable.
});
