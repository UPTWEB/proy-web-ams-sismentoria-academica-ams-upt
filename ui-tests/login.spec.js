// ui-tests/login.spec.js
const { test, expect } = require('@playwright/test');

test('Login carga correctamente', async ({ page }) => {
  await page.goto('http://localhost/web_asm/public/index.php?accion=login');

  // Verifica que el formulario esté visible
  await expect(page.locator('form')).toBeVisible();
});
