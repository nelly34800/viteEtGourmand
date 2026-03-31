// données "en dur" simulant la base de données
const noticesDB = [
    {
    note: "****",
    description: "très bien",
    prenom: "pren1"
  },
  {
    note: "**",
    description: "pas content",
    prenom: "pren2"
  },
  {
    note: "*****",
    description: "très bien",
    prenom: "pren3"
  },
  {
    note: "*",
    description: "pas content",
    prenom: "pren4"
  }
];

const carouselInner = document.getElementById("carouselInner");

// détermine combien d'avis selon la largeur de l'écran
let noticesPerSlide;

if (window.innerWidth >= 680) {   // breakpoint
  noticesPerSlide = 3;             // grand écran 3 images
} else {
  noticesPerSlide = 1;             // petit écran 1 image
}

// Générer les slides
for (let i = 0; i < noticesDB.length; i += noticesPerSlide) {
  // Sélection des avis pour cette slide
  const slideNotices = noticesDB.slice(i, i + noticesPerSlide);
  // Création de la div de slide
  const slideDiv = document.createElement("div");
  slideDiv.classList.add("carousel-item");
  // La première slide prend la classe active
  if (i === 0) slideDiv.classList.add("active");
  // Construit la slide
  let rowHTML = `<div class="row">`;
  slideNotices.forEach(p => {
    // Chaque avis prend toute la largeur en mobile, 1/3 en desktop
    rowHTML += `
      <div class="col-12 col-md-${12 / noticesPerSlide}">
        <div class="card p-3 bgc-primary">
          <p>Note: ${p.note}</p>
          <p>Commentaire: ${p.description}</p>
          <cite>${p.prenom}</cite>
        </div>
        <button type="submit" class="btn btn-secondary m-3" name="valider">Valider</button>
        <button type="submit" class="btn btn-danger m-3" name="rejeter">Rejeter</button>
      </div>
    `;
  });
  rowHTML += `</div>`;
  //  Ajouter le HTML dans la slide
  slideDiv.innerHTML = rowHTML;
  // Ajouter la slide dans le carousel
  carouselInner.appendChild(slideDiv);
}