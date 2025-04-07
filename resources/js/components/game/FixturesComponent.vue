<template>
  <div class="container py-4">

    <div class="row mb-4">
      <div class="col-12">
        <h1 class="page-header">Fixtures</h1>
      </div>
    </div>

    <div class="row">
      <div v-for="week in weeks" class="col-6 col-md-4">
        <div class="card mb-4">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                <tr>
                  <th>Week {{ week.week }}</th>
                </tr>
                </thead>
                <tbody>
                <tr class="team-row" v-for="game in week.games">
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
            </div>
          </div>
        </div>
      </div>

      <div v-if="weeks.length > 0" class="d-grid gap-2 d-md-flex justify-content-center mt-4">
        <a :href="`/scoreboard/${tournamentId}`" class="btn btn-primary">
          <i class="fas fa-calendar-alt me-2"></i> Start Simulation
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import axios from "axios";
import {handelApiResponseError} from "../../GlobalMethods.js";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const weeks = ref([]);
const element = document.getElementById('fixtures-app');
const tournamentId = element ? element.getAttribute('data-id') : null;

const getFixtures = () => {
  axios.get(`/api/games/get-by-tournament/${tournamentId}?group_by=week`).then(({data}) => {
    weeks.value = data?.data ?? [];
    console.log(weeks.value);
  }).catch(({response}) => {
    handelApiResponseError(response);
  })
}

onMounted(() => {
  getFixtures();
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
</style>