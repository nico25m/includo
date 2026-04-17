# IncluDO: L'Artigianato che Include il Futuro

Benvenuti nel progetto **IncluDO**! Questo è un chatbot innovativo progettato per orientare le persone verso corsi di artigianato tradizionale, con un focus sull'inclusione sociale.

## L'Idea Dietro IncluDO

IncluDO nasce dall'osservazione dei fondatori, marito e moglie, che hanno visto i loro paesi svuotarsi e le botteghe storiche chiudere. Le poche attività artigianali rimaste faticano a trovare giovani disposti a imparare questi mestieri, mettendo a rischio la loro sopravvivenza.

Il progetto si propone di preservare e trasmettere queste competenze uniche, offrendo al contempo opportunità di formazione e inserimento lavorativo a chi ne ha più bisogno, come migranti e persone in riabilitazione sociale.

## Visione

🤝 Diventare un punto di riferimento nella formazione di mestieri tradizionali, rendendoli accessibili a migranti e persone in riabilitazione sociale.

## Missione

Creare percorsi di formazione per includere nel mondo del lavoro migranti e persone svantaggiate, contribuendo a salvaguardare mestieri artigianali destinati a scomparire.

## Chi Siamo

I fondatori provengono dal mondo del no-profit e hanno unito le loro competenze per dare vita a un progetto che coniuga inclusione sociale e tutela delle tradizioni locali.

## Come Funziona

IncluDO organizza percorsi professionali gratuiti, sviluppati in collaborazione con artigiani locali che insegnano il proprio mestiere alle nuove generazioni.

I percorsi sono finanziati in parte attraverso fondi europei e regionali per il ripopolamento dei borghi e in parte attraverso sponsorizzazioni di aziende locali. Per supportare il loro percorso, gli allievi organizzano workshop per turisti, dove possono mettere in pratica quanto appreso e contribuire in parte al finanziamento della propria formazione.

## Slogan

**L’artigianato che include il futuro.**

---

## Stack Tecnologico

*   **Frontend**: React (con Vite)
*   **Backend**: Laravel
*   **AI**: OpenAI API

## Come Avviare il Progetto in Locale

Segui questi passaggi per configurare e avviare il progetto sul tuo ambiente locale.

### 1. Database

Crea un database MySQL (ad esempio con XAMPP) di nome `includo`.

### 2. Configurazione Backend (Laravel)

1.  Naviga nella cartella `back-end` tramite il terminale.
2.  Installa le dipendenze di Composer: `composer install`.
3.  Copia il file `.env.example` in `.env`: `cp .env.example .env`.
4.  Genera la chiave dell'applicazione: `php artisan key:generate`.
5.  Aggiorna il file `.env` con la tua chiave OpenAI: `OPENAI_API_KEY=la_tua_chiave_openai`.
6.  Esegui le migrazioni del database: `php artisan migrate`.
7.  Carica i dati dei corsi: `php artisan courses:seed`.
8.  Avvia il server Laravel: `php artisan serve`.

### 3. Configurazione Frontend (React)

1.  Naviga nella cartella `front-end` tramite il terminale.
2.  Installa le dipendenze di Node.js: `npm install`.
3.  Avvia il server di sviluppo React: `npm run dev`.

Ora puoi aprire `http://localhost:5173` nel tuo browser per interagire con la chat.

---


