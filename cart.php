<?php

//start the session 
session_start();

	//products 
	// ######## please do not alter the following code ######## 
	$products = array(
		array( "name" => "Sledgehammer", "price" => 125.75 ),
		array( "name" => "Axe", "price" => 190.50 ),
		array( "name" => "Bandsaw", "price" => 562.13 ),
		array( "name" => "Chisel", "price" => 12.9 ),
		array( "name" => "Hacksaw", "price" => 18.45 )
	);
	// ######## please do not alter the previous code ######## 

	
	//draws the products into a table embedded inside a form 
	function drawProducts(){
		//start form and table tags
		echo '<form name="products" action="" method="POST">';
		echo '<h1>Products</h1>';
		echo '<table class="productList"><tr><th>Name</th><th>Price</th><th>Add</th></tr>';
		
		//display each product available in a new table row 
		foreach($GLOBALS['products'] as $product){
			
			//prodcut information
			echo '<tr>';
			echo 	'<td>' . $product['name'] . '</td>';
			echo 	'<td>' . number_format($product['price'],2) . '</td>';
			echo 	'<td>' . '<button type="submit" name="addItem" value="'.$product['name'].'" >Add Item </button></td>';
			echo '</tr>';


		}
		//close the table and form tags
		echo '</table>';
		echo '</form>';
	}

	//draws the cart into a table embedded inside a form 
	function drawCart(){
		//start form and table tags
		echo '<form name="cart" action="" method="POST">';
		echo '<h1>Cart</h1>';
		echo '<table>';
		echo '<tr><th>Name</th><th>Price</th><th>Quantity</th><th>Total</th><th>Remove</th></tr>';
    	
		//hold the cumulative total price
		$totalPrice = 0;
		
		//display each item in the cart in a new table row 
		foreach($_SESSION['cart']->items as $cartItem){	
			
			//generate the total price for this item, and add it to the total price
			$itemTotalPrice = $cartItem['price'] * $cartItem['quantity'];
			$totalPrice += $itemTotalPrice;
			
			//item information
			echo '<tr>';
			echo 	'<td>' . $cartItem['name'] . '</td>';
			echo 	'<td>' . number_format($cartItem['price'],2) . '</td>';
			echo 	'<td>' . $cartItem['quantity'] . '</td>';
			echo 	'<td>' . number_format($itemTotalPrice,2) . '</td>';
			echo 	'<td>' . '<button type="submit" name="removeItem" value="'.$cartItem['name'].'" >Remove Item</button>' . '</td>';
			echo '</tr>';
			
		}
		//draw overall Total
		echo '<tr>';
		echo 	'<th>Overall Total</th><td></td><td></td>';
		echo 	'<td>' . number_format($totalPrice,2) . '</td>';
		echo '</tr>';
		
		//close the table and form tags
		echo '</table><br>';
		echo '</form>';
		
	}
	
	/* Cart Class 
		stores the cart in an array with the item name as the key
		
	*/
	class Cart {

		public $items = array();
			
		//add item to cart
		function addItem($itemKey){
			//if the item already exsists in the cart, add one.
			if(isset($this->items[$itemKey])){
				$this->items[$itemKey]['quantity'] = $this->items[$itemKey]['quantity'] + 1; 
			}
			
			//if the item is not already set in the cart.
			//Find the correct price off the item from the server side $products
			//note - I am looking up the prices server-side to protect from customers entering in their own prices in the html
			else{
				//find the corresponding product based on the product name (keys would be preferable)
				foreach($GLOBALS['products'] as $product){		
					if($itemKey == $product['name']){
						//set the item price server-side
						$itemPrice = $product['price'];
					}
				}
				
				//if there is a valid product with that name (key) and therefore has a corresponding price.
				//add it to the cart otherwise ignore it
				//note - This protects the server from having items in the shopping cart that don't exsist server side. 
				if(isset($itemPrice)){
					$this->items[$itemKey] = array( "name" => $itemKey, "price" => $itemPrice , "quantity" => 1);
				}
				
			}			
			return;
		}
		
		
		//removes item from cart given its name (key)
		function removeItem($itemKey){

			if(isset($this->items[$itemKey])){
				//remove the item from the cart including all of its quantity
				unset($this->items[$itemKey]);
				
				/*
				//code for if the items should be removed 1 item at a time 
				$this->items[$itemKey]['quantity'] = $this->items[$itemKey]['quantity'] - 1;
				if($this->items[$itemKey]['quantity'] == 0){	
					unset($this->items[$itemKey]);
				}
				*/
			}

			return;
		}
		
		
	}


	// if a cart does not exist for this session, create one.
	if(!isset($_SESSION['cart'])){
		$_SESSION['cart'] = new Cart();	
	}

	//if a post for removeItem is called.
	if(isset($_POST['removeItem'])){	
		$_SESSION['cart']->removeItem($_POST['removeItem']);
		//stop items redeleting on refresh - not needed, but just for consistency or if items were deleted one by one.
		header("Location:#");
	}
	//if a post for addItem is called.
	if(isset($_POST['addItem'])){
		$_SESSION['cart']->addItem($_POST['addItem']);
		//stop items resubmitting on refresh
		header("Location:#");
	}
	


	//draw the products and cart 
	drawProducts();
	drawCart();


