<?php
// The output of this file will be a js file, for the service worker.
// you can use php variables or embed js code using php code. Awesome isnt it?
?>
// Service Worker for PWA, more custom core to cache and show offline etc.
console.log('ROOT sw, Custom JS for PWA Loaded from another js. ');

'use strict';

/**
 * Service Worker of Balconi PWA
 * To learn more and add one to your website, visit - https://superpwa.com
 */

const cacheName = '<?php echo $_SERVER['HTTP_HOST']; ?>-balconi-pwa-1.0';
const homepage = '<?php echo home_url(); ?>';


const startPage = homepage + '/';
const offlinePage = homepage + '/offline';
const filesToCache = [
     startPage,
     offlinePage
];
const filesToCacheAdditional = [

];
const neverCacheUrls = [/\/wp-content\/hi-res/, /\/wp-admin/, /\/wp-includes/, /\/wp-login/, /preview=true/];


// Install. TODO: load the filestocache, and use a way to load the Additional in backgroun. I hace use the cache.All but I think it broke the code.
self.addEventListener('install', function (e) {
         console.log('Balconi PWA service worker installation');
         e.waitUntil(
             caches.open(cacheName).then(function (cache) {
                console.log('Balconi PWA service worker caching dependencies');
                filesToCache.map(function (url) {
                  return cache.add(url).catch(function (reason) {
                       return console.log('Balconi PWA: ' + String(reason) + ' ' + url);
                  });
                });
             })
         );
    });

// Activate
self.addEventListener('activate', function (e) {
    console.log('Service worker activation');
    e.waitUntil(
        caches.keys().then(function (keyList) {
            return Promise.all(keyList.map(function (key) {
                if (key !== cacheName) {
                    console.log(' SW - old cache removed', key);
                    return caches.delete(key);
                }
            }));
        })
    );
    return self.clients.claim();
});

// Fetch any resource: 
self.addEventListener('fetch', function (e) {

    // Return if the current request url is in the never cache list
    if (!neverCacheUrls.every(checkNeverCacheList, e.request.url)) {
        console.log('Balconi PWA: Current request is excluded from cache.');
        return;
    }

    // Return if request url protocal isn't http or https
    if (!e.request.url.match(/^(http|https):\/\//i))
        return;

    // Return if request url is from an external domain.
    if (new URL(e.request.url).origin !== location.origin)
        return;

    // For POST requests, do not use the cache. Serve offline page if offline.
    if (e.request.method !== 'GET') {
        e.respondWith(
            fetch(e.request).catch(function () {
                return caches.match(offlinePage);
            })
        );
        return;
    }

    // Revving strategy
    if (e.request.mode === 'navigate' && navigator.onLine) {
        e.respondWith(
            fetch(e.request).then(function (response) {
                return caches.open(cacheName).then(function (cache) {
                    cache.put(e.request, response.clone());
                    return response;
                });
            })
        );
        return;
    }

    e.respondWith(
        caches.match(e.request).then(function (response) {
            return response || fetch(e.request).then(function (response) {
                return caches.open(cacheName).then(function (cache) {
                    cache.put(e.request, response.clone());
                    return response;
                });
            });
        }).catch(function () {
            return caches.match(offlinePage);
        })
    );
});

// Check if current url is in the neverCacheUrls list
function checkNeverCacheList(url) {
    if (this.match(url)) {
        return false;
    }
    return true;
}