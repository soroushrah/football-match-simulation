import './bootstrap';
import { createApp } from 'vue';

//Helper Methods
import './GlobalMethods.js';

//Boostrap
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

// Toast
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

//Font Awesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faUser,faUsers, faHome, faFootball, faTrophy, faTable,faPlus,faTrash,faPencil,faCalendar ,faMinus,faFutbol } from '@fortawesome/free-solid-svg-icons';


// Add icons to the library
library.add(
    faUser,faUsers, faHome, faFootball, faTrophy, faTable,faPlus,faTrash,faPencil,faCalendar,faMinus,faFutbol
);


// Import components
import TournamentIndex from './components/tournament/Index.vue';
import TournamentManagement from './components/tournament/Management.vue';
import FixturesComponent from './components/game/FixturesComponent.vue';
import ScoreboardComponent from './components/scoreboard/ScoreboardComponent.vue';


// This Method Help Us to Load Each Component That Related To Laravel Routes
function mountComponent(component, elementId) {
    const el = document.getElementById(elementId);
    if (el) {
        const app = createApp(component);

        // Register globals
        app.use(Toast);

        // Register Font Awesome component globally
        app.component('font-awesome-icon', FontAwesomeIcon);

        app.mount(`#${elementId}`);
    }
}


mountComponent(TournamentIndex, 'tournament-index');
mountComponent(TournamentManagement, 'tournament-management');
mountComponent(FixturesComponent, 'fixtures-app');
mountComponent(ScoreboardComponent, 'scoreboard-app');