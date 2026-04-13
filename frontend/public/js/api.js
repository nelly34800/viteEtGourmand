async function getAllergens() {
  return await secureFetch(
    'http://localhost:8082/allergen',
    { method: 'GET' },
    ['employee', 'admin']
  );
}

async function getDiets() {
  return await secureFetch(
    'http://localhost:8082/diet',
    { method: 'GET' },
    ['employee', 'admin']
  );
}

async function getDishes() {
  return await secureFetch(
    'http://localhost:8082/dish',
    { method: 'GET' },
    ['employee', 'admin']
  );
}

async function getMaterial() {
  return await secureFetch(
    'http://localhost:8082/material',
    { method: 'GET' },
    ['employee', 'admin']
  );
}

async function getDrinkPackages() {
  return await secureFetch(
    'http://localhost:8082/drinkPackage',
    { method: 'GET' },
    ['employee', 'admin']
  );
}

async function getPersonalPackages() {
  return await secureFetch(
    'http://localhost:8082/personalPackage',
    { method: 'GET' },
    ['employee', 'admin']
  );
}
async function getConditions() {
  return await secureFetch(
    'http://localhost:8082/condition',
    { method: 'GET' },
    ['employee', 'admin']
  );
}

async function getSchedules() {
  return await secureFetch(
    'http://localhost:8082/schedule',
    { method: 'GET' },
    ['employee', 'admin']
  );
}

async function getMenus() {
  return await secureFetch(
    'http://localhost:8082/menu',
    { method: 'GET' },
    ['employee', 'admin']
  );
}

async function getCategoriesDish() {
  return await secureFetch(
    'http://localhost:8082/categoryDish',
    { method: 'GET' },
    ['employee', 'admin']
  );
}

async function getCategoriesMaterial() {
  return await secureFetch(
    'http://localhost:8082/materialCategory',
    { method: 'GET' },
    ['employee', 'admin']
  );
}

async function getNotices() {
  return await secureFetch(
    'http://localhost:8082/notice',
    { method: 'GET' },
    ['employee', 'admin']
  );
}