const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const outDir = path.join(__dirname, 'public', 'preview_screenshots');
if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
}

const delay = ms => new Promise(res => setTimeout(res, ms));

(async () => {
    console.log("Starting browser...");
    const browser = await puppeteer.launch({
        headless: "new",
        defaultViewport: { width: 1440, height: 900 }
    });
    const page = await browser.newPage();
    
    console.log("Taking screenshot of Login page...");
    await page.goto('http://localhost:8000/login');
    await delay(1000);
    await page.screenshot({ path: path.join(outDir, '01_Login.png'), fullPage: true });
    
    console.log("Logging in...");
    await page.type('input[name="username"]', 'admin');
    await page.type('input[name="password"]', 'Admin1234');
    await Promise.all([
        page.click('button[type="submit"]'),
        page.waitForNavigation({ waitUntil: 'networkidle0' })
    ]);
    
    console.log("Taking screenshot of Dashboard...");
    await delay(1500); // Wait for charts to render
    await page.screenshot({ path: path.join(outDir, '02_Dashboard.png'), fullPage: true });
    
    console.log("Taking screenshot of Dokumen List...");
    await page.goto('http://localhost:8000/dokumen');
    await delay(1000);
    await page.screenshot({ path: path.join(outDir, '03_Dokumen_List.png'), fullPage: true });
    
    console.log("Taking screenshot of Upload Form...");
    await page.goto('http://localhost:8000/dokumen/create');
    await delay(1000);
    await page.screenshot({ path: path.join(outDir, '04_Upload_Dokumen.png'), fullPage: true });
    
    console.log("Taking screenshot of Laporan...");
    await page.goto('http://localhost:8000/laporan');
    await delay(1000);
    await page.screenshot({ path: path.join(outDir, '05_Laporan.png'), fullPage: true });
    
    console.log("Taking screenshot of Log Aktivitas...");
    await page.goto('http://localhost:8000/aktivitas');
    await delay(1000);
    await page.screenshot({ path: path.join(outDir, '06_Log_Aktivitas.png'), fullPage: true });

    console.log("Taking screenshot of Recycle Bin...");
    await page.goto('http://localhost:8000/recycle-bin');
    await delay(1000);
    await page.screenshot({ path: path.join(outDir, '07_Recycle_Bin.png'), fullPage: true });
    
    console.log("Taking screenshot of Manajemen User...");
    await page.goto('http://localhost:8000/admin/users');
    await delay(1000);
    await page.screenshot({ path: path.join(outDir, '08_Manajemen_User.png'), fullPage: true });
    
    console.log("Taking screenshot of Kategori...");
    await page.goto('http://localhost:8000/admin/categories');
    await delay(1000);
    await page.screenshot({ path: path.join(outDir, '09_Manajemen_Kategori.png'), fullPage: true });
    
    console.log("Taking screenshot of Profil...");
    await page.goto('http://localhost:8000/profil');
    await delay(1000);
    await page.screenshot({ path: path.join(outDir, '10_Profil.png'), fullPage: true });
    
    await browser.close();
    console.log("All screenshots captured successfully in public/preview_screenshots");
})();
