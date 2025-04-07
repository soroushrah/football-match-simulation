<template>
  <div class="container py-4">
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="page-header">Tournament Teams</h1>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="card-title mb-0">
                <i class="fas fa-users me-2 text-primary"></i>
                Manage Teams
              </h5>
              <button v-if="tournament.total_weeks === 0" class="btn btn-sm btn-outline-primary" @click="openAddTeamModal">
                <font-awesome-icon icon="plus" class="me-1"></font-awesome-icon>
                Add Team
              </button>
            </div>

            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                <tr>
                  <th style="width: 50%;">Team Name</th>
                  <th style="width: 20%; text-align: center">Team Power</th>
                  <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr class="team-row" v-for="(team, index) in teams" :key="index">
                  <td>
                    <div class="d-flex align-items-center">
                      <div
                          class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                          :style="{width: '40px', height: '40px' }"
                      >
                        <span>{{ getTeamInitials(team.name) }}</span>
                      </div>
                      {{ team.name }}
                    </div>
                  </td>

                  <td>
                    <div class="d-flex justify-content-center">
                      {{ team.power }}
                    </div>
                  </td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-outline-secondary" @click="openEditTeamModal(index)">
                      <font-awesome-icon icon="pencil"/>
                    </button>
                    <button v-if="tournament.total_weeks === 0" class="btn btn-sm btn-outline-danger ms-1" @click="confirmDeleteTeam(index)">
                      <font-awesome-icon icon="trash"/>
                    </button>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>

            <div v-if="tournament.total_weeks === 0" class="d-grid gap-2 d-md-flex justify-content-center mt-4">
              <button
                  @click="generateFixtures"
                  class="btn btn-primary"
                  :disabled="teams.length < 2"
              >
                <font-awesome-icon icon="calendar" class="me-2"/> Generate Fixtures
              </button>
            </div>
            <div v-else class="d-grid gap-2 d-md-flex justify-content-center mt-4">
              <a
                  :href="`/fixtures/${tournament.id}`"
                  class="btn btn-primary"
              >
                <font-awesome-icon icon="calendar" class="me-2"/>
                Show Fixtures
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Add Team Modal -->
    <div class="modal fade" ref="addTeamModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Team</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveTeam">
              <div class="mb-3">
                <label for="teamName" class="form-label">Team Name</label>
                <input
                    type="text"
                    class="form-control"
                    id="teamName"
                    v-model="newTeam.name"
                    required
                >
              </div>

              <div class="mb-3">
                <label for="teamPower" class="form-label">Team Power</label>
                <input
                    type="number"
                    min="1"
                    max="100"
                    class="form-control"
                    id="teamPower"
                    v-model="newTeam.power"
                    required
                >
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveTeam">Save Team</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Team Modal -->
    <div class="modal fade" ref="editTeamModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Team</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateTeam">
              <div class="mb-3">
                <label for="teamName" class="form-label">Team Name</label>
                <input
                    type="text"
                    class="form-control"
                    id="teamName"
                    v-model="editTeam.name"
                    required
                >
              </div>

              <div class="mb-3">
                <label for="teamPower" class="form-label">Team Power</label>
                <input
                    type="number"
                    min="1"
                    max="100"
                    class="form-control"
                    id="teamPower"
                    v-model="editTeam.power"
                    required
                >
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="updateTeam">Update Team</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import Swal from 'sweetalert2';
import {Modal} from "bootstrap";
import axios from "axios";
import {handelApiResponseError} from "../../GlobalMethods.js";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {useToast} from "vue-toastification";

const element = document.getElementById('tournament-management');
const tournamentId = element ? element.getAttribute('data-id') : null;
const tournament = ref({});

const toast = useToast();
const teams = ref([]);
const newTeam = ref({name: "", power: 1, tournament_id: tournamentId})
const editTeam = ref({id: "", name: "", power: 1})

const getTournament = () => {
  axios.get(`/api/tournaments/${tournamentId}`).then(({data}) => {
    tournament.value = data.data;
    teams.value = data?.data?.teams ?? [];
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
}

const fixtures = ref([]);
const addTeamModal = ref(null);
const editTeamModal = ref(null);
let addTeamModalInstance = null;
let editTeamModalInstance = null;
onMounted(() => {
  addTeamModalInstance = new Modal(addTeamModal.value);
  editTeamModalInstance = new Modal(editTeamModal.value);
  getTournament()
});

const getTeamInitials = (name) => {
  return name
      .split(' ')
      .map(word => word.charAt(0))
      .join('')
      .substring(0, 2);
};

const openAddTeamModal = () => {
  newTeam.value = {name: "", power: 1, tournament_id: tournamentId};
  addTeamModalInstance.show();
};

const openEditTeamModal = (index) => {
  editTeam.value = {id: teams.value[index]?.id, name: teams.value[index]?.name, power: teams.value[index]?.power}
  editTeamModalInstance.show();
};


const saveTeam = () => {
  axios.post(`/api/teams`, newTeam.value).then(({data}) => {
    toast.success("Team Successfully Added !!")
    addTeamModalInstance.hide();
    getTournament();
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
};

const updateTeam = () => {
  axios.put(`/api/teams/${editTeam.value.id}`, editTeam.value).then(({data}) => {
    toast.success("Tournament Successfully Added !!")
    editTeamModalInstance.hide();
    getTournament();
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
};


const confirmDeleteTeam = (index) => {
  const teamName = teams.value[index].name;

  Swal.fire({
    title: 'Delete Team',
    text: `Are you sure you want to delete ${teamName}?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      teams.value.splice(index, 1);

      Swal.fire(
          'Deleted!',
          `${teamName} has been removed.`,
          'success'
      );

      // Clear fixtures if they exist
      if (fixtures.value.length > 0) {
        fixtures.value = [];

        Swal.fire({
          icon: 'info',
          title: 'Fixtures Cleared',
          text: 'Team changes have cleared the generated fixtures.',
          timer: 2000,
          showConfirmButton: false
        });
      }
    }
  });
};

const generateFixtures = () => {
  axios.post(`/api/tournaments/generate-games/${tournamentId}`).then(({data}) => {
    toast.success("Fixtures Generated !!")
    window.location.href = `/fixtures/${tournamentId}`;
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
};

const generateRoundRobinFixtures = (teamNames) => {
  const teams = [...teamNames];
  const fixtures = [];

  if (teams.length % 2 === 1) {
    teams.push('BYE'); // Add a dummy team if odd number
  }

  const numberOfRounds = teams.length - 1;
  const matchesPerRound = teams.length / 2;

  for (let round = 0; round < numberOfRounds; round++) {
    const roundMatches = [];

    for (let match = 0; match < matchesPerRound; match++) {
      const home = (round + match) % (teams.length - 1);
      const away = (teams.length - 1 - match + round) % (teams.length - 1);

      if (match === 0) {
        roundMatches.push({
          home: teams[teams.length - 1],
          away: teams[away]
        });
      } else {
        roundMatches.push({
          home: teams[home],
          away: teams[away]
        });
      }
    }

    fixtures.push({
      round: round + 1,
      matches: roundMatches.filter(match => match.home !== 'BYE' && match.away !== 'BYE')
    });
  }

  return fixtures;
};

const exportFixtures = () => {
  // Export as JSON
  const fixturesJson = JSON.stringify(fixtures.value, null, 2);
  const blob = new Blob([fixturesJson], {type: 'application/json'});
  const url = URL.createObjectURL(blob);

  const a = document.createElement('a');
  a.href = url;
  a.download = 'tournament_fixtures.json';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);

  Swal.fire({
    icon: 'success',
    title: 'Export Successful',
    text: 'Fixtures have been exported as JSON.',
    timer: 1500,
    showConfirmButton: false
  });
};

const saveFixtures = () => {
  // Simulate saving to server
  Swal.fire({
    title: 'Saving Fixtures',
    html: 'Please wait while we save your fixtures...',
    timer: 1500,
    timerProgressBar: true,
    didOpen: () => {
      Swal.showLoading();
    }
  }).then(() => {
    Swal.fire({
      icon: 'success',
      title: 'Fixtures Saved',
      text: 'Your fixtures have been saved successfully!',
      timer: 1500,
      showConfirmButton: false
    });
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