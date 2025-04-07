const axios = require('axios');

const API_BASE = 'http://127.0.0.1:8000';

jest.setTimeout(15000);

describe('Account API', () => {
  let createdUserId;

  it('GET /accounts - should return all users', async () => {
    const res = await axios.get(`${API_BASE}/accounts`);
    expect(res.status).toBe(200);
    expect(Array.isArray(res.data.data)).toBe(true);
  });

  it('POST /accounts - should create a new user', async () => {
    const payload = {
      name: 'Test User',
      email: `test${Date.now()}@mail.com`,
      age: 25
    };

    const res = await axios.post(`${API_BASE}/accounts`, payload, {
      headers: { 'Content-Type': 'application/json' }
    });

    expect(res.status).toBe(200);
    expect(res.data.message).toMatch(/berhasil ditambahkan/i);

    const userRes = await axios.get(`${API_BASE}/accounts`);
    createdUserId = userRes.data.data.find(u => u.email === payload.email)?.id;
    expect(createdUserId).toBeTruthy();
  });

  it('PUT /accounts/{id} - should update user', async () => {
    const res = await axios.post(`${API_BASE}/accounts/${createdUserId}`, {
      _method: 'PUT',
      name: 'Updated User',
      email: `updated${Date.now()}@mail.com`,
      age: 30
    }, {
      headers: { 'Content-Type': 'application/json' }
    });

    expect(res.status).toBe(200);
    expect(res.data.message).toMatch(/berhasil diupdate/i);
  });

  it('DELETE /accounts/{id} - should delete user', async () => {
    const res = await axios.post(`${API_BASE}/accounts/${createdUserId}`, {
      _method: 'DELETE'
    }, {
      headers: { 'Content-Type': 'application/json' }
    });

    expect(res.status).toBe(200);
    expect(res.data.message).toMatch(/berhasil dihapus/i);
  });
});
