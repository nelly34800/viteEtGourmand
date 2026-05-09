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
  if (!user) {
    throw new Error("Utilisateur non connecté");
  } 
  const method = (options.method || "GET").toUpperCase();
  // envoie dans l'entête de requête le type d'envoie
  const headers = {
    "Content-Type": "application/json",
    ...(options.headers || {})
  };
  // si le type d'envoie n'est pas get, ajoute le token CSRF
  if (["POST", "PUT", "DELETE"].includes(method)) {
    const csrfToken = localStorage.getItem("csrf_token");

    if (!csrfToken) {
      throw new Error("Token CSRF manquant");
    }

    headers["X-CSRF-Token"] = csrfToken;
  }

  const fetchOptions = {
    ...options,
    method,
    credentials: "include",
    headers
  };

  try {
    const response = await fetch(url, fetchOptions);
    const text = await response.text();

  let data;
  try {
    data = JSON.parse(text);
  } catch (e) {
    console.error("Réponse brute serveur non JSON :", text);
    throw new Error("Le serveur a renvoyé une réponse invalide.");
  }

    if (!response.ok) {
      throw new Error(data.error || "Une erreur est survenue");
    }

    return data;

  } catch (error) {
    console.error("Erreur fetch sécurisé :", error);
    throw error;
  }
}
// fonction pour les boutons d'action (modifier/supprimer: évite répétition)
function createActionButtons(id) {
  const container = document.createElement("div");

  const editBtn = document.createElement("button");
  editBtn.className = "btn btn-secondary editBtn m-1";
  editBtn.dataset.id = id;
  editBtn.textContent = "modifier";

  const deleteBtn = document.createElement("button");
  deleteBtn.className = "btn btn-danger deleteBtn m-1";
  deleteBtn.dataset.id = id;

  const icon = document.createElement("i");
  icon.className = "bi bi-trash";
  deleteBtn.appendChild(icon);

  container.appendChild(editBtn);
  container.appendChild(deleteBtn);

  return container;
}

// fonction pour le bouton d'action (ajouter: évite répétition)
function CreateAddButton(id) {
  const container = document.createElement("div");

  const addBtn = document.createElement("button");
  addBtn.className = "btn btn-primary addBtn m-1";
  addBtn.dataset.id = id;
  addBtn.textContent = "ajouter";

  container.appendChild(addBtn);

  return container;
}

//formatage des horaires
const formatTime = (time) => {
  const [hours, minutes] = time.split(":");
  return `${hours}h${minutes}`;
};

// affichage des messages
function showMessage(message, type = "info") {
  const messageDiv = document.getElementById("messageDiv");

  if (!messageDiv) return;

  messageDiv.textContent = message;
  messageDiv.className = `alert alert-${type}`;
  messageDiv.classList.remove("d-none");

  // disparition automatique (optionnel mais propre)
  setTimeout(() => {
    messageDiv.classList.add("d-none");
  }, 3000);
}
//affichage icones emprunts et retours matériel
function booleanIcon(value) {
  return value
    ? '<i class="bi bi-check-circle-fill text-success"></i>'
    : '<i class="bi bi-x-circle-fill text-danger"></i>';
}