async function loadUsers() {
  try {
  //appel la route get/user
    const data = await secureFetch(
      `${API_URL}/user`, {
      method: 'GET'},
      ['admin']
    );
    //desktop: vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';

    //mobile
    const mobileContainer = document.getElementById('mobile-container');
    mobileContainer.innerHTML = '';

    // boucle sur chaque employé
    data.forEach(user => {
      //desktop: création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      // Nom
      const tdtName = document.createElement("td");
      tdtName.textContent = `${user.first_name} ${user.last_name}`;
      tr.appendChild(tdtName);
      // Email
      const tdemail = document.createElement("td");
      tdemail.textContent = user.email;
      tr.appendChild(tdemail);
      // Adresse
      const tdaddress = document.createElement("td");
      tdaddress.textContent = `${user.postal_address} ${user.postal_code} ${user.city}`;
      tr.appendChild(tdaddress);
      // Téléphone
      const tdphone = document.createElement("td");
      tdphone.textContent = user.phone;
      tr.appendChild(tdphone);
      // Actions
      const tdAction = document.createElement("td");

      const tdDeleteButton = document.createElement("button");
      tdDeleteButton.className = "btn btn-danger deleteBtn m-1";
      tdDeleteButton.dataset.id = user.id;

      const tdIcon = document.createElement("i");
      tdIcon.className = "bi bi-trash";

      tdDeleteButton.appendChild(tdIcon);
      tdAction.appendChild(tdDeleteButton);

      tr.appendChild(tdAction);

      // ajout dans le DOM
      tbody.appendChild(tr);

      //mobile: boucle pour afficher les cartes de tous les employées
      const card = document.createElement('div');
      card.className = 'card mb-3';

      const cardBody = document.createElement('div');
      cardBody.className = 'card-body bgc-secondary text-center';
      // nom
      const cardTitle = document.createElement('h5');
      cardTitle.textContent = `${user.first_name} ${user.last_name}`;
      cardBody.appendChild(cardTitle);
      // email
      const cardEmail = document.createElement('p');
      cardEmail.textContent = user.email;
      cardBody.appendChild(cardEmail);
      // adresse
      const cardAddress = document.createElement('p');
      cardAddress.textContent = ` ${user.postal_address} ${user.postal_code} ${user.city}`;
      cardBody.appendChild(cardAddress);
      // téléphone
      const cardPhone = document.createElement('p');
      cardPhone.textContent = user.phone;
      cardBody.appendChild(cardPhone);
      // Actions
      const cardAction = document.createElement("p");
      const deleteButton = document.createElement("button");
      deleteButton.className = "btn btn-danger deleteBtn m-1";
      deleteButton.dataset.id = user.id;

      const icon = document.createElement("i");
      icon.className = "bi bi-trash";
      deleteButton.appendChild(icon);
      cardAction.appendChild(deleteButton);
      cardBody.appendChild(cardAction);
      card.appendChild(cardBody);
      mobileContainer.appendChild(card);
    });
   } catch (error) {
      // affiche l'erreur si problème API
      alert(error.message);
  }
}
// se lance au chargement
loadUsers();

//supprimer l'employé
document.addEventListener("click", async (e) => {
  // vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");
  // récupère l'id
  const userId = button.dataset.id;
  // confirmation utilisateur
  if (!confirm("Êtes-vous sûr de vouloir supprimer cet employé ?")) return;

  try {
    // appel API DELETE
    await secureFetch(
      `${API_URL}/user/${userId}`,
       { method: 'DELETE', }
    );

    // message succès
    alert("Employé supprimé");

    // rafraîchit la liste
    loadUsers();

  } catch (error) {
    alert(error.message);
  }
});