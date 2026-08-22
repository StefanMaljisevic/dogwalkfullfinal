# Kontrolna lista projekta

- Baza se zove `dog_walk`.
- Koristi se PDO za rad sa MySQL bazom.
- Lozinke se čuvaju pomoću bcrypt hash algoritma.
- Registracija koristi jedinstvenu email adresu i aktivacioni token.
- Reset lozinke koristi token sa vremenskim ograničenjem.
- Gost može da pregleda sajt i pretražuje šetače.
- Korisnik može da menja profil, promeni lozinku, kontaktira šetača i oceni ga jednom.
- Šetač može da uređuje oglas, fotografiju i zahteve tek nakon admin odobrenja.
- Administrator može da blokira korisnike i odobri/deaktivira šetače.
- Početna strana prikazuje 5 najbolje ocenjenih i 5 najaktivnijih šetača.
- Fetch API + JSON se koriste za registraciju, prijavu i ocenjivanje.
- CSS i JavaScript su izdvojeni u posebne fajlove.
- Bootstrap 5 se koristi za responsive prikaz.
- PHPMailer se koristi za email funkcionalnosti preko Composer-a.
