<!DOCTYPE html>
<?php
require_once("../connection.php");
session_start();

$seller_id = $_SESSION['id'];
// Get the order_id from the URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
// Fetch available products
$product_query = "SELECT product_ID, product_name, product_price FROM product WHERE seller_ID = $seller_id";
$product_result = mysqli_query($db, $product_query);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product to Order #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Add Product to Order #<?php echo $order_id; ?></h2>

        <form action="./submit_product.php" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

            <!-- Product Selection with Datalist -->
            <div class="mb-4">
                <label for="product_name" class="block text-sm font-medium text-gray-700">Select Product</label>
                <input list="product_list" id="product_name" name="product_name" 
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md" 
                       placeholder="Start typing product name" required>
                
                <datalist id="product_list">
                    <?php while ($product = mysqli_fetch_assoc($product_result)): ?>
                        <option value="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                data-price="<?php echo htmlspecialchars($product['product_price']); ?>" 
                                data-id="<?php echo $product['product_ID']; ?>">
                            <?php echo htmlspecialchars($product['product_name']) . " - $" . htmlspecialchars($product['product_price']); ?>
                        </option>
                    <?php endwhile; ?>
                </datalist>
            </div>

            <!-- Quantity Input -->
            <div class="mb-4">
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" id="quantity" name="quantity" 
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md" 
                       placeholder="Enter quantity" required>
            </div>

            <button type="submit" class="w-full py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">
                Add Product
            </button>
        </form>

        <a href="../order_details?order_id=<?php echo $order_id; ?>" class="mt-4 inline-block text-blue-600 hover:underline">Back to Order Details</a>
    </div>

    <script>
        // JavaScript to autofill hidden product ID and price based on selection
        const productInput = document.getElementById('product_name');
        const productList = document.getElementById('product_list');

        productInput.addEventListener('input', function() {
            const options = productList.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === productInput.value) {
                    const productID = options[i].getAttribute('data-id');
                    const productPrice = options[i].getAttribute('data-price');
                    
                    // Create hidden fields for product ID and price
                    if (!document.getElementById('product_id')) {
                        const productIdInput = document.createElement('input');
                        productIdInput.type = 'hidden';
                        productIdInput.id = 'product_id';
                        productIdInput.name = 'product_id';
                        document.forms[0].appendChild(productIdInput);
                    }
                    document.getElementById('product_id').value = productID;

                    if (!document.getElementById('product_price')) {
                        const productPriceInput = document.createElement('input');
                        productPriceInput.type = 'hidden';
                        productPriceInput.id = 'product_price';
                        productPriceInput.name = 'product_price';
                        document.forms[0].appendChild(productPriceInput);
                    }
                    document.getElementById('product_price').value = productPrice;
                }
            }
        });
    </script>
</body>
</html>
