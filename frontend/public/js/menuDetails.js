// données "en dur" simulant la base de données
const menuImages = [
  { src: "../assets/img/asperge.jpg", alt: "Plat 1" },
  { src: "../assets/img/brochette_fruits.jpg", alt: "Plat 2" },
  { src: "../assets/img/burger.jpg", alt: "Plat 3" },
  { src: "../assets/img/gambas.jpg", alt: "Plat 4" },
  { src: "../assets/img/gigot_agneau.jpg", alt: "Plat 5" },
  { src: "../assets/img/entremet_framb_choc.jpg", alt: "Plat 6" }
];

const carouselInner = document.getElementById("carouselInner");

// détermine combien d'images selon la largeur de l'écran
let imagesPerSlide;

if (window.innerWidth >= 680) {   // breakpoint
  imagesPerSlide = 3;             // grand écran 3 images
} else {
  imagesPerSlide = 1;             // petit écran 1 image
}

// Générer les slides
for (let i = 0; i < menuImages.length; i += imagesPerSlide) {
  // Sélection des images pour cette slide
  const slideImages = menuImages.slice(i, i + imagesPerSlide);
  // Création de la div de slide
  const slideDiv = document.createElement("div");
  slideDiv.classList.add("carousel-item");
  // La première slide prend la classe active
  if (i === 0) slideDiv.classList.add("active");
  // Construit la slide
  let rowHTML = `<div class="row">`;
  slideImages.forEach(img => {
    // Chaque image prend toute la largeur en mobile, 1/3 en desktop
    rowHTML += `
      <div class="col-12 col-lg-${12 / imagesPerSlide}">
        <img src="${img.src}" class="d-block w-100" alt="${img.alt}">
      </div>
    `;
  });
  rowHTML += `</div>`;
  //  Ajouter le HTML dans la slide
  slideDiv.innerHTML = rowHTML;
  // Ajouter la slide dans le carousel
  carouselInner.appendChild(slideDiv);
}