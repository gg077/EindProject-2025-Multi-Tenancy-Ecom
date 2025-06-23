# Multi-Tenancy Digital E-commerce Platform

Dit is een **Laravel-gebaseerd multi-tenant e-commerce platform** dat meerdere onafhankelijke webshops kan hosten onder één applicatie-instance. Het is een SaaS-oplossing die verschillende bedrijven in staat stelt hun eigen online winkel te beheren met volledige isolatie van data en functionaliteit.

---

## ⚙️ Functionaliteiten

### 🧩 Multi-Tenancy: Shared Database met Shared Schema

- Gescheiden omgevingen per tenant (data en functionaliteit)
- Ondersteuning voor subdomeinen per tenant
- Centraal beheer via Super Admin panel

### 🛒 E-commerce

- Productbeheer met categorieën
- Winkelwagen en afrekenproces
- Bestellingen (inclusief digitale levering)
- Reviews & beoordelingen
- Geavanceerde zoek- en filterfuncties

### 💳 Betalingen

- Stripe-integratie (Laravel Cashier)

### 👥 Gebruikersbeheer

- **Role-Based Access Control**: Spatie permissions
- **Multi-role ondersteuning**: Admin, Buyer en Super Admin rollen
- **User impersonation**: TenantSwitch
- And more…

### 🧑‍💼 Beheerder Panelen

- Instellingen per tenant (branding, adres, btw, Stripe)
- Inzicht in producten, bestellingen en gebruikers
- Basisstatistieken en dashboard

---

## 🧰 Stack

| Component | Technologie |
| --- | --- |
| Framework | Laravel 12 |
| Frontend | Livewire, Tailwind CSS |
| Database | MySQL / PostgreSQL |
| Multi-Tenancy | Stancl/Tenancy |
| Betalingen | Stripe + Cashier |
| Rollen & rechten | Spatie Laravel Permission |
| Build tools | Vite |
| Testing | PHPUnit, Pest |

---

## 📦 Systeemvereisten

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+ / PostgreSQL 13+
- Redis (optioneel)

---

## 🚀 Installatie

### 1. Repo clonen

```bash
git clone <repository-url>
```

### 2. Afhankelijkheden installeren

```bash
composer install
npm install
```

### 3. `.env` configureren

```bash
Kopieer .env.example file en maak het .env
php artisan key:generate

Setup Mysql Database & onnect
```

Pas de `.env` aan met je database-, mail- en Stripe-gegevens.

### 4. Migrations uitvoeren

```bash
php artisan migrate --seed
```

### 5. Opslag en assets

```bash
1. Verwijder uploads map in public
2. php artisan storage:link
Composer run dev
npm run dev # of npm run build voor productie

```

---

## 🌍 Domeinconfiguratie

In `config/tenancy.php`:

```php
 'central_domains' => [
        '127.0.0.1',
        'digimarket.be', // verander dit naar de central domein naam = deze naam moet ook in de hostfile staan
    ]
```

Pas dit aan op basis van jouw omgeving.

---

## 🧪 Testen

### HostFile Aanpassen C:\Windows\System32\drivers\etc\hosts

```bash
#
127.0.0.1 localhost
::1 localhost

# Multi-tenancy domeinen
127.0.0.1 digimarket.be
127.0.0.1 tenant1.digimarket.be
127.0.0.1 tenant2.digimarket.be
127.0.0.1 tenant3.digimarket.be
127.0.0.1 larishop.digimarket.be
127.0.0.1 larishop2.digimarket.be
```

`.env` voorbeeld:

```
`DB_CONNECTION=mysql 
DB_HOST=127.0.0.1 
DB_PORT=3306 
DB_DATABASE= 
DB_USERNAME=root 
DB_PASSWORD=`

# Vergeet email setup niet
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls

# Caching probleem? Dit los het op:
CACHE_STORE=array
CACHE_PREFIX=array
```

---

## 🛠 Tenant aanmaken

### 🖥 Via registratiepagina

1. Bezoek: `/tenant/register`
2. Vul gegevens in: winkelnaam, domein, admin info
3. Tenant wordt automatisch aangemaakt en doorgestuurd

---

## 🧾 Orderproces

1. Klant voegt product toe aan winkelmand
2. Checkout & betaling via Stripe
3. Orderbevestiging en (digitale) levering
4. Ordergeschiedenis beschikbaar voor klant en admin
5. PDF factuur beschikbaar

---
