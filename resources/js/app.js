import * as Turbo from '@hotwired/turbo';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

// Configure NProgress (Nuxt-style top loading bar)
NProgress.configure({
    showSpinner: false,
    trickleSpeed: 100,
    minimum: 0.15,
});

// Setup Turbo SPA Navigation & Loading Bar
document.addEventListener('turbo:click', () => {
    NProgress.start();
});

document.addEventListener('turbo:before-visit', () => {
    NProgress.start();
});

document.addEventListener('turbo:submit-start', () => {
    NProgress.start();
});

document.addEventListener('turbo:render', () => {
    NProgress.done();
    createIcons({ icons });
});

document.addEventListener('turbo:load', () => {
    NProgress.done();
    createIcons({ icons });
});

// Alpine JS initialization
window.Alpine = Alpine;
Alpine.start();

// Initial icons render
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});
