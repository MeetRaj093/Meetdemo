Disable national shipping:-

<?php
class DisablenatstoreshipCest
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
$I->click('#_wcv_shipping_fee_national_disable');
$I->click('#store_save_button');
$I->waitForText('Store Settings Saved',300);
}
}