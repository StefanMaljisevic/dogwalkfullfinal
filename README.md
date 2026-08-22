# Dog Walk - Web programiranje projekat

Web aplikacija za temu **Šetnja pasa**.

## Tehnologije

- HTML5, CSS3, Bootstrap 5
- JavaScript + Fetch API + JSON
- PHP 8 + OOP
- MySQL + PDO
- PHPMailer preko Composer-a
- Responsive dizajn

## Pokretanje lokalno

1. Kopiraj folder projekta u `C:/xampp/htdocs/`.
2. Pokreni Apache i MySQL u XAMPP-u.
3. Importuj `database.sql` kroz phpMyAdmin.
4. Proveri podatke za bazu u `includes/db_config.php`.
5. U terminalu pokreni `composer install` u folderu projekta.
6. Otvori projekat u browseru preko `http://localhost/ime-foldera/`.

## Test nalozi

Svi test nalozi koriste lozinku: `password`.

- Admin: `admin@dogwalk.local`
- Korisnik: `user@dogwalk.local`
- Šetač 1: `walker1@dogwalk.local`
- Šetač 2: `walker2@dogwalk.local`

## Email

PHPMailer je podešen u `includes/MailService.php`, a SMTP podaci se nalaze u `includes/db_config.php`.
Za lokalno testiranje, ako SMTP nije podešen, linkovi za aktivaciju i reset lozinke upisuju se u `logs/mail_log.txt`.

## Napomena za predaju

Trello tabla i Git repozitorijum se prave ručno, jer nisu deo PHP koda.

## Gmail SMTP setup

The project is configured to send mail through Gmail using PHPMailer.

1. Enable 2-Step Verification on the Google account used for sending mail.
2. Create a Google App Password for the project.
3. Open `includes/smtp_config.local.php`.
4. Replace `PASTE_YOUR_16_CHARACTER_APP_PASSWORD_HERE` with the App Password. Spaces are allowed; the application removes them automatically.
5. Run `composer install` in the project directory so PHPMailer is installed.
6. Start Apache and MySQL, import `database.sql`, then test registration or Forgot Password.

The sender account is configured as `markoprobojcevic@gmail.com`. Never commit `includes/smtp_config.local.php` to Git; it is included in `.gitignore`.
