const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
puppeteer.use(StealthPlugin());
const funcs = require('./functions');
const logger = require('./other/logger');

let rawdata = fs.readFileSync(path.resolve(__dirname, './config.json'));
let config = JSON.parse(rawdata);
let browser, page;
// let proxyNumber = funcs.randomInt(0, 1);
// 0 - cheaper proxy, 1 - expensive proxy
let proxyNumber = 0;
let firstname = process.argv[2];
let lastname = process.argv[3];
let city = process.argv[4];
let state = process.argv[5];

const webpageURL = 'https://www.advancedbackgroundchecks.com';

(async () => {
    try {

        browser = await puppeteer.launch({
            slowMo: 100,
            headless: true,
            devtools: true,
            args: [
                '--proxy-server=' + config.proxy[proxyNumber].host,
                '--no-sandbox',
                '--disable-setuid-sandbox',
                "--disable-gpu",
                "--disable-dev-shm-usage"
            ],
        });

        page = await browser.newPage()

        await page.setJavaScriptEnabled(true);
        await page.setDefaultNavigationTimeout(0);
        await page.setDefaultTimeout(30000);
        await page.setRequestInterception(true);

        await page.setViewport({
            width: 1920 + Math.floor(Math.random() * 100),
            height: 3000 + Math.floor(Math.random() * 100),
            deviceScaleFactor: 1,
            hasTouch: false,
            isLandscape: false,
            isMobile: false,
        });

        page.on('request', (req) => {
            if (
                req.resourceType() === 'image'
                || req.resourceType() === 'stylesheet'
                || req.resourceType() === 'font'
                || req.url().includes('amazon')
                || req.url().includes('youtube')
                || req.url().includes('google')
                || req.url().includes('adservice')
            ) {
                req.abort();
            } else {
                req.continue();
            }
        });


        await page.authenticate({
            username: config.proxy[proxyNumber].user,
            password: config.proxy[proxyNumber].pass
        });

        await page.goto(webpageURL)
        await page.waitForSelector('#form-search-name')
        await page.type('#search-name-name', firstname+' '+lastname);
        await page.keyboard.press('Enter');

        await page.waitForSelector('#peoplelist2');

        const results = await page.evaluate(() => {
            let titleNodeList = Array.from(document.querySelectorAll('#peoplelist2 > div.card'));
            let res = [];
            titleNodeList.map((td, index) => {
                if (index >= 3) { // skip sponsored
                    const nameContent = (td.querySelector('div > h4').textContent.trim())
                    const name = nameContent.slice(0, nameContent.indexOf('Age'));
                    const age = td.querySelector('div > h4 > span').textContent.trim();
                    const location = td.querySelector('div > p:nth-child(2)').textContent.trim();
                    const linkContent = (td.querySelector('div > a.link-to-details')).getAttribute('href');
                    const link = linkContent[0] === '/' ? 'https://www.advancedbackgroundchecks.com' + linkContent : linkContent;
                    res.push({name, age, location, link});
                }
            });
            return res;
        });
        console.log(JSON.stringify({message: results, error: null}));
    } catch(e){
        console.log(JSON.stringify({message: null, error: e.message}));
        logger.error(JSON.stringify(e, Object.getOwnPropertyNames(e)));
    } finally {
        process.exit(0);
    }
})();
