<?php

class pricefromsettingsCest {

	public function _before( AcceptanceTester $I ) {
		$I->amOnPage( '/' );
		$I->see( 'wcvendors' );
		$I->click( 'My account' );
		$I->fillField( '#username', 'vendor.two@yopmail.com' );
		$I->fillField( '#password', '1IZ)h7%J9wQNG@AUqE43y2%c' );
		$I->click( '.woocommerce-button' );
		$I->see( 'Hello vendor' );
	}

	public function frontpageWorks( AcceptanceTester $I ) {
		$I->click( 'Pro Dashboard' );
		$I->click( 'Settings' );
		$I->click( 'Shipping' );
		$I->wait( 3 );
		$I->click( '.button.insert' );
		$I->wait( 5 );
		$I->click( '#select2-_wcv_shipping_countries-container' );
		$I->wait( 5 );
		$I->fillField( '.select2-search__field', 'India' );
		$I->wait( 5 );
		$I->pressKey( '.select2-search__field', \Facebook\WebDriver\WebDriverKeys::ENTER );
		$I->wait( 5 );
		$I->fillField( 'td.fee > input:nth-child(1)', '45' );
		$I->wait( 5 );
		$I->click( '.button.insert' );
		$I->click( '.ui-sortable > tr:nth-child(2) > td:nth-child(2) > span:nth-child(2) > span:nth-child(1) > span:nth-child(1) > span:nth-child(1)' );
		$I->wait( 5 );
		$I->fillField( '.select2-search__field', 'China' );
		$I->wait( 5 );
		$I->pressKey( '.select2-search__field', \Facebook\WebDriver\WebDriverKeys::ENTER );
		$I->fillField( '.ui-sortable > tr:nth-child(2) > td:nth-child(5) > input:nth-child(1)', '35' );
		$I->wait( 5 );
		$I->click( '#store_save_button' );
		$I->waitForText( 'Store Settings Saved', '300' );
		$I->click( 'Pro Dashboard' );
		$I->click( 'Add product' );
		// Add Product name
		$I->fillField( '#post_title', 'Test For Country 35' );
		$I->fillField( '#_regular_price', '100' );
		$I->click( '#product_save_button' );
		$I->waitForText( 'Product Added.', '300' );
		$I->click( 'My account' );
		$I->click( 'Logout' );
		$I->wait( 5 );
		// Login in as customer
		$I->fillField( '#username', 'customer.one@yopmail.com' );
		$I->fillField( '#password', 'dM^gc87RPE&Osuj(EKPY)X8(' );
		$I->click( '.woocommerce-button' );
		// Search Product name
		$I->fillField( '#wp-block-search__input-1', 'Test For Country 35' );
		$I->click( '#block-2 > form > div > button' );
		// Click the Product name
		$I->click( 'Test For Country 35' );
		$I->wait( 5 );
		// Click add to cart button
		$I->click( 'Add to cart' );
		$I->wait( 10 );
		$I->click( '#content > div > div.woocommerce > div > a' );
		$I->wait( 5 );
		$I->see( 'Vendor2 Store Shipping: $45' );
		$I->click( '#post-7 > div > div > div.cart-collaterals > div > div > a' );
		$I->wait( 5 );
		$I->waitForText( 'Checkout', '300' );

		}

}

