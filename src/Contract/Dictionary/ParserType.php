<?php

declare(strict_types=1);

namespace App\Contract\Dictionary;

enum ParserType
{
    case Parser411;
    case ParserAdvancedBackgroundChecks;
    case ParserAddresses;
    case ParserAmericaPhoneBook;
    case ParserAnyWho;
    case ParserBackgroundAlert;
    case ParserBackgroundCheck;
    case ParserBeenVerified;
    case ParserClustrMaps;
    case ParserCyberBackgroundChecks;
    case ParserEmailTracer;
    case ParserFamilyTreeNow;
    case ParserFastPeopleSearch;
    case ParserFindPeopleFast;
    case ParserFindPeopleSearch;
    case ParserFireArmsCalifornia;
    case ParserFloridaResidentsDirectory;
    case ParserFreePeopleDirectory;
    case ParserGovernmentRegistry;
    case ParserInstantCheckmate;
    case ParserIntelius;
    case ParserKwold;
    case ParserMichiganResidentDatabase;
    case ParserMyLife;
    case Parsernorthcarolinaresidentdatabase;
    case ParserNuwber;
    case ParserOhioResidentDatabase;
    case ParserPeekYou;
    case ParserPeopleFinders;
    case ParserPeopleWhiz;
    case ParserPersonTrust;
    case ParserRadaris;
    case ParserSearchQuarry;
    case ParserSpokeo;
    case ParserTruePeopleSearch;
    case ParserTruthFinder;
    case ParserUsaTrace;
    case ParserUsSearch;
    case ParserVoterRecords;
    case ParserWhitePages;
    case ParserZabaSearch;
}
