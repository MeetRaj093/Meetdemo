minimum shipping fee:-
<?php
class NationalminishippingCest
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
$I->click('a.quick-link-btn:nth-child(1)');
$I->fillField('#post_title','nike9');
$I->fillField('#_regular_price','500');
$I->click('.hide_if_virtual');
$I->fillField('#_shipping_fee_national_min_charge','10');
$I->click('#product_save_button');
$I->waitForText('Product Added. View product.', 300);
}
}