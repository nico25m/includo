# Appunti Progetto IncluDO

Questo file spiega come funziona il chatbot e come cambiare le cose principali per il progetto.

## 📁 Struttura
Ci sono due cartelle principali:
- **front-end**: la parte grafica fatta con React.
- **back-end**: il server fatto con Laravel che gestisce i dati e l'AI.

---

## 🎨 Modifiche Grafiche (Front-end)

Tutti gli stili sono nel file `front-end/src/App.css`.

- **Colori e Sfondo**: se vuoi cambiare i colori, cerca le classi nel CSS. Ad esempio, per il colore dell'header cerca `.header` e cambia il `background-color`.
- **Messaggio Iniziale**: se vuoi cambiare quello che dice Indo quando apri la pagina, vai in `front-end/src/components/ChatWindow.jsx` e modifica la variabile `WELCOME_MESSAGE`.
- **Titoli**: il titolo della pagina è in `front-end/src/App.jsx` dentro il tag `<h1>`.

---

## 🤖 Modifiche AI e Dati (Back-end)

### Cambiare come ragiona Indo
Se vuoi che il bot sia più simpatico o faccia domande diverse, apri `back-end/app/Http/Controllers/ChatController.php`. 
In alto c'è la variabile `SYSTEM_PROMPT`. Lì puoi scrivere in italiano le istruzioni per il bot (es: "Chiedi sempre il nome dell'utente all'inizio").

### Aggiungere o cambiare i corsi
I corsi sono scritti nel file `back-end/data/courses.json`. 
1. Apri il file e modifica i testi o aggiungi un nuovo pezzo (copiando uno esistente).
2. Per far leggere i nuovi corsi al database, apri il terminale nella cartella `back-end` e scrivi:
   `php artisan courses:seed`
   (Questo serve a creare gli "embeddings", ovvero la versione dei testi che l'AI può capire per fare i confronti).

### Database e API Key
Nel file `back-end/.env` ci sono le configurazioni:
- `DB_DATABASE=includo`: il nome del database su XAMPP.
- `OPENAI_API_KEY`: la tua chiave per far funzionare l'intelligenza artificiale.

---

## ❓ Problemi comuni
- **Il bot non risponde**: controlla che il server Laravel sia attivo (`php artisan serve`) e che la chiave OpenAI sia corretta nel file .env.
- **I nuovi corsi non appaiono**: ricordati di lanciare sempre il comando `php artisan courses:seed` dopo aver modificato il file JSON.
