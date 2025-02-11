<template>
    <div class="container mt-5">
      <h2>Zoznam osôb</h2>
  
      <div class="mb-3">
        <button class="btn btn-success" @click="addUser">
          Pridaj osobu
        </button>
      </div>
  
      <div class="form-group mb-3">
        <input
          type="text"
          class="form-control"
          placeholder="Vyhľadaj osobu (meno, vek, pohlavie)"
          v-model="search"
          @input="searchPersons"
        />
      </div>
      
      <table class="table table-bordered table-hover">
        <thead class="thead-light">
          <tr>
            <th>ID</th>
            <th>Meno</th>
            <th>Vek</th>
            <th>Pohlavie</th>
            <th>Akcie</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="person in persons" :key="person.id">
            <td>{{ person.id }}</td>
            <td>{{ person.meno }}</td>
            <td>{{ person.vek }}</td>
            <td>{{ person.pohlavie }}</td>
            <td>
              <!-- Tlačidlo pre editáciu -->
              <button class="btn btn-primary btn-sm mr-2" @click="editPerson(person)">
                Edit
              </button>
              <!-- Tlačidlo pre odstránenie -->
              <button class="btn btn-danger btn-sm" @click="deletePerson(person.id)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div :class="['modal fade', { show: showAddModal }]" tabindex="-1" v-show="showAddModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Pridaj osobu</h5>
            <button type="button" class="btn-close" @click="closeAddUserModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitNewUser">
              <div class="mb-3">
                <label for="meno" class="form-label">Meno</label>
                <input
                  type="text"
                  class="form-control"
                  id="meno"
                  v-model="newUser.meno"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="vek" class="form-label">Vek</label>
                <input
                  type="number"
                  class="form-control"
                  id="vek"
                  v-model="newUser.vek"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="pohlavie" class="form-label">Pohlavie</label>
                <select
                  class="form-select"
                  id="pohlavie"
                  v-model="newUser.pohlavie"
                  required
                >
                  <option disabled value="">Vyberte pohlavie</option>
                  <option value="M">Muž</option>
                  <option value="Z">Žena</option>
                </select>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeAddUserModal">
                  Zrušiť
                </button>
                <button type="submit" class="btn btn-primary">
                  Pridať osobu
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  
    <div :class="['modal fade', { show: showEditModal }]" tabindex="-1" v-show="showEditModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Uprav osobu</h5>
            <button type="button" class="btn-close" @click="closeEditUserModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitEditUser">
              <div class="mb-3">
                <label for="edit-meno" class="form-label">Meno</label>
                <input
                  type="text"
                  class="form-control"
                  id="edit-meno"
                  v-model="editUser.meno"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="edit-vek" class="form-label">Vek</label>
                <input
                  type="number"
                  class="form-control"
                  id="edit-vek"
                  v-model="editUser.vek"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="edit-pohlavie" class="form-label">Pohlavie</label>
                <select
                  class="form-select"
                  id="edit-pohlavie"
                  v-model="editUser.pohlavie"
                  required
                >
                  <option disabled value="">Vyberte pohlavie</option>
                  <option value="M">Muž</option>
                  <option value="Z">Žena</option>
                </select>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeEditUserModal">
                  Zrušiť
                </button>
                <button type="submit" class="btn btn-primary">
                  Uložiť zmeny
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  
  </template>
  
  <script setup>
  import { ref, reactive, onMounted } from 'vue'
  import axios from 'axios'
  
  const persons = ref([])
  const search = ref('')
  
  const showAddModal = ref(false)
  const showEditModal = ref(false)
  
  const newUser = reactive({
    meno: '',
    vek: '',
    pohlavie: ''
  })
  
  const editUser = reactive({
    id: null,
    meno: '',
    vek: '',
    pohlavie: ''
  })
  
  function getPersons() {
    axios.get('/api/users')
      .then(response => {
        persons.value = response.data.users
      })
      .catch(error => {
        console.error('Chyba pri získavaní osôb:', error)
      })
  }
  
  function searchPersons() {
    axios.get('/api/user/filtered', { params: { search: search.value } })
      .then(response => {
        persons.value = response.data
      })
      .catch(error => {
        console.error('Chyba pri vyhľadávaní osôb:', error)
      })
  }
  
  function deletePerson(id) {
    if (confirm('Skutočne chcete zmazať túto osobu?')) {
      axios.delete(`/api/users/${id}`)
        .then(response => {
          getPersons()
        })
        .catch(error => {
          console.error('Chyba pri mazaní osoby:', error)
        })
    }
  }
  
  function editPerson(person) {
    editUser.id = person.id
    editUser.meno = person.meno
    editUser.vek = person.vek
    editUser.pohlavie = person.pohlavie
    showEditModal.value = true
  }
  
  function closeEditUserModal() {
    showEditModal.value = false
    editUser.id = null
    editUser.meno = ''
    editUser.vek = ''
    editUser.pohlavie = ''
  }
  
  function addUser() {
    console.log('Klikli sme na Pridaj osobu')
    showAddModal.value = true
  }
  
  function closeAddUserModal() {
    showAddModal.value = false
    newUser.meno = ''
    newUser.vek = ''
    newUser.pohlavie = ''
  }
 
  function submitNewUser() {
    axios.post('/api/users', newUser)
      .then(response => {
        console.log('Osoba pridaná:', response.data)
        getPersons()       
        closeAddUserModal() 
      })
      .catch(error => {
        console.error('Chyba pri pridávaní osoby:', error)
      })
  }
  
  function submitEditUser() {
    axios.put(`/api/users/${editUser.id}`, {
        meno: editUser.meno,
        vek: editUser.vek,
        pohlavie: editUser.pohlavie
      })
      .then(response => {
        console.log('Osoba aktualizovaná:', response.data)
        getPersons()         
        closeEditUserModal() 
      })
      .catch(error => {
        console.error('Chyba pri aktualizácii osoby:', error)
      })
  }
  
  onMounted(() => {
    getPersons()
  })
  </script>
  
  <style scoped>
  .modal {
    display: block; 
    background: rgba(0, 0, 0, 0.5);
    z-index: 1050;
  }
  
  /* Efekt fade-out a fade-in */
  .modal.fade:not(.show) {
    opacity: 0;
    transition: opacity 0.15s linear;
  }
  
  .modal.fade.show {
    opacity: 1;
    transition: opacity 0.15s linear;
  }
  
  .modal-dialog {
    margin: 1.75rem auto;
  }
  </style>
  