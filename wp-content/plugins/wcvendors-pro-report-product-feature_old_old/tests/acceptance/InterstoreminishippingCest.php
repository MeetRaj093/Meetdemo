minimum shipping:-

<?php
class InterstoreminishippingCest
{
public function _before(AcceptanceTester $I)
{
$I->amOnPage('/');
$I->see('wcvendors');
$I->click('My account');
$I->fillField('#username', 'vendor.two@yopmail.com');
$I->fillField('#password', '1IZ)h7%J9wQNG@AUqE43y2%c');
$I->click('.woocommerce-button');
$I->see('Hello vendor');
}

public function frontpageWorks(AcceptanceTester $I)
{
$I->click('Pro Dashboard');
$I->click('#dashboard-menu-item-settings > a:nth-child(1)');
$I->click('.shipping');
$I->fillField('#_wcv_shipping_international_min_charge','100');
$I->click('#store_save_button');
$I->waitForText('Store Settings Saved',300);
}
}