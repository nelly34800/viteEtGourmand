// fonction pour charger les avis validés
async function loadNoticeValidate() {
  try {
    // Appel sécurisé vers l'API
    const response = await fetch('http://localhost:8082/noticeValidate');

    if (!response.ok) {
      throw new Error('Erreur API');
    }

    const data = await response.json();
    const carouselContent = document.querySelector("#carouselContent");

    carouselContent.innerHTML = '';

    // détermine combien d'avis selon la largeur de l'écran
    let noticesPerSlide = 3;

    // Générer les slides
    for (let i = 0; i < data.length; i += noticesPerSlide) {
      // Sélection des avis pour cette slide

  const slideDiv = document.createElement("div");
  slideDiv.classList.add("carousel-item");
  // La première slide prend la classe active
  if (i === 0) slideDiv.classList.add("active");

  // Construit la slide
  const row = document.createElement("div");
  row.classList.add("row", "justify-content-center");

  const slideNotices = data.slice(i, i + noticesPerSlide);

    slideNotices.forEach((notice) => {
      // création d'une ligne d'un avis
      const col = document.createElement("div");

      // desktop = 3 colonnes / Mobile = 1 colonne
      col.classList.add("col-12", "col-md-4", "mb-3");

      col.innerHTML = `
        <div class="d-flex justify-content-center">
          <div class="card bgc-primary p-4 text-center" style="max-width: 500px;">
            <div class="note mb-2">
              ${renderStars(Number(sanitizeHTML(notice.note)))}
            </div>
            <p>${sanitizeHTML(notice.description)}</p>
            <p class="signature mt-2">${sanitizeHTML(notice.signature)}</p>
          </div>
        </div>
      `;
      row.appendChild(col);
    });

    slideDiv.appendChild(row);
      carouselContent.appendChild(slideDiv);
    }

  } catch (error) {
      // Affiche l'erreur si problème API
      showMessage("Une erreur est survenue", "danger");
  }
}
// se lance au chargement
loadNoticeValidate();