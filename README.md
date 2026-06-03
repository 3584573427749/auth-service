# 🔐 Auth Service

Auth Service ansvarar för autentisering, sessionshantering och användarhantering i plattformen.

Tjänsten är en central del av mikrotjänstarkitekturen och används av klienter som Admin UI, Web UI och PWA:er samt av övriga backend‑tjänster via Gateway Service.

## 📌 Översikt

Auth Service hanterar två huvudområden:

1. **Autentisering och sessioner**
2. **Användare, roller och rättigheter**

Tjänsten fungerar som plattformens källa för identitet, inloggning och behörighetsinformation.

## 🔑 Autentisering

Auth Service hanterar:

- Passwordless login
  - Magic link
  - One-time password / engångskod
- Utfärdande av JWT access tokens
- Hantering av refresh tokens via httpOnly cookies
- Token rotation
- Verifiering av inloggad användare
- Utloggning och invalidering av sessioner

### Autentiseringsflöde

1. Användaren begär inloggning med e-postadress.
2. Auth Service skapar en engångskod eller magic link.
3. Användaren verifierar inloggningen.
4. Auth Service utfärdar:
   - JWT access token
   - Refresh token som httpOnly cookie
5. Klienten skickar access token vid skyddade anrop.
6. Access token kan roteras automatiskt vid svar från API:et.

## 👥 Användarhantering

Auth Service ansvarar också för administration av användare i plattformen.

Det omfattar bland annat:

- Skapa användare
- Visa användare
- Lista användare
- Uppdatera användarinformation
- Aktivera/inaktivera användare
- Radera användare (soft delete)
- Koppla användare till roller
- Hantera användarens behörigheter via roller och rättigheter

Användarhanteringen används framför allt av administrativa gränssnitt, men informationen kan även användas av andra tjänster för behörighetskontroll.

## 🛡️ Roller och rättigheter

Auth Service ansvarar för plattformens grundläggande behörighetsmodell.

### Roller

En roll beskriver en uppsättning rättigheter som kan tilldelas en användare.

Exempel på roller kan vara:

- `admin`
- `trainer`
- `official`
- `user`

Exakta roller definieras i plattformens domänmodell och OpenAPI-kontrakt.

### Rättigheter

Rättigheter beskriver vad en användare får göra i systemet.

Exempel på rättigheter kan vara:

- Läsa användare
- Skapa användare
- Uppdatera användare
- Hantera roller
- Administrera tävlingar
- Läsa resultat
- Registrera tider

Roller används för att gruppera rättigheter och göra behörighetsstyrningen enklare att administrera.

### Behörighetskontroll

Auth Service kan användas för att:

- Validera vem användaren är
- Exponera användarens roller och rättigheter
- Tillhandahålla claims i JWT
- Ge andra tjänster underlag för åtkomstkontroll

Övriga mikrotjänster ansvarar för sin egen domänlogik, men kan basera sina behörighetsbeslut på information från Auth Service.

## 🧩 Teknisk kontext

Auth Service följer plattformens gemensamma backend‑stack:

- Slim 4
- PHP-DI
- Doctrine DBAL
- OpenAPI-specifikation
- Docker / Docker Compose
- Traefik
- MariaDB
- GitHub Actions för CI/CD

All kommunikation sker via tydliga API-kontrakt och varje tjänst har ett eget `openapi.yaml`.

## 🚀 Kom igång med lokal utvecklingsmiljö

### 1. Förutsättningar

Installera:

* Git
* Docker
* Docker Compose
* PHP/Composer, om tjänsten även ska köras utanför container
* Node.js, om OpenAPI-generering eller frontend-typer ska köras lokalt

### 2. Klona repo

```bash
git clone <repo-url>
cd auth-service
```

### 3. Skapa lokal miljöfil

Kopiera exempelkonfigurationen:

```bash
cp .env.example .env
```

Kontrollera minst följande inställningar:

```env
APP_ENV=dev
APP_DEBUG=true

DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=auth
DB_USERNAME=auth
DB_PASSWORD=auth

JWT_PRIVATE_KEY_PATH=/app/config/jwt/private.pem
JWT_PUBLIC_KEY_PATH=/app/config/jwt/public.pem
JWT_TTL=900

REFRESH_TOKEN_TTL=1209600
COOKIE_SECURE=false
COOKIE_HTTP_ONLY=true
COOKIE_SAME_SITE=Lax
```

Värdena ovan är exempel och ska anpassas efter aktuell Docker Compose‑miljö.

### 4. Starta tjänsten

Auth Service körs i Docker, men ansluter till en databas som körs direkt på hostmaskinen.

Starta tjänsten med:

```bash
docker compose up --build

### 5. Installera PHP-beroenden

Om beroenden inte installeras automatiskt i Docker-bygget:

```bash
docker compose exec auth-service composer install
```

### 6. Kör databasmigreringar

```bash
docker compose exec auth-service php bin/console migrations:migrate
```

Om projektet inte använder `bin/console`, använd projektets aktuella migreringskommando.

### 7. Skapa JWT-nycklar

Om nycklar inte redan finns lokalt:

```bash
docker compose exec auth-service mkdir -p config/jwt

docker compose exec auth-service openssl genrsa \
  -out config/jwt/private.pem 4096

docker compose exec auth-service openssl rsa \
  -in config/jwt/private.pem \
  -pubout \
  -out config/jwt/public.pem
```

### 8. Åtkomst lokalt

Auth Service exponeras normalt via Gateway Service, exempelvis:

```text
http://localhost/api/auth
```

Exakta URL:er styrs av Docker Compose och Traefik-konfigurationen.

## 📄 OpenAPI

Auth Service ska ha ett eget OpenAPI-kontrakt:

```text
openapi.yaml
```

OpenAPI-kontraktet beskriver bland annat:

* Inloggning
* Token-refresh
* Utloggning
* Verifiering av aktuell användare
* CRUD för användare
* Hantering av roller
* Hantering av rättigheter
* Koppling mellan användare och roller

All utveckling ska utgå från API-kontraktet.

## 🔁 Utvecklingsflöde

Rekommenderat arbetsflöde:

1. Uppdatera `openapi.yaml`
2. Validera kontraktet
3. Implementera endpoint
4. Skriv tester
5. Kör lokal verifiering
6. Skapa pull request

Eftersom plattformen är kontraktsstyrd ska OpenAPI uppdateras innan implementationen ändras.

## 🧪 Test och verifiering

### Begär inloggning

```bash
curl -X POST http://localhost/api/auth/login/request \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com"}'
```

### Verifiera inloggning

```bash
curl -X POST http://localhost/api/auth/login/verify \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","code":"123456"}'
```

### Hämta aktuell användare

```bash
curl http://localhost/api/auth/me \
  -H "Authorization: Bearer <access-token>"
```

### Lista användare

```bash
curl http://localhost/api/auth/users \
  -H "Authorization: Bearer <access-token>"
```

### Skapa användare

```bash
curl -X POST http://localhost/api/auth/users \
  -H "Authorization: Bearer <access-token>" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "new.user@example.com",
    "displayName": "New User",
    "roles": ["user"]
  }'
```

Endpointnamnen ovan är exempel och ska stämmas av mot `openapi.yaml`.

## 🔒 Säkerhet

Auth Service ska hantera säkerhetskritisk logik på ett konsekvent sätt.

Viktiga principer:

* Passwordless login, inga användarlösenord lagras
* JWT används för kortlivade access tokens
* Refresh tokens lagras som httpOnly cookies
* Access tokens roteras vid behov
* Roller och rättigheter hanteras centralt
* Endast behöriga användare får administrera användare, roller och rättigheter
* Domäntjänster ska inte själva skapa identiteter
* Känsliga värden ska ligga i miljövariabler eller secrets, inte i källkod

## 🧱 Ansvarsgränser

Auth Service ansvarar för:

* Identitet
* Autentisering
* Sessioner
* Användare
* Roller
* Rättigheter
* Behörighetsinformation i tokens/claims

Auth Service ansvarar inte för:

* Resultat
* Grupphantering
* Tidsredovisning/uppföljning
* Domänspecifika regler i andra tjänster

Dessa hanteras av respektive mikrotjänst.

## 📌 Sammanfattning

Auth Service är plattformens centrala tjänst för identitet och behörighet.

Tjänsten hanterar:

* Passwordless inloggning
* JWT access tokens
* Refresh tokens
* Sessionshantering
* Användaradministration
* Roller och rättigheter
* Underlag för behörighetskontroll i övriga tjänster

Målet är att samla autentisering och behörighetsinformation på ett ställe, samtidigt som övriga mikrotjänster förblir ansvariga för sin egen domänlogik.

