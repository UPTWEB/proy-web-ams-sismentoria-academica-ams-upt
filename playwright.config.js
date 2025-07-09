// playwright.config.js
import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './ui-tests',
  use: {
    headless: true,
    video: 'on',
    screenshot: 'only-on-failure',
    baseURL: 'http://localhost/web_asm/public/',
  },
});
