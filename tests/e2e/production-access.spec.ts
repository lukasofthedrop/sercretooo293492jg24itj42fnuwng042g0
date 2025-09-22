import { test, expect } from '@playwright/test';

test.describe('Production Access Tests', () => {
  test('Admin Dashboard Access', async ({ page }) => {
    const responses: { url: string; status: number }[] = [];

    page.on('response', response => {
      responses.push({ url: response.url(), status: response.status() });
    });

    await page.goto('https://lucrativabet-web-production.up.railway.app/admin');

    // Check if it's a login page
    const loginForm = page.locator('form').filter({ hasText: /email|login/i });
    if (await loginForm.count() > 0) {
      // Fill login form
      await page.fill('input[name="email"]', 'admin@lucrativa.bet');
      await page.fill('input[name="password"]', 'foco123@');
      await page.click('button[type="submit"]');

      // Wait for navigation
      await page.waitForLoadState('networkidle');

      // Check if redirected to admin dashboard
      const currentUrl = page.url();
      const isAdminDashboard = currentUrl.includes('/admin') && !currentUrl.includes('/login');

      console.log('Admin Login Test Results:');
      console.log('Current URL:', currentUrl);
      console.log('Is Admin Dashboard:', isAdminDashboard);
      console.log('HTTP Responses:', responses.filter(r => r.url.includes('admin') || r.url.includes('login')));

      expect(isAdminDashboard).toBe(true);
    } else {
      // Already on dashboard
      console.log('Admin Dashboard loaded directly');
      expect(page.url()).toContain('/admin');
    }
  });

  test('Affiliate Dashboard Access', async ({ page }) => {
    const responses: { url: string; status: number }[] = [];

    page.on('response', response => {
      responses.push({ url: response.url(), status: response.status() });
    });

    await page.goto('https://lucrativabet-web-production.up.railway.app/afiliado');

    // Check if it's a login page
    const loginForm = page.locator('form').filter({ hasText: /email|login/i });
    if (await loginForm.count() > 0) {
      // Fill login form
      await page.fill('input[name="email"]', 'afiliado@lucrativa.bet');
      await page.fill('input[name="password"]', 'afiliado123');
      await page.click('button[type="submit"]');

      // Wait for navigation
      await page.waitForLoadState('networkidle');

      // Check if redirected to affiliate dashboard
      const currentUrl = page.url();
      const isAffiliateDashboard = currentUrl.includes('/afiliado') && !currentUrl.includes('/login');

      console.log('Affiliate Login Test Results:');
      console.log('Current URL:', currentUrl);
      console.log('Is Affiliate Dashboard:', isAffiliateDashboard);
      console.log('HTTP Responses:', responses.filter(r => r.url.includes('afiliado') || r.url.includes('login')));

      expect(isAffiliateDashboard).toBe(true);
    } else {
      // Already on dashboard
      console.log('Affiliate Dashboard loaded directly');
      expect(page.url()).toContain('/afiliado');
    }
  });
});