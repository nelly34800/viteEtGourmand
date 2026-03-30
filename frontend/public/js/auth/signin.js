const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const btnSignin = document.getElementById('signin');

btnSignin.addEventListener('click', checkCredentials);

// vérifie le mail et le mot de passe 
async function checkCredentials(e) {
  e.preventDefault();

  try {
    const response = await fetch('http://localhost:8082/login', {
      method: 'POST',
      credentials: 'include', // récupère la session
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        email: emailInput.value,
        password: passwordInput.value
      })
    });
    const data = await response.json();

    const messageDiv = document.getElementById('signin-message');

    if (response.ok) {
      // afficher le message
      messageDiv.textContent = "Connexion réussie ! Vous allez être redirigé sur la page d'accueil";
      messageDiv.classList.remove("d-none");
      messageDiv.classList.add("alert-success");

      // stocker le CSRF
      localStorage.setItem('csrf_token', data.csrf_token);

        // stocker l'utilisateur
      localStorage.setItem('user', JSON.stringify(data.user));

      // redirection après 3 secondes
      setTimeout(() => {
        window.location.href = '/';
      }, 3000);

    } else {
      // message d'erreur
      messageDiv.textContent = data.error || "Une erreur est survenue";
      messageDiv.classList.remove("d-none");
      messageDiv.classList.add("alert-danger");

      emailInput.classList.add('is-invalid');
      passwordInput.classList.add('is-invalid');
    }

  } catch (error) {
    console.error("Erreur fetch :", error);
  }
}