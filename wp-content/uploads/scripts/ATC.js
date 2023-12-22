console.log("Called: Add To Cart Snippet")

console.log('Debug Stock');
var stockHTML = document.querySelector("p.stock").innerHTML;
console.log('')
console.log('//Debug Stock')


// Create a function called "revealAddToCartButton" that will reveal the add to cart button when the Condition selected is New and the Quantity selected is equal to or less than the quantity in stock.   

function revealAddToCartButton() {
    var condition = document.getElementById("condition").value;
    var quantity = document.querySelector('input[name="quantity"]').value;
    var stockHTML = document.querySelector("p.stock").innerHTML;
    var addToCartButton = document.querySelector("button.single_add_to_cart_button");
    // if stockHTML contains a number, then stock = that number, otherwise stock = 0, be sure to include the entire number and not just the first digit
    var stock = stockHTML.match(/\d+/) ? stockHTML.match(/\d+/)[0] : 0;

    if (condition === "New" && Number(quantity) <= Number(stock)) {
        addToCartButton.classList.add('showCartButton');
        addToCartButton.classList.remove('hideCartButton');
        console.log("condition is new and quantity is less than or equal to stock")
    } else {
        console.log("condition is not new or quantity is greater than stock")
        addToCartButton.classList.remove('showCartButton');
        addToCartButton.classList.add('hideCartButton');
    }

    console.log("Condition selected: " + condition);
    console.log("Qty selected: " + quantity);
    console.log("Stock: " + stock);
}

// document.getElementById("condition").addEventListener("click", revealAddToCartButton);
// document.getElementById("condition").addEventListener("change", revealAddToCartButton);

// document.querySelector('input[name="quantity"]').addEventListener("click", revealAddToCartButton);
// document.querySelector('input[name="quantity"]').addEventListener("change", revealAddToCartButton);

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("condition").addEventListener("click", revealAddToCartButton);
    document.getElementById("condition").addEventListener("change", revealAddToCartButton);
    document.querySelector('input[name="quantity"]').addEventListener("change", revealAddToCartButton);
	
    revealAddToCartButton();
});

// Create a function that swaps $0.00 with "Get a quote."

function updatePrice() {
	var priceContainer = document.getElementById('price-container');
	if (priceContainer) {
		var priceHtml = priceContainer.innerHTML;
		if (priceHtml.trim() === '<span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>0.00</bdi></span></span>') {
			priceContainer.innerHTML = '<span class="nr-varprice-quote">Get a quote for the best price available.</span>';
		}
	}
}

document.addEventListener('DOMContentLoaded', function() {
	var conditionElement = document.getElementById('condition');
	if (conditionElement) {
		conditionElement.addEventListener('click', updatePrice);
		conditionElement.addEventListener('change', updatePrice);
	}
});

