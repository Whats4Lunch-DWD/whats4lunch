<?php
// Class that provides methods for working with the form data.
// There should be NOTHING in this file except this class definition.

class TransactionsController {
	private $mapper;
    private $cart_items_mapper;
    
	public function __construct() {
		global $f3;

        $this->mapper = new DB\SQL\Mapper($f3->get('DB'),"transactions");	// create DB query mapper object
        $this->cart_items_mapper = new DB\SQL\Mapper($f3->get('DB'),"cart_items");
    }
    
    public function add($cart) {
        print_r($cart);
        die();
    }

    public function getTransaction($id) {
        $transaction = $this->mapper->load(["id=?",$id]);
        return $transaction;
    }

}