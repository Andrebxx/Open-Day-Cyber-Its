# Open Day Cyber Security Game 🎯

An interactive gaming system for Open Day events dedicated to cybersecurity, designed to engage participants in hands-on information security challenges.

## 🎮 Game Features

- **Team-based system**: Participants register with their team name
- **Progressive levels**: Series of cybersecurity challenges with increasing difficulty
- **CTF format**: Answers follow the `flag{answer}` format
- **Countdown timer**: Limited time to complete all challenges
- **Completion bonus**: Extra points for finishing all levels on time
- **Admin panel**: Complete game control and results visualization

## 🚀 Functionality

### For Participants
- ✅ Team registration
- ⏱️ Waiting countdown for game start
- 📝 Intuitive interface to answer questions
- 🎯 Real-time scoring system
- 📊 Final results visualization

### For Administrators
- 🎮 Start/stop game control
- 📈 Real-time teams and scores monitoring
- 📋 Levels and questions management
- 🏆 Final leaderboard visualization
- 📥 Results download in CSV format

## 🛠️ Technologies Used

- **Backend**: PHP with sessions
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Styling**: Custom CSS with gradients and modern animations
- **File Management**: Upload and download file system

## 📁 Project Structure

```
├── index.php          # Team registration page
├── attesa.php          # Waiting room with game rules
├── livelli.php         # Available levels list
├── livello.php         # Interface to answer questions
├── risultati.php       # Final results visualization
├── admin.php           # Administration panel
├── header.php          # Common page header
├── stile.css           # Custom stylesheets
├── password.php        # Database configuration (excluded from Git)
├── allegati/           # Files and resources for challenges
│   ├── *.pdf          # Challenge documents
│   ├── *.png          # Images and screenshots
│   ├── *.html         # Web files for challenges
│   └── *.csv          # Data for challenges
└── immagini/           # Logos and graphic resources
```

## 🔧 Installation and Configuration

### Prerequisites
- Web server with PHP (7.4+)
- MySQL/MariaDB
- PHP extensions: mysqli, session, file_uploads

### Database Setup
1. Create a MySQL database
2. Configure tables for:
   - Registered teams
   - Levels and questions
   - Answers and scores
   - Game configuration

### Configuration
1. Clone the repository:
   ```bash
   git clone https://github.com/Andrebxx/Open-Day-Cyber-Its.git
   ```

2. Create the `password.php` file with database configuration:
   ```php
   <?php
   $servername = "localhost";
   $username = "your_username";
   $password = "your_password";
   $dbname = "your_database";
   
   $myDB = new mysqli($servername, $username, $password, $dbname);
   if ($myDB->connect_error) {
       die("Connection failed: " . $myDB->connect_error);
   }
   ?>
   ```

3. Configure the web server to point to the project directory
4. Ensure the `allegati/` directory has write permissions

## 🎯 How to Play

### For Participants
1. **Registration**: Enter your team name on the homepage
2. **Waiting**: Wait in the waiting room for the administrator to start the game
3. **Game**: Solve levels following the `flag{answer}` format
4. **Results**: View your score and final leaderboard

### For Administrators
1. Access the admin panel (`admin.php`)
2. Configure levels and questions
3. Start the game when participants are ready
4. Monitor progress in real-time
5. Download results at the end

## 🎨 Design and UI/UX

- **Modern design**: Clean interface with gradients and shadows
- **Responsive**: Works on desktop, tablet, and mobile
- **Accessible**: Contrasted colors and readable fonts
- **Animations**: Smooth transitions and visual feedback
- **Cybersecurity theme**: Colors and icons consistent with the theme

## 🔒 Security

- PHP sessions for authentication
- User input sanitization
- Sensitive files excluded from version control
- Access control for admin panel

## 📊 Supported Challenge Types

- **OSINT**: Open source intelligence gathering
- **Cryptography**: Decryption and cryptographic analysis
- **Web Security**: Web vulnerabilities and code analysis
- **Forensics**: File and metadata analysis
- **Steganography**: Hidden messages in images
- **Social Engineering**: Behavioral analysis

## 🌍 Language Support

- **English**: README.md (this file)
- **Italian**: README_IT.md
- **Interface**: Currently in Italian (can be localized)

## 🤝 Contributing

Contributions are welcome! To contribute:

1. Fork the project
2. Create a branch for your feature (`git checkout -b feature/new-functionality`)
3. Commit your changes (`git commit -m 'Add new functionality'`)
4. Push the branch (`git push origin feature/new-functionality`)
5. Open a Pull Request

## 📄 License

This project is released under the MIT license. See the `LICENSE` file for details.

## 👨‍💻 Authors

**Andrea Bassi** - [Andrebxx](https://github.com/Andrebxx)  
**Leonardo Falconi** - [leofalcoo](https://github.com/leofalcoo)

## 🙏 Acknowledgments

- All Open Day participants
- Italian cybersecurity community
- Teachers and students who tested the system

---

*Made with ❤️ to promote cybersecurity education*
