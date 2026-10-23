
# Beriya Travels

## Run locally

The Laravel API uses SQLite by default, so a MySQL server is not required. From the `backend` directory:

```powershell
Copy-Item .env.example .env
New-Item -ItemType File -Path database/database.sqlite -Force
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

In another terminal, run the Angular frontend if you are working on it:

```powershell
cd frontend
npm install
npm start
```

The standalone site file is at the workspace root as `website v1.html`. Run `node serve-beriya-static.cjs` from the workspace root to preview it at `http://localhost:5500/`; its API requests use `http://localhost:8000/api`.

## Free deployment

The repository includes a Render Docker blueprint. It serves the standalone site and Laravel API from the same origin, and uses Neon Postgres for persistent database storage. This avoids putting booking and contact data in Render's temporary filesystem.

1. Create a **private** GitHub repository containing this workspace's `Dockerfile`, `render.yaml`, `.dockerignore`, `deploy/`, `website v1.html`, and `beriya-travels/`.
2. Create a free Neon Postgres project and copy its connection string. Keep the password private.
3. In Render, create a Blueprint from the private repository and set the service's `DB_URL` secret to the Neon connection string (including `sslmode=require`).
4. Deploy. The Laravel entrypoint applies migrations and seeds the initial destinations, offers, and departures once. The Render service URL serves both the website and `/api`.

Free Render web services can sleep when idle and have an ephemeral filesystem, so the first request after inactivity may take a while. The Neon database is separate and keeps application data across service restarts. Review each provider's current free-plan limits before launch.
