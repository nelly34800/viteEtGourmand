const acceptCgv = document.getElementById("acceptCgv");
const acceptAllergens = document.getElementById("acceptAllergens");
const acceptConditions = document.getElementById("acceptConditions");
const confirmOrderBtn = document.getElementById("confirmOrderBtn");

if (!acceptCgv || !acceptAllergens || !acceptConditions || !confirmOrderBtn) {
} else {
  function validateConfirmation() {
    confirmOrderBtn.disabled = !(
      acceptCgv.checked &&
      acceptAllergens.checked &&
      acceptConditions.checked
    );
  }

  [acceptCgv, acceptAllergens, acceptConditions].forEach(input => {
    input.addEventListener("change", validateConfirmation);
  });

  confirmOrderBtn.addEventListener("click", async (event) => {
  event.preventDefault();

  try {
    const response = await secureFetch("http://localhost:8082/order", {
      method: "POST",
      body: JSON.stringify({
        service_date: localStorage.getItem("service_date")
      })
    }, ["client"]);
    showMessage("Commande enregistrée avec succès", "success");

    setTimeout(() => {
      window.location.href = "/account";
    }, 2000);

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
});
  validateConfirmation();
}