const displayReviews = document.getElementById("allNotices");

//récupérer les informations des avis 
let note = '4';
let description = 'Super expérience, je recommande !';
let firstName = 'Julie'
let myNote = getNotice(note,description, firstName);
displayReviews.innerHTML = myNote;

function getNotice(note,description, firstName){
  note = sanitizeHTML(note);
  description = sanitizeHTML(description);
    return `  <div class="col p-3">
      <div class="image-card">
        ${renderStars(Number(note))}
        <p class="description">${description}</p>
        <p class="signature">${firstName}</p>
      </div>
    </div>`
}