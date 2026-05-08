async function getAllergens() {
  return await secureFetch(
    'http://localhost:8082/allergen',
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getDiets() {
  return await secureFetch(
    'http://localhost:8082/diet',
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getDishes() {
  return await secureFetch(
    'http://localhost:8082/dish',
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getMaterial() {
  return await secureFetch(
    'http://localhost:8082/material',
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getDrinkPackages() {
  return await secureFetch(
    'http://localhost:8082/drinkPackage',
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getPersonalPackages() {
  return await secureFetch(
    'http://localhost:8082/personalPackage',
    { method: 'GET' },
    ['employé', 'admin']
  );
}
async function getConditions() {
  return await secureFetch(
    'http://localhost:8082/condition',
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getSchedules() {
  return await secureFetch(
    'http://localhost:8082/schedule',
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getMenus() {
  return await secureFetch(
    'http://localhost:8082/menu',
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getCategoriesDish() {
  return await secureFetch(
    'http://localhost:8082/categoryDish',
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getCategoriesMaterial() {
  return await secureFetch(
    'http://localhost:8082/materialCategory',
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getNotices() {
  return await secureFetch(
    'http://localhost:8082/notice',
    { method: 'GET' },
    ['employé', 'admin']
  );
}

async function getOrders() {
  return await secureFetch(
    'http://localhost:8082/order',
    { method: 'GET' },
  );
}