<template>
  <div class="container py-4" v-if="tournament">

    <div class="row mb-4">
      <div class="col-12">
        <h1 class="page-header">Simulation</h1>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-md-8">
        <div class="card mb-4">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                <tr>
                  <th style="width: 40%">Team Name</th>
                  <th>P</th>
                  <th>W</th>
                  <th>D</th>
                  <th>L</th>
                  <th>GD</th>
                </tr>
                </thead>
                <tbody>
                <tr class="team-row" v-for="(scoreboard,index) in scoreboards">
                  <td class=" py-3">
                    <template v-if="tournament.completed ">
                      <font-awesome-icon v-if="index === 0" icon="trophy" class="me-1 text-gold"/>
                      <font-awesome-icon v-if="index === 1" icon="trophy" class="me-1 text-silver"/>
                      <font-awesome-icon v-if="index === 2" icon="trophy" class="me-1 text-bronze"/>
                    </template>

                    {{ index + 1 }} - {{ scoreboard.team?.name }}
                  </td>
                  <td class="py-3">
                    {{ scoreboard.points }}
                  </td>
                  <td class="py-3">
                    {{ scoreboard.game_win }}
                  </td>
                  <td class="py-3">
                    {{ scoreboard.game_draw }}
                  </td>
                  <td class="py-3">
                    {{ scoreboard.game_lose }}
                  </td>
                  <td class="py-3">
                    {{ scoreboard.goals_different }}
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div v-if="tournament" class="col-12">
          <div class="card mb-4">
            <div class="card-body">
              <div class="table-responsive">
                <template v-if="tournament.completed">
                  <table class="table table-hover">
                    <thead>
                    <tr>
                      <th>Week -</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </template>
                <template v-else>
                  <table class="table table-hover">
                    <thead>
                    <tr>
                      <th>Week {{ tournament.current_week }} ( Next Week Fixture)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="team-row" v-for="game in currentWeek">
                      <td class="d-flex justify-content-between py-3">
                        <div class="home col-5">
                          {{ game.home_team.name }}
                        </div>

                        <div class="col-1 text-center">
                          <font-awesome-icon icon="minus"/>
                        </div>

                        <div class="away col-5" style="text-align: right">
                          {{ game.away_team.name }}
                        </div>
                      </td>
                    </tr>

                    </tbody>
                  </table>
                </template>
              </div>
            </div>
          </div>
        </div>

        <div v-if="tournament" class="col-12">
          <div class="card mb-4">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                  <tr>
                    <th>Champion Prediction</th>
                    <th>%</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr class="team-row" v-for="(scoreboard,index) in scoreboards">
                    <td class="">
                      {{ scoreboard.team.name }}
                    </td>

                    <td class="">
                      {{ scoreboard.champion_prediction }}
                    </td>
                  </tr>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 d-flex justify-content-between">
        <button class="btn  btn-primary" @click="playAllWeek" v-if="!tournament.completed">
          <font-awesome-icon icon="trophy" class="me-1"/>
          Play All Weeks
        </button>
        <button class="btn  btn-secondary" @click="playNextWeek" v-if="!tournament.completed">
          <font-awesome-icon icon="futbol" class="me-1"/>
          Play Next Week
        </button>

        <button class="btn  btn-danger" @click="reGenerateFixtures">
          <font-awesome-icon icon="trash" class="me-1"/>
          Reset Data
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import axios from "axios";
import {handelApiResponseError} from "../../GlobalMethods.js";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {useToast} from "vue-toastification";

const scoreboards = ref([]);
const currentWeek = ref();
const element = document.getElementById('scoreboard-app');
const tournamentId = element ? element.getAttribute('data-id') : null;
const tournament = ref();
const toast = useToast()
const getTournament = () => {
  axios.get(`/api/tournaments/${tournamentId}`).then(({data}) => {
    tournament.value = data?.data ?? [];
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
}
const getScoreboards = () => {
  axios.get(`/api/scoreboards/get-by-tournament/${tournamentId}`).then(({data}) => {
    scoreboards.value = data?.data ?? [];
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
}

const getCurrentWeek = () => {
  axios.get(`/api/games/get-tournament-current-week/${tournamentId}`).then(({data}) => {
    currentWeek.value = data?.data ?? {};
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
}

const playNextWeek = () => {
  axios.post(`/api/games/play-games-of-current-week/${tournamentId}`).then(({data}) => {
    reloadPage();
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
}

const playAllWeek = () => {
  axios.post(`/api/games/play-all-games-of-tournament/${tournamentId}`).then(({data}) => {
    reloadPage();
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
}

const reGenerateFixtures = () => {
  axios.post(`/api/tournaments/re-generate-games/${tournamentId}`).then(({data}) => {
    toast.success("All Data Re Generated !!")
    reloadPage();
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
};


const reloadPage = () => {
  getTournament();
  getScoreboards();
  getCurrentWeek();
}

onMounted(() => {
  reloadPage();
});


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

.text-gold {
  color: #FFD700
}

.text-silver {
  color: #C0C0C0
}

.text-bronze {
  color: #CD7F32
}

</style>