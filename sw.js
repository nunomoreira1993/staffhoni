const version = "0.1.00";
const cacheName = `staffhoni-${version}`;
self.addEventListener('install', e => {
	const timeStamp = Date.now();
	e.waitUntil(
		caches.open(cacheName).then(cache => {
			return cache.addAll([
					'/',
					'/temas/administrador/js/script.js'
				])
				.then(() => self.skipWaiting());
		})
	);
});

self.addEventListener('activate', event => {
	event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
	event.respondWith(
		caches.open(cacheName)
		.then(cache => cache.match(event.request, {
			ignoreSearch: true
		}))
		.then(response => {
			return response || fetch(event.request);
		})
	);
});

// let deferredPrompt;
// window.addEventListener('load', function(){	
	// const addBtn = document.querySelector('.add-button');
	// addBtn.style.display = 'none';
// }, false);