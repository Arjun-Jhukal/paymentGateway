const form = document.getElementById("checkout-form");
const payNowBtn = document.getElementById("pay-now-btn");

form.addEventListener("submit", (event) => {
  event.preventDefault();

  payNowBtn.disabled = true;
  payNowBtn.textContent = "Processing...";

  const payload = {
    amount: form.amount.value,
    product_identity: Date.now(),
    product_name: "Test Product",
    product_url: "http://example.com",
    user: {
      name: form.name.value,
      email: form.email.value,
      mobile: form.phone.value,
    },
  };

  fetch("payment-request.php", {
    method: "POST",
    body: JSON.stringify(payload),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.idx) {
        window.location.href = "success.php?idx=" + data.idx;
      } else {
        window.location.href = "failure.php";
      }
    })
    .catch((error) => {
      window.location.href = "failure.php";
    });
});
