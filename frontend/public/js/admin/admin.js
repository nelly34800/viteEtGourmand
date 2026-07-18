let orderMenuChart = null;
let caMenuChart = null;
// récupérer les stats et afficher le graphique commandes/menu
async function loadOrdersByMenu() {
  try {
    const data = await secureFetch(
        `${API_URL}/statistics/orders-by-menu`, {
             method: "GET"
    }, ["admin"]);

    orderMenuChart = new Chart(
      document.getElementById('orderMenuChart'), {
        type: 'pie',
        data: {
          labels: data.map(item => item._id),
          datasets: [{
            label: 'Nombre de commandes',
            data: data.map(item => item.nombre_commandes),
            backgroundColor: [
              '#22b8cf',
              '#f06595',
              '#a6e63f',
              '#9775fa',
              '#ffa94d',
              '#339af0',
              '#51cf66',
              '#ff6b6b',
              '#660000',
              '#fff2c2',
            ],
            borderWidth: 2,
            hoverOffset: 10
          }]
        },
        options: {
          responsive: true,
          cutout: '40%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                color: '#660000',
                font: {
                  size: 16
                },
                padding: 20
              }
            }
          }
        }
      }
    );
    fillMenusSelect(data);
  } catch (error) {
    console.error("Erreur chargement statistiques :", error);
  }
}
// récupérer les stats et afficher le graphique de
async function loadRevenueByMenu(url = null) {
  try {
    const finalUrl = url ?? `${API_URL}/statistics/revenue-by-menu`;
    const data = await secureFetch(finalUrl, {
      method: "GET"
    }, ["admin"]);

    // création initiale du graphique
    if (!caMenuChart) {
      caMenuChart = new Chart(
        document.getElementById('CAmenuChart'), {
          type: 'bar',
          data: {
            labels: data.map(item => item._id),
            datasets: [{
              label: "Chiffre d'affaires",
              data: data.map(item => item.chiffre_affaires),
              backgroundColor: '#660000',
              borderRadius: 5
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                display: false,
                labels: {
                  color: '#660000',
                  font: {
                    size: 16
                  }
                }
              }
            },
            scales: {
              x: {
                ticks: {
                  color: '#660000',
                  font: {
                    size: 16
                  },
                  minRotation: 15
                } 
              },
              y: {
                ticks: {
                  color: '#660000',
                  font: {
                    size: 16
                  }
                }
              }
            }
          }
        }
      );
    } else {
      // mise à jour après filtre
        caMenuChart.data.labels = data.map(item => item._id);
        caMenuChart.data.datasets[0].data = data.map(item => item.chiffre_affaires);
        caMenuChart.update();
    }
  } catch (error) {
    console.error("Erreur chargement statistiques :", error);
  }
}
// remplir le select avec les noms des menus
function fillMenusSelect(data) {
  const menuFilter = document.getElementById('menuFilter');

  menuFilter.innerHTML = `<option value="">Tous les menus</option>`;
  data.forEach(item => {
    const option =document.createElement('option');

    option.value = item._id;
    option.textContent = item._id;

    menuFilter.appendChild(option);
  });
}
// filtre le chiffre d'affaires par menu et date
document.getElementById('filtrerCA').addEventListener('click', async () => {
  const menuName = document.getElementById('menuFilter').value;
  const dateDebut = document.getElementById('dateDebut').value;
  const dateFin = document.getElementById('dateFin').value;

  const params = new URLSearchParams();

  if (menuName) {
    params.append('menu_name', menuName);
  }

  if (dateDebut) {
    params.append('start_date', dateDebut);
  }

  if (dateFin) {
    params.append('end_date', dateFin);
  }

  const url = `${API_URL}/statistics/revenue-by-menu?${params.toString()}`;
  await loadRevenueByMenu(url);
});

loadOrdersByMenu();
loadRevenueByMenu();