<?php

namespace frontend\tests;

use frontend\tests\FunctionalTester;

class AboutCest
{

    public function checkAbout(FunctionalTester $I)
    {
        $I->amOnRoute('site/about');
        $I->see('About Images project', 'h1');
    }

}