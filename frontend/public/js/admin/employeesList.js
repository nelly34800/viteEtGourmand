async function loadUsers() {
  const csrfToken = localStorage.getItem('csrf_token');
  //appel la route get/user
  const response = await fetch('http://localhost:8082/user', {
    method: 'GET',
    credentials: 'include',
    headers: {
      'X-CSRF-Token': csrfToken
    }
  });

  //transforme JSON en objet JS
  const data = await response.json();

    if (!response.ok) {
    console.error(data);
    return;
  }
  //desktop
  const tbody = document.querySelector('tbody');
  tbody.innerHTML = '';

  //mobile
  const mobileContainer = document.getElementById('mobile-container');
  mobileContainer.innerHTML = '';
  data.forEach(user => {
    //desktop: boucle pour afficher le tableau de tous les employées
    const tr = document.createElement('tr');
    //inject dans le DOM
    tr.innerHTML = `
      <td>${user.first_name} ${user.last_name}</td>
      <td>${user.email}</td>
      <td>${user.postal_address} ${user.postal_code} ${user.city}</td>
      <td>${user.phone}</td>
      <td>
        <button class="btn btn-danger deleteBtn" data-id="${user.id}" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
      </td>
    `;

    tbody.appendChild(tr);

    //mobile: boucle pour afficher les cartes de tous les employées
    const card = document.createElement('div');
    card.className = 'card mb-3';

    card.innerHTML = `
      <div class="card-body bgc-secondary text-center">
        <h5>${user.first_name} ${user.last_name}</h5>
        <p>${user.email}</p>
        <p>${user.postal_address} ${user.postal_code} ${user.city}</p>
        <p>${user.phone}</p>
        <button class="btn btn-danger deleteBtn" data-id="${user.id}" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
      </div>
    `;

    mobileContainer.appendChild(card);
  });
}
// se lance au chargement
loadUsers();

//supprimer l'employé
document.addEventListener("click", async (e) => {
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");
  const userId = button.dataset.id;

  if (!confirm("Êtes-vous sûr de vouloir supprimer cet employé ?")) {
    return;
  }
  const csrfToken = localStorage.getItem('csrf_token');

  try {
    const response = await fetch(`http://localhost:8082/user/${userId}`, {
      method: 'DELETE',
      credentials: 'include',
      headers: {
        'X-CSRF-Token': csrfToken
      }
    });

    const data = await response.json();

    if (response.ok) {
      alert("Employé supprimé");

      // rafraîchit la liste
      loadUsers();

    } else {
      //
      button.closest('tr')?.remove();
      button.closest('.card')?.remove();
    }

  } catch (error) {
    console.error(error);
  }
});