# FormaFlow By Desiste
Piattaforma gestionale web per enti di formazione (admin, docenti, corsisti).

## Stack
- PHP 8+
- MySQL/MariaDB
- HTML5, CSS3, JavaScript vanilla
- Bootstrap 5

## Avvio rapido
1. Crea database e importa:
   - `database/schema.sql`
   - `database/seed.sql`
2. Configura `config/db.php`.
3. Avvia con XAMPP/Apache puntando alla cartella progetto.
4. Login demo (password: `password`):
   - admin@formaflow.test
   - docente@formaflow.test
   - corsista@formaflow.test

## Cron reminder
Configura un cron job su `cron/reminder-cron.php` (es. ogni ora).

## Struttura principale
- `admin/`, `docente/`, `corsista/` aree ruolo
- `actions/` endpoint POST
- `ajax/` endpoint JSON
- `includes/` layout condiviso
- `database/` schema e seed
