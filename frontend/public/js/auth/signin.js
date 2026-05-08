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

    if (response.ok) {

      // stocker le CSRF
      localStorage.setItem('csrf_token', data.csrf_token);

        // stocker l'utilisateur
      localStorage.setItem('user', JSON.stringify(data.user));

      // afficher le message
      showMessage("Connexion réussie ! Vous allez être redirigé", "success");

      // redirection
      const redirect = localStorage.getItem("redirectAfterLogin");

      setTimeout(() => {
        if (redirect) {
          localStorage.removeItem("redirectAfterLogin");
          window.location.href = redirect;
        } else {
          window.location.href = "/";
        }
      }, 3000);

    } else {
      // message d'erreur
      showMessage("Une erreur est survenue", "danger");

      emailInput.classList.add('is-invalid');
      passwordInput.classList.add('is-invalid');
    }

  } catch (error) {
    console.error("Erreur fetch :", error);
  }
}