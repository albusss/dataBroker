const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
const logger = require('./other/logger');
const fs = require("fs");
const path = require("path");

let rawdata = fs.readFileSync(path.resolve(__dirname, './config.json'));
let config = JSON.parse(rawdata);
let browser, page;
let proxyNumber = Math.floor(Math.random() * config.proxy.length);
let firstname = process.argv[2];
let lastname = process.argv[3];
let city = process.argv[4];
let state = process.argv[5];

puppeteer.use(StealthPlugin());

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
        await page.setDefaultTimeout(60000 * 10);
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

        await page.goto(`https://www.floridaresidentsdirectory.com/name/${firstname}-${lastname}`);

        await page.waitForSelector('#search-results')

        let result = await page.evaluate(() => {
            let titleNodeList = document.querySelectorAll('.row.bb.element');
            let res = [];
            for (let i = 0; i < titleNodeList.length; i++) {
                if (i > 10) {
                    break;
                }
                let name = titleNodeList[i].querySelector('div a h3').innerText;
                res[i] = {
                    name: name,
                    age: titleNodeList[i].querySelector('div div div p:nth-child(1)').innerText.replace(/\D/g, ""),
                    address: titleNodeList[i].querySelector('div div div p:nth-child(2) span').innerText,
                    link: titleNodeList[i].querySelector('div a').getAttribute('href'),
                };
            }
            return res;
        });

        console.log(JSON.stringify({message: result, error: null}));
    } catch(e){
        console.log(JSON.stringify({message: null, error: e.message}));
        logger.error(JSON.stringify(e, Object.getOwnPropertyNames(e)));
    } finally {
        process.exit(0);
    }
})();
