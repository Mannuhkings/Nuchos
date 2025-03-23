<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Function to retrieve access token from M-Pesa API
function getAccessToken() {
    return "mock_access_token_123456"; // Simulated access token
}

// Check if the user is logged in, otherwise redirect to the login page
if (strlen($_SESSION['login']) == 0) {
    header('location:login.php');
} else {
    if (isset($_POST['submit'])) {
        // Retrieve the total amount from the cart
        $totalAmount = 0;
        foreach ($_SESSION['cart'] as $key => $value) {
            $totalAmount += $value['item_price'] * $value['quantity'];
        }

        // Update payment method in the orders table
        mysqli_query($con, "UPDATE orders SET paymentMethod='" . $_POST['paymethod'] . "' WHERE userId='" . $_SESSION['id'] . "' AND paymentMethod IS NULL");

        // Validate user phone number format
        $userPhone = $_POST['phone'];
        if (!preg_match("/^\d{10}$/", $userPhone)) {
            echo "<div style='text-align: center; font-size: 24px; margin-top: 20%;'>Invalid phone number format. Please enter a 10-digit phone number.</div>";
            exit;
        }

        // Retrieve access token from M-Pesa API
        $accessToken = getAccessToken();

        // Simulated Payment Processing
        if ($_POST['paymethod'] == "Lipa na Mpesa") {
            echo "<div style='text-align: center; font-size: 24px; margin-top: 20%;'>Processing Payment...</div>";
            
            // Simulated API Response
            $response = ["ResponseCode" => "0", "ResponseMessage" => "Success"];
            
            if ($response["ResponseCode"] === "0") {
                echo "<div style='text-align: center; font-size: 24px; margin-top: 20%;'>Payment Successful!<br>";
                echo "Transaction Details:<br>";
                echo "Amount: $totalAmount<br>";
                echo "Phone Number: $userPhone<br>";
                echo "Response Message: " . $response["ResponseMessage"] . "<br>";
                
                // Clear the cart session
                unset($_SESSION['cart']);
                echo "<br>Redirecting to homepage...</div>";
                echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 3000);</script>";
            } else {
                echo "<div style='text-align: center; font-size: 24px; margin-top: 20%;'>Payment Failed: " . $response["ResponseMessage"] . "</div>";
            }
        } else {
            echo "<div style='text-align: center; font-size: 24px; margin-top: 20%;'>Payment method: Cash on Delivery selected.<br>Please pay upon delivery.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Method</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<div class="container">
    <h2>Choose Payment Method</h2>
    <form name="payment" method="post">
        <label>
            <input type="radio" name="paymethod" value="COD" checked> Cash on Delivery (COD)
        </label>
        <br>
        <label>
            <input type="radio" name="paymethod" value="Lipa na Mpesa"> Lipa na Mpesa
        </label>
        <br><br>
        <label for="phone">Enter Your Phone Number:</label>
        <input type="text" id="phone" name="phone" placeholder="Enter 10-digit phone number" required>
        <br><br>
        <input type="submit" value="Submit Payment" name="submit" class="btn btn-primary">
    </form>
</div>
</body>
</html>