async function getAllergens() {
  return await secureFetch(
    `${API_URL}/allergen`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getDiets() {
  return await secureFetch(
    `${API_URL}/diet`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getDishes() {
  return await secureFetch(
    `${API_URL}/dish`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getMaterial() {
  return await secureFetch(
    `${API_URL}/material`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getDrinkPackages() {
  return await secureFetch(
    `${API_URL}/drinkPackage`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getPersonalPackages() {
  return await secureFetch(
    `${API_URL}/personalPackage`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}
async function getConditions() {
  return await secureFetch(
    `${API_URL}/condition`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getSchedules() {
  return await secureFetch(
    `${API_URL}/schedule`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getMenus() {
  return await secureFetch(
    `${API_URL}/menu`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getCategoriesDish() {
  return await secureFetch(
    `${API_URL}/categoryDish`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getCategoriesMaterial() {
  return await secureFetch(
    `${API_URL}/materialCategory`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getNotices() {
  return await secureFetch(
    `${API_URL}/notice`,
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getOrders() {
  return await secureFetch(
    `${API_URL}/order`,
    { method: 'GET' },
  );
}