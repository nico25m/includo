# Progetto IncluDO

Questo progetto è un chatbot che aiuta a scegliere i corsi artigianali di IncluDO. 
Funziona con React per la parte davanti e Laravel per il server.

## Come farlo funzionare

1. **Database**: Crea un database su XAMPP di nome `includo`.
2. **Configurazione**: Apri `back-end/.env` e metti la tua chiave di OpenAI su `OPENAI_API_KEY`.
3. **Installazione Backend**:
   - Vai nella cartella `back-end` col terminale.
   - Digita `composer install`.
   - Digita `php artisan migrate`.
   - Digita `php artisan courses:seed` (carica i dati dei corsi).
   - Digita `php artisan serve` per avviare il server.
4. **Installazione Frontend**:
   - Vai nella cartella `front-end` col terminale.
   - Digita `npm install`.
   - Digita `npm run dev`.

Ora puoi aprire `http://localhost:5173` per provare la chat.

---
Progetto creato per IncluDO.