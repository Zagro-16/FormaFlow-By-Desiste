# FormaFlow By Desiste
Piattaforma gestionale web per enti di formazione (Admin, Docente, Corsista).

## Requisiti
- PHP 8.1+
- MySQL/MariaDB
- Apache (XAMPP/Laragon) **oppure** server PHP built-in
- Estensioni PHP: `pdo`, `pdo_mysql`

## 1) Configurazione database
1. Crea DB `formaflow`.
2. Importa nell'ordine:
   - `database/schema.sql`
   - `database/seed.sql`
3. Configura credenziali in `config/db.php`.

## 2) Avvio in locale
### Opzione A — XAMPP (consigliata)
1. Copia la cartella progetto in `htdocs/FormaFlow-By-Desiste`.
2. Avvia Apache + MySQL da XAMPP.
3. Apri: `http://localhost/FormaFlow-By-Desiste/`

### Opzione B — PHP built-in server
1. Dalla root del progetto esegui:
   ```bash
   php -S 127.0.0.1:8000
   ```
2. Apri: `http://127.0.0.1:8000/index.php`

> `APP_URL` viene calcolato automaticamente. Se sei su hosting/proxy, puoi forzarlo via variabile ambiente `APP_URL`.

## 3) Login demo
Password per tutti gli utenti demo: **Password123!**
- admin@formaflow.test
- docente@formaflow.test
- corsista@formaflow.test

## 4) Dipendenze opzionali (funzioni avanzate)
Per reminder email e PDF:
```bash
composer require phpmailer/phpmailer dompdf/dompdf
```

## 5) Reminder automatici (cron)
Script: `cron/reminder-cron.php`

Esempio cron Linux (ogni ora):
```cron
0 * * * * /usr/bin/php /percorso/progetto/cron/reminder-cron.php >> /percorso/logs/reminder.log 2>&1
```

## Struttura principale
- `admin/`, `docente/`, `corsista/`: aree per ruolo
- `actions/`: endpoint POST operativi
- `ajax/`: endpoint JSON
- `includes/`: layout condiviso
- `database/`: schema e seed
- `assets/`: CSS/JS frontend
- `uploads/`: materiali e attestati


## Troubleshooting login (errore colonna `full_name`)
Se vedi errore tipo `Unknown column full_name`, significa che nel tuo MySQL esiste già una tabella `users` con schema diverso.

Soluzione consigliata (sviluppo locale):
1. Drop del DB `formaflow`.
2. Reimporta `database/schema.sql` e `database/seed.sql`.

In alternativa, puoi allineare la tabella manualmente con:
```sql
ALTER TABLE users ADD COLUMN full_name VARCHAR(150) NULL;
```


## Troubleshooting redirect `/actions/index.php` Not Found
Se dopo il login vieni reindirizzato a `/actions/index.php`, la causa è un `APP_URL` non allineato o una versione precedente del file `config/config.php`.

Passi rapidi:
1. Aggiorna il progetto all'ultima versione (fix già incluso).
2. Se necessario, forza la base URL con variabile ambiente `APP_URL` (es. `http://localhost/FormaFlow-By-Desiste`).
3. Riavvia Apache/PHP e svuota cache browser.
