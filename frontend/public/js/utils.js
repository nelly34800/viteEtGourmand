function sanitizeHTML(text){
    //fonction pour éviter les failles XSS
  const tempHTML = document.createElement('div');
  tempHTML.textContent = text;
  return tempHTML.innerHTML;
}

function renderStars(rating, maxStars = 5) {
    //fonction qui transforme note en chiffres en étoiles
  let html = '';

  for (let i = 1; i <= maxStars; i++) {
    html += i <= Math.floor(rating)
      ? '<i class="bi bi-star-fill"></i>'
      : '<i class="bi bi-star"></i>';
  }

  return `<div class="stars">${html}</div>`;
}

// fonction pour les fetch sécurisés avec vérification du rôle et gestion du token CSRF
async function secureFetch(url, options = {}, allowedRoles = []) {//prend en paramètre l'url, les options du fetch(get, post...) et les rôles autorisés
  // vérifie le rôle 
  const user = JSON.parse(localStorage.getItem('user'));
  if (!user) throw new Error("Utilisateur non connecté");
  if (allowedRoles.length && !allowedRoles.includes(user.role)) {
    // si le rôle de l'utilisateur n'est pas dans les rôles autorisés, on bloque l'accès
    throw new Error("Accès interdit : rôle non autorisé");
  }
  const csrfToken = localStorage.getItem('csrf_token');

  const fetchOptions = {
    credentials: 'include',
    headers: {
      'X-CSRF-Token': csrfToken,
      'Content-Type': 'application/json',
      ...(options.headers || {})
    },
    ...options
  };
  try {
    const response = await fetch(url, fetchOptions);
    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.error || 'Une erreur est survenue');
    }

    return data;
  } catch (error) {
    console.error("Erreur fetch sécurisé :", error);
    throw error;
  }
}