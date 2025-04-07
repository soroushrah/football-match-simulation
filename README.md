# Football Simulation App

This is a football simulation app built with Laravel, designed to simulate football matches and display results through a web interface.

## 🚀 Getting Started

### Prerequisites

Make sure you have the following installed:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

### 🛠️ Installation

 1 - Clone the repository:
   ```bash
   git clone git@github.com:soroushrah/football-match-simulation.git
   cd football-match-simulation
   ```
 2 - Build and start the containers:
   
   ```bash
   docker-compose up --build
   ```

 3 - Run database migrations:
  ```bash
   docker exec -it football-match-app php artisan migrate --force
  ```
  4 - Access the web app at: http://localhost:9090

### 🚀 Running Tests
  ```bash
   docker exec -it football-match-app php artisan test
  ```
