# Projektna dokumentacija - Dog Walk

Dog Walk je web aplikacija za pronalaženje i kontaktiranje šetača pasa. Aplikacija razlikuje četiri nivoa pristupa: gost, registrovani korisnik, registrovani korisnik-šetač i administrator.

## Uloge

Gost može da pregleda početnu stranicu, pretražuje šetače i izvrši registraciju.

Registrovani korisnik može da menja profilne podatke, promeni lozinku, zatraži reset lozinke, kontaktira šetača i oceni svakog šetača najviše jednom.

Registrovani šetač može da uređuje svoj oglas, postavi ili obriše fotografiju, menja dostupnost i pregleda zahteve korisnika za šetnju. Šetač može da radi sa oglasom tek kada ga administrator odobri.

Administrator može da pregleda korisnike, blokira ili odblokira korisnike, pregleda šetače i odobri ili deaktivira šetače.

## Baza podataka

Baza se zove `dog_walk`. Glavne tabele su:

- `users`
- `walkers`
- `ratings`
- `contacts`

Email adresa je jedinstvena i koristi se kao korisničko ime.

## Bezbednost

Lozinke se čuvaju pomoću bcrypt hash algoritma. Za rad sa bazom koristi se PDO i prepared statements. Zaštićene stranice koriste sesije i proveru uloge korisnika.

## Email funkcionalnosti

Aktivacija naloga i reset lozinke rade preko tokena i email linkova. PHPMailer se instalira preko Composer-a. Ako SMTP nije podešen, linkovi se za lokalno testiranje upisuju u `logs/mail_log.txt`.

## AJAX / Fetch

Fetch API i JSON koriste se kod registracije, prijave i ocenjivanja šetača. Ti zahtevi komuniciraju sa bazom podataka.

## Validacija

Validacija postoji na klijentskoj strani kroz HTML/JavaScript i na serverskoj strani kroz PHP metode i provere.

## Dodatna funkcionalnost

Dodata je stranica na kojoj šetač može da prihvati ili odbije zahteve za šetnju.

