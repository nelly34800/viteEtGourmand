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