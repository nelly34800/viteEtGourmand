// fonction pour charger les horaires
async function loadSchedules() {
  try {
    // Appel sécurisé vers l'API (GET: fonction dans api.js)
    const data = await getSchedules();
    // dekstop: vide le tableau
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';

    //mobile
    const mobileContainer = document.getElementById('mobile-container');
    mobileContainer.innerHTML = '';

    // boucle sur chaque horaire reçu
    data.forEach(schedule => {
      // desktop: création d'une ligne
      const tr = document.createElement('tr');
      //inject dans le DOM
      // Nom
      const tdName = document.createElement("td");
      tdName.textContent = schedule.schedule_name;
      tr.appendChild(tdName);
      // jour de début
      const tdFirst = document.createElement("td");
      tdFirst.textContent = schedule.first_day;
      tr.appendChild(tdFirst);
      // jour de fin
      const tdLast = document.createElement("td");
      tdLast.textContent = schedule.last_day;
      tr.appendChild(tdLast);
      // heure de fin
      const tdOpen = document.createElement("td");
      tdOpen.textContent = formatTime(schedule.opening_time);
      tr.appendChild(tdOpen);
      // heure de fin
      const tdClosing = document.createElement("td");
      tdClosing.textContent = formatTime(schedule.closing_time);
      tr.appendChild(tdClosing);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const tdAction = document.createElement("td");
      tdAction.appendChild(createActionButtons(schedule.id));
      tr.appendChild(tdAction);

      // ajout dans le DOM
      tbody.appendChild(tr);

      //mobile: boucle pour afficher les cartes de tous les horaires
      const card = document.createElement('div');
      card.className = 'card mb-3';

      const cardBody = document.createElement('div');
      cardBody.className = 'card-body bgc-secondary text-center';
      // Titre
      const title = document.createElement('h2');
      title.textContent = schedule.schedule_name;
      cardBody.appendChild(title);
      // Jours
      const days = document.createElement('p');
      days.textContent = `${schedule.first_day} - ${schedule.last_day}`;
      cardBody.appendChild(days);
      // Heures
      const hours = document.createElement('p');
      hours.textContent = `${formatTime(schedule.opening_time)} - ${formatTime(schedule.closing_time)}`;
      cardBody.appendChild(hours);
      // Actions (récupère la fonction dans utils.js pour créer les boutons d'action)
      const cardAction = document.createElement('p');
      cardAction.appendChild(createActionButtons(schedule.id));
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
loadSchedules();

// modifier: aller sur la page createSchedule pour modifier l'horaire
document.addEventListener("click", (e) => {
  // vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // récupère l'id de l'horaire
    const scheduleId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/createSchedule?id=${scheduleId}`;
  }
});

//supprimer l'horaire
document.addEventListener("click", async (e) => {

  // vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;

  const button = e.target.closest(".deleteBtn");

  // récupère l'id
  const scheduleId = button.dataset.id;

  // confirmation utilisateur
  if (!confirm("Êtes-vous sûr de vouloir supprimer cet horaire ?")) return;

  try {
    // appel API DELETE
    await secureFetch(
      `http://localhost:8082/schedule/${scheduleId}`,
      { method: 'DELETE' },
      ['employee', 'admin']
    );

    // message succès
    alert("Horaire supprimé avec succès");

    // recharge la liste
    loadSchedules();

  } catch (error) {
    alert(error.message);
  }
});