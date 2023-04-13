<?php
// Khalti API endpoint
$khaltiUrl = 'https://khalti.com/api/v2/payment/verify/';

// Merchant ID, Merchant Key, and Secret Key obtained from Khalti Merchant Dashboard
$merchantId = 'YOUR_MERCHANT_ID';
$merchantKey = 'YOUR_MERCHANT_KEY';
$secretKey = 'test_secret_key_51510e2504934c0689f7a271958edfd7';

// Payment request parameters
$amount = 1000; // amount in paisa
$orderId = 'ORDER123'; // unique order ID
$customerName = 'John Doe'; // customer name
$customerEmail = 'johndoe@example.com'; // customer email
$customerPhone = '984XXXXXXX'; // customer phone number

// Generate checksum using SHA-256 hashing algorithm
$payload = array(
'public_key' => $merchantId,
'amount' => $amount,
'token' => $merchantKey,
'product_identity' => $orderId,
);
ksort($payload);
$payloadStr = http_build_query($payload, null, '&');
$checksum = hash('sha256', $secretKey . $payloadStr . $secretKey);

// Send payment request to Khalti API
$ch = curl_init($khaltiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
'public_key' => $merchantId,
'amount' => $amount,
'token' => $merchantKey,
'product_identity' => $orderId,
'product_name' => 'Test Product',
'product_url' => 'http://example.com/test',
'user' => array(
'name' => $customerName,
'email' => $customerEmail,
'mobile' => $customerPhone,
),
'checksum' => $checksum,
)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
$response = curl_exec($ch);
curl_close($ch);

// Process payment response
if ($response) {
$responseArr = json_decode($response, true);
if ($responseArr['idx']) {
// Payment successful
$idx = $responseArr['idx']; // Khalti transaction ID
// Update order status and redirect to success page
header('Location: success.php?idx=' . $idx);
exit;
} else {
// Payment failed
$errorMessage = $responseArr['detail'];
// Display error message to the user
echo 'Payment failed: ' . $errorMessage;
}
} else {
// Payment request failed
// Display error message to the user
echo 'Payment request failed';
}
?>