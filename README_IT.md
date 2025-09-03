# Open Day Cyber Security Game 🎯

Un sistema di gioco interattivo per eventi Open Day dedicato alla cybersecurity, sviluppato per coinvolgere i partecipanti in sfide pratiche di sicurezza informatica.

## 🎮 Caratteristiche del Gioco

- **Sistema a squadre**: I partecipanti si registrano con il nome del team
- **Livelli progressivi**: Serie di sfide di cybersecurity con difficoltà crescente
- **Formato CTF**: Le risposte seguono il formato `flag{risposta}`
- **Timer countdown**: Tempo limitato per completare tutte le sfide
- **Bonus completamento**: Punti extra per chi finisce tutti i livelli in tempo
- **Pannello amministratore**: Controllo completo del gioco e visualizzazione risultati

## 🚀 Funzionalità

### Per i Partecipanti
- ✅ Registrazione squadra
- ⏱️ Countdown di attesa per l'inizio del gioco
- 📝 Interfaccia intuitiva per rispondere alle domande
- 🎯 Sistema di punteggio in tempo reale
- 📊 Visualizzazione risultati finali

### Per gli Amministratori
- 🎮 Avvio/stop del gioco
- 📈 Monitoraggio squadre e punteggi in tempo reale
- 📋 Gestione livelli e domande
- 🏆 Visualizzazione classifica finale
- 📥 Download risultati in formato CSV

## 🛠️ Tecnologie Utilizzate

- **Backend**: PHP con sessioni
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Styling**: CSS custom con gradienti e animazioni moderne
- **File Management**: Sistema di upload e download file

## 📁 Struttura del Progetto

```
├── index.php          # Pagina di registrazione squadre
├── attesa.php          # Sala d'attesa con regole del gioco
├── livelli.php         # Lista dei livelli disponibili
├── livello.php         # Interfaccia per rispondere alle domande
├── risultati.php       # Visualizzazione risultati finali
├── admin.php           # Pannello di amministrazione
├── header.php          # Header comune delle pagine
├── stile.css           # Fogli di stile personalizzati
├── password.php        # Configurazione database (escluso da Git)
├── allegati/           # File e risorse per le sfide
│   ├── *.pdf          # Documenti delle sfide
│   ├── *.png          # Immagini e screenshot
│   ├── *.html         # File web per le sfide
│   └── *.csv          # Dati per le sfide
└── immagini/           # Loghi e risorse grafiche
```

## 🔧 Installazione e Configurazione

### Prerequisiti
- Server web con PHP (7.4+)
- MySQL/MariaDB
- Estensioni PHP: mysqli, session, file_uploads

### Setup Database
1. Crea un database MySQL
2. Configura le tabelle per:
   - Squadre registrate
   - Livelli e domande
   - Risposte e punteggi
   - Configurazione di gioco

### Configurazione
1. Clona il repository:
   ```bash
   git clone https://github.com/Andrebxx/Open-Day-Cyber-Its.git
   ```

2. Crea il file `password.php` con la configurazione del database:
   ```php
   <?php
   $servername = "localhost";
   $username = "your_username";
   $password = "your_password";
   $dbname = "your_database";
   
   $myDB = new mysqli($servername, $username, $password, $dbname);
   if ($myDB->connect_error) {
       die("Connessione fallita: " . $myDB->connect_error);
   }
   ?>
   ```

3. Configura il server web per puntare alla directory del progetto
4. Assicurati che la directory `allegati/` abbia i permessi di scrittura

## 🎯 Come Giocare

### Per i Partecipanti
1. **Registrazione**: Inserisci il nome della tua squadra nella homepage
2. **Attesa**: Attendi nella sala d'attesa che l'amministratore avvii il gioco
3. **Gioco**: Risolvi i livelli seguendo il formato `flag{risposta}`
4. **Risultati**: Visualizza il tuo punteggio e la classifica finale

### Per gli Amministratori
1. Accedi al pannello admin (`admin.php`)
2. Configura i livelli e le domande
3. Avvia il gioco quando i partecipanti sono pronti
4. Monitora i progressi in tempo reale
5. Scarica i risultati al termine

## 🎨 Design e UI/UX

- **Design moderno**: Interfaccia pulita con gradienti e ombre
- **Responsive**: Funziona su desktop, tablet e mobile
- **Accessibile**: Colori contrastati e font leggibili
- **Animazioni**: Transizioni fluide e feedback visivi
- **Tema cybersecurity**: Colori e icone coerenti con il tema

## 🔒 Sicurezza

- Sessioni PHP per l'autenticazione
- Sanitizzazione input utente
- File sensibili esclusi dal version control
- Controllo accessi per il pannello admin

## 📊 Tipi di Sfide Supportate

- **OSINT**: Ricerca informazioni open source
- **Crittografia**: Decifraggio e analisi crittografica
- **Web Security**: Vulnerabilità web e analisi codice
- **Forensics**: Analisi file e metadati
- **Steganografia**: Messaggi nascosti in immagini
- **Social Engineering**: Analisi comportamentale

## 🤝 Contribuire

Le contribuzioni sono benvenute! Per contribuire:

1. Fai un fork del progetto
2. Crea un branch per la tua feature (`git checkout -b feature/nuova-funzionalita`)
3. Committare le modifiche (`git commit -m 'Aggiungi nuova funzionalità'`)
4. Push del branch (`git push origin feature/nuova-funzionalita`)
5. Apri una Pull Request

## 📄 Licenza

Questo progetto è rilasciato sotto licenza MIT. Vedi il file `LICENSE` per i dettagli.

## 👨‍💻 Autore

**Andrea Bassi** - [Andrebxx](https://github.com/Andrebxx)
**Leonardo Falconi** - [leofalcoo](https://github.com/leofalcoo)

## 🙏 Ringraziamenti

- Tutti i partecipanti agli Open Day
- La community cybersecurity italiana
- I docenti e gli studenti che hanno testato il sistema

---

*Realizzato con ❤️ per promuovere l'educazione in cybersecurity*
