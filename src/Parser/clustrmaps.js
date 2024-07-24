const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
puppeteer.use(StealthPlugin());
const logger = require('./other/logger');

let rawdata = fs.readFileSync(path.resolve(__dirname, './config.json'));
let config = JSON.parse(rawdata);
let browser, page;
let proxyNumber = Math.floor(Math.random() * config.proxy.length);
let firstname = process.argv[2];
let lastname = process.argv[3];
let city = process.argv[4];
let state = process.argv[5];

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
        await page.setDefaultTimeout(15000);
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

        await page.goto(`https://clustrmaps.com/persons/${firstname}-${lastname}`);

        // require select an suggestion item and click
        // await page.goto('https://clustrmaps.com');
        //
        // await page.waitForSelector('div.deep-search-div');
        // await page.type('div.deep-search-div input[type=text]', firstname + ' ' + lastname);
        // await page.keyboard.press('Enter');

        await page.waitForSelector('div[itemprop=Person]');

        let results = await page.evaluate(() => {
            let titleNodeList = Array.from(document.querySelectorAll('div[itemprop=Person]'));

            let res = [];
            titleNodeList.map(node => {
                let name = node.querySelector('span[itemprop=name]').textContent.trim();
                let age = ' ';
                let agenode = node.querySelector('span.age');
                if(agenode) {
                    age = agenode.textContent.replace(', age', '').trim();
                }
                let link = node.querySelector('a.persons').href;
                let location = node.querySelector('div[itemprop=address] > a').textContent.trim();

                res.push({name, age, link, location});
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
