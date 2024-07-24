<?php

namespace App\Enum;

class Parsers
{
    public const TEST_PARSER = [
        '1' => [
            'name' => 'FloridaResidentsDirectory1.com',
            'path' => 'src/Parser/floridatest.js',
            'path_to_deleter' => '',
        ],
        '2' => [
            'name' => 'FloridaResidentsDirectory2.com',
            'path' => 'src/Parser/floridatest2.js',
            'path_to_deleter' => '',
        ],
        '3' => [
            'name' => 'FloridaResidentsDirectory3.com',
            'path' => 'src/Parser/floridatest3.js',
            'path_to_deleter' => '',
        ],
    ];

    public const PARSERS = [
        [// cloudflare, but works
            'name' => '411.com',
            'path' => 'src/Parser/411.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'abcheck.com',
            'path' => 'src/Parser/abcheck.js',
            'path_to_deleter' => '',
        ],
//        [// cloudflare
//            'name' => 'addresses.com',
//            'path' => 'src/Parser/addresses.js',
//            'path_to_deleter' => '',
//        ],
        [
            'name' => 'americaphonebook.com',
            'path' => 'src/Parser/americaphonebook.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'anywho.com',
            'path' => 'src/Parser/anywho.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'backgroundalert.com',
            'path' => 'src/Parser/backgroundalert.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'backgroundcheck-run.com',
            'path' => 'src/Parser/backgroundcheck-run.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'beenverified.com',
            'path' => 'src/Parser/beenverified.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'clustrmaps.com',
            'path' => 'src/Parser/clustrmaps.js',
            'path_to_deleter' => '',
        ],
        [// cloudflare, but works
            'name' => 'cyberBackgroundChecks.com',
            'path' => 'src/Parser/cyberbackgroundchecks.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'emailtracer.com',
            'path' => 'src/Parser/emailtracer.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'familytreenow.com',
            'path' => 'src/Parser/familytreenow.js',
            'path_to_deleter' => '',
        ],
//        [// not working, waiting for selector failed
//            'name' => 'fastpeoplesearch.com',
//            'path' => 'src/Parser/fastpeoplesearch.js',
//            'path_to_deleter' => '',
//        ],
//        [// cloudflare
//            'name' => 'findpeoplefast.com',
//            'path' => 'src/Parser/findpeoplefast.js',
//            'path_to_deleter' => '',
//        ],
        [
            'name' => 'findpeoplesearch.com',
            'path' => 'src/Parser/findpeoplesearch.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'firearmscalifornia.com',
            'path' => 'src/Parser/firearmscalifornia.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'floridaresidentsdirectory.com',
            'path' => 'src/Parser/floridaresidentsdirectory.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'freepeopledirectory.com',
            'path' => 'src/Parser/freepeopledirectory.js',
            'path_to_deleter' => '',
        ],
//        [// prompts you to enter an email address to send your results
//            'name' => 'governmentregistry.com',
//            'path' => 'src/Parser/governmentregistry.js',
//            'path_to_deleter' => '',
//        ],
//        [// not working, waiting for selector failed
//            'name' => 'persontrust.com',
//            'path' => 'src/Parser/persontrust.js',
//            'path_to_deleter' => '',
//        ],
        [
            'name' => 'instantcheckmate.com',
            'path' => 'src/Parser/instantcheckmate.js',
            'path_to_deleter' => '',
        ],
//        [// not working, waiting for selector failed
//            'name' => 'intelius.com',
//            'path' => 'src/Parser/intelius.js',
//            'path_to_deleter' => '',
//        ],
        [
            'name' => 'kwold.com',
            'path' => 'src/Parser/kwold.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'michiganresidentdatabase.com',
            'path' => 'src/Parser/michiganresidentdatabase.js',
            'path_to_deleter' => '',
        ],
//        [// not working, page https://www.mylife.com/ can't be loaded
//            'name' => 'mylife.com',
//            'path' => 'src/Parser/mylife.js',
//            'path_to_deleter' => '',
//        ],
        [
            'name' => 'northcarolinaresidentdatabase.com',
            'path' => 'src/Parser/northcarolinaresidentdatabase.js',
            'path_to_deleter' => '',
        ],
//        [// cloudflare
//            'name' => 'nuwber.com',
//            'path' => 'src/Parser/nuwber.js',
//            'path_to_deleter' => '',
//        ],
        [
            'name' => 'ohioresidentdatabase.com',
            'path' => 'src/Parser/ohioresidentdatabase.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'peekyou.com',
            'path' => 'src/Parser/peekyou.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'peopleFriends.com',
            'path' => 'src/Parser/peoplefinders.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'peoplewhiz.com',
            'path' => 'src/Parser/peoplewhiz.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'radaris.com',
            'path' => 'src/Parser/radaris.js',
            'path_to_deleter' => '',
        ],
//        [// not working, waiting for selector failed
//            'name' => 'searchquarry.com',
//            'path' => 'src/Parser/searchquarry.js',
//            'path_to_deleter' => '',
//        ],
        [
            'name' => 'spokeo.com',
            'path' => 'src/Parser/spokeo.js',
            'path_to_deleter' => '',
        ],
//        [// cloudflare
//            'name' => 'truePeopleSearch.com',
//            'path' => 'src/Parser/truepeoplesearch.js',
//            'path_to_deleter' => '',
//        ],
        [
            'name' => 'truthfinder.com',
            'path' => 'src/Parser/truthfinder.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'usaTrace.com',
            'path' => 'src/Parser/usatrace.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'usSearch.com',
            'path' => 'src/Parser/ussearch.js',
            'path_to_deleter' => '',
        ],
//        [// cloudflare
//            'name' => 'voterRecords.com',
//            'path' => 'src/Parser/voterrecords.js',
//            'path_to_deleter' => '',
//        ],
        [
            'name' => 'whitePages',
            'path' => 'src/Parser/whitepages.js',
            'path_to_deleter' => '',
        ],
        [
            'name' => 'zabasearch.com',
            'path' => 'src/Parser/zabasearch.js',
            'path_to_deleter' => '',
        ]
    ];
}
