<template>
  <div class="container py-4">

    <div class="row mb-4">
      <div class="col-12">
        <h1 class="page-header">Tournaments Lists</h1>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="card-title mb-0">
                <font-awesome-icon icon="users" class="me-2 text-primary"/>
                Manage Tournaments
              </h5>
              <button class="btn btn-sm btn-outline-primary" @click="openAddTeamModal">
                <font-awesome-icon icon="plus" class="me-1"/>
                Add Tournament
              </button>
            </div>

            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                <tr>
                  <th style="width: 70%;">Name</th>
                  <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr class="team-row" v-for="(tournament, index) in tournaments" :key="index">
                  <td>
                    <div class="d-flex align-items-center">
                      <div
                          class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                          :style="{width: '40px', height: '40px' }"
                      >
                        <a :href="`/tournament-management/${tournament.id}`" class="text-white"
                           style="">{{ tournament.id }}</a>
                      </div>

                      <a :href="`/tournament-management/${tournament.id}`" class="text-black"
                         style=""> {{ tournament.name }}</a>

                    </div>
                  </td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-outline-danger ms-1" @click="confirmDeleteTournament(index)">
                      <font-awesome-icon icon="trash"/>
                    </button>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Add Tournament Modal -->
    <div class="modal fade" ref="teamModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Tournament</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveTournament">
              <div class="mb-3">
                <label for="teamName" class="form-label">Tournament Name</label>
                <input
                    type="text"
                    class="form-control"
                    id="teamName"
                    v-model="formData.name"
                    required
                >
              </div>

            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn  btn-primary" @click="saveTournament">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import Swal from 'sweetalert2';
import {Modal} from 'bootstrap';
import axios from "axios";
import {handelApiResponseError} from "../../GlobalMethods.js";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {useToast} from "vue-toastification";

const formData = ref({name: ""});
const teamModal = ref(null);
let modalInstance = null;
const tournaments = ref([]);
const toast = useToast();

const getTournaments = () => {
  axios.get("/api/tournaments").then(({data}) => {
    tournaments.value = data?.data ?? [];
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
}

onMounted(() => {
  modalInstance = new Modal(teamModal.value);
  getTournaments();
});


const openAddTeamModal = () => {
  formData.value = {name: ''};
  modalInstance.show();
};


const saveTournament = () => {
  if (!formData.value.name) return;
  axios.post("/api/tournaments", formData.value).then(({data}) => {
    toast.success("Tournament Successfully Added !!")
    modalInstance.hide();
    getTournaments();
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
};

const confirmDeleteTournament = (index) => {
  const tournamentName = tournaments.value[index].name;
  const tournamentID = tournaments.value[index].id;

  Swal.fire({
    title: 'Delete Tournament',
    text: `Are you sure you want to delete ${tournamentName}?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      axios.delete(`/api/tournaments/${tournamentID}?force=1`).then(({data}) => {
        toast.success("Tournament Deleted !!");
        getTournaments();
      }).catch(({response}) => {
        handelApiResponseError(response);
      })

    }
  });
};

</script>

<style scoped>
.page-header {
  position: relative;
  padding-bottom: 15px;
  margin-bottom: 30px;
}

.page-header:after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 3px;
  background-color: #0d6efd;
}

.card {
  border-radius: 15px;
  box-shadow: 0 6px 10px rgba(0, 0, 0, .08);
  transition: all .3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, .12);
}

.team-row {
  border-bottom: 1px solid #dee2e6;
  transition: all .2s ease;
}

.team-row:hover {
  background-color: #f1f3f5;
}

.btn-primary:hover {
  background-color: #0b5ed7;
  transform: translateY(-2px);
}

.table th {
  background-color: #212529;
  color: white;
  border: none;
  padding: 15px;
}
</style>